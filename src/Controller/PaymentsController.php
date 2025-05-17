<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Core\Configure;
use Cake\Http\Client;
use Cake\Log\Log;
use App\Controller\AppController;
use App\Model\Table\BookingsTable;
use App\Model\Table\PaymentHistoriesTable;
use Cake\I18n\FrozenTime;
use App\Mailer\InvoiceMailer;
use Cake\Routing\Router;
use Mpdf\Mpdf;
use Cake\View\View;

class PaymentsController extends AppController
{
    protected BookingsTable $Bookings;
    protected PaymentHistoriesTable $PaymentHistories;

    public function initialize(): void
    {
        parent::initialize();
        $this->Bookings = $this->getTableLocator()->get('Bookings');
        $this->PaymentHistories = $this->getTableLocator()->get('PaymentHistories');

        if (!$this->components()->has('Authentication')) {
            $this->loadComponent('Authentication.Authentication');
        }
        $this->Authentication->allowUnauthenticated(['success', 'cancel', 'processGuestPayment', 'successGuest', 'cancelGuest']);
    }

    public function createOrder($bookingId)
    {
        $this->request->allowMethod(['post']);

        try {
            $booking = $this->Bookings->get($bookingId);
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            Log::error("Booking not found for ID: {$bookingId}. Error: " . $e->getMessage(), ['scope' => ['paypal']]);
            return $this->response
                ->withStatus(404)
                ->withType('application/json')
                ->withStringBody(json_encode(['error' => 'Booking not found.']));
        }

        $clientId = Configure::read('PayPal.clientId');
        $clientSecret = Configure::read('PayPal.clientSecret');
        $mode = Configure::read('PayPal.mode', 'sandbox');

        if (empty($clientId) || empty($clientSecret)) {
            Log::error("PayPal client ID or secret is not configured.", ['scope' => ['paypal']]);
            return $this->response->withStatus(500)->withType('application/json')->withStringBody(json_encode(['error' => 'PayPal configuration error.']));
        }

        // Get PayPal access token
        $http = new Client();
        $tokenUrl = "https://api-m." . ($mode === 'sandbox' ? 'sandbox.' : '') . "paypal.com/v1/oauth2/token";

        $response = $http->post(
            $tokenUrl,
            ['grant_type' => 'client_credentials'],
            [
                'auth' => ['user' => $clientId, 'pass' => $clientSecret],
                'headers' => ['Content-Type' => 'application/x-www-form-urlencoded']
            ]
        );

        if ($response->isOk()) {
            $tokenData = $response->getJson();
            $token = $tokenData['access_token'] ?? null;

            if (empty($token)) {
                Log::error("Extracted PayPal Access Token is empty. Full token response: " . json_encode($tokenData), ['scope' => ['paypal']]);
                return $this->response->withStatus(500)->withType('application/json')->withStringBody(json_encode(['error' => 'Failed to retrieve PayPal token.']));
            }

            // Create order
            $orderAmount = number_format($booking->total_cost, 2, '.', '');
            if ($booking->total_cost <= 0) {
                Log::error("Cannot create PayPal order for booking ID {$bookingId} with zero or negative amount: {$booking->total_cost}", ['scope' => ['paypal']]);
                return $this->response->withStatus(400)->withType('application/json')->withStringBody(json_encode(['error' => 'Invalid amount for payment.']));
            }

            $orderData = [
                'intent' => 'CAPTURE',
                'application_context' => [
                    'return_url' => Router::url(['controller' => 'Payments', 'action' => 'success', $booking->id], true),
                    'cancel_url' => Router::url(['controller' => 'Payments', 'action' => 'cancel', $booking->id], true),
                    'locale' => 'en-AU',
                    'shipping_preference' => 'NO_SHIPPING',
                    'user_action' => 'PAY_NOW',
                ],
                'payer' => [
                    'address' => [
                        'country_code' => 'AU'
                    ]
                ],
                'purchase_units' => [
                    [
                        'amount' => [
                            'currency_code' => 'AUD',
                            'value' => $orderAmount
                        ],
                        'description' => 'Payment for booking #' . $booking->id,
                        'custom_id' => 'BOOKING_ID_' . $booking->id
                    ]
                ],
            ];

            // Create order
            $orderApiUrl = "https://api-m." . ($mode === 'sandbox' ? 'sandbox.' : '') . "paypal.com/v2/checkout/orders";

            $orderResponse = $http->post(
                $orderApiUrl,
                json_encode($orderData),
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $token,
                        'Content-Type' => 'application/json',
                    ]
                ]
            );

            if ($orderResponse->isOk()) {
                return $this->response
                    ->withType('application/json')
                    ->withStringBody($orderResponse->getStringBody());
            } else {
                Log::error("Failed to create PayPal Order for booking ID: {$bookingId}. Status: " . $orderResponse->getStatusCode() . ". Response: " . $orderResponse->getStringBody(), ['scope' => ['paypal']]);
                return $this->response
                    ->withStatus($orderResponse->getStatusCode())
                    ->withType('application/json')
                    ->withStringBody(json_encode(['error' => 'Failed to create PayPal order.', 'paypal_response' => $orderResponse->getJson() ?? $orderResponse->getStringBody()]));
            }
        } else {
            Log::error("Failed to get PayPal Access Token. Status: " . $response->getStatusCode() . ". Response: " . $response->getStringBody(), ['scope' => ['paypal']]);
            return $this->response
                ->withStatus(500)
                ->withType('application/json')
                ->withStringBody(json_encode(['error' => 'Failed to obtain PayPal access token.', 'paypal_response' => $response->getJson() ?? $response->getStringBody()]));
        }

        // Fallback generic error
        return $this->response
            ->withStatus(500)
            ->withType('application/json')
            ->withStringBody(json_encode(['error' => 'Generic failure in create order process.']));
    }

    public function captureOrder($bookingId)
    {
        $this->request->allowMethod(['post']);
        $data = $this->request->getData();
        $orderID = $data['orderID'];

        $clientId = Configure::read('PayPal.clientId');
        $clientSecret = Configure::read('PayPal.clientSecret');
        $mode = Configure::read('PayPal.mode', 'sandbox');

        // Get PayPal access token
        $http = new Client();
        $response = $http->post(
            "https://api-m." . ($mode === 'sandbox' ? 'sandbox.' : '') . "paypal.com/v1/oauth2/token",
            [
                'grant_type' => 'client_credentials'
            ],
            [
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode($clientId . ':' . $clientSecret),
                    'Content-Type' => 'application/x-www-form-urlencoded'
                ]
            ]
        );

        if ($response->isOk()) {
            $token = $response->getJson()['access_token'];

            // Capture order
            $captureResponse = $http->post(
                "https://api-m." . ($mode === 'sandbox' ? 'sandbox.' : '') . "paypal.com/v2/checkout/orders/{$orderID}/capture",
                null,
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $token,
                        'Content-Type' => 'application/json'
                    ]
                ]
            );

            if ($captureResponse->isOk()) {
                $captureData = $captureResponse->getJson();

                // Update booking status
                $booking = $this->Bookings->get($bookingId);
                $booking->remaining_cost = 0;
                $booking->status = 'Confirmed - Paid';
                $this->Bookings->save($booking);

                // Attempt to find existing 'Pending' PaymentHistory
                $paymentHistory = $this->PaymentHistories->find()
                    ->where(['booking_id' => $bookingId, 'payment_status' => 'Pending'])
                    ->orderByDesc('payment_date')
                    ->first();

                if (!$paymentHistory) {
                    Log::warning("[PaymentsController.captureOrder] No 'Pending' PaymentHistory found for Booking ID: {$bookingId}. Creating a new one as fallback.");
                    $paymentHistory = $this->PaymentHistories->newEmptyEntity();
                }

                $captureDetails = $captureData['purchase_units'][0]['payments']['captures'][0] ?? null;
                $paypalApiStatus = strtolower($captureData['status'] ?? 'unknown');
                $internalPaymentStatus = ($paypalApiStatus === 'completed') ? 'Completed' : $paypalApiStatus;

                $paymentHistoryData = [
                    'booking_id' => $bookingId,
                    'customer_id' => $booking->customer_id,
                    'paypal_transaction_id' => $captureData['id'] ?? $orderID,
                    'paypal_payer_id' => $captureData['payer']['payer_id'] ?? null,
                    'payment_amount' => $captureDetails ? ($captureDetails['amount']['value'] ?? $booking->total_cost) : $booking->total_cost,
                    'payment_currency' => $captureDetails ? ($captureDetails['amount']['currency_code'] ?? 'AUD') : 'AUD',
                    'payment_status' => $internalPaymentStatus,
                    'payment_method' => 'paypal',
                    'payment_date' => isset($captureDetails['create_time'])
                                        ? \Cake\I18n\FrozenTime::parse($captureDetails['create_time'])->format('Y-m-d H:i:s')
                                        : FrozenTime::now()->format('Y-m-d H:i:s'),
                    'notes' => 'Payment captured via server-side API call. PayPal Order ID: ' . $orderID
                ];

                $paymentHistory = $this->PaymentHistories->patchEntity($paymentHistory, $paymentHistoryData);

                if (!$this->PaymentHistories->save($paymentHistory)) {
                    Log::error('Failed to save PaymentHistory in CAPTURE_ORDER.');
                    Log::error('Validation errors after save attempt: ' . json_encode($paymentHistory->getErrors()));
                    Log::error('Failed to save payment history for booking ID: ' . $bookingId . ' Errors: ' . json_encode($paymentHistory->getErrors()));
                } else {
                    Log::debug('PaymentHistory SAVED successfully in CAPTURE_ORDER. ID: ' . $paymentHistory->id);
                }

                return $this->response
                    ->withType('application/json')
                    ->withStringBody($captureResponse->getBody());
            }
        }

        return $this->response
            ->withStatus(500)
            ->withType('application/json')
            ->withStringBody(json_encode(['error' => 'Failed to capture order']));
    }

    public function success($bookingId)
    {
        try {
            $booking = $this->Bookings->get($bookingId);

            if ($booking) {
                // Update Booking
                $booking->status = 'Confirmed - Paid';
                $booking->remaining_cost = 0;

                if ($this->Bookings->save($booking)) {
                    // Attempt to find existing 'Pending' PaymentHistory
                    $paymentHistory = $this->PaymentHistories->find()
                        ->where(['booking_id' => $bookingId, 'payment_status' => 'Pending'])
                        ->orderByDesc('payment_date')
                        ->first();

                    if (!$paymentHistory) {
                        Log::warning("[PaymentsController.success] No 'Pending' PaymentHistory found for Booking ID: {$bookingId}. Creating a new one as fallback.");
                        $paymentHistory = $this->PaymentHistories->newEmptyEntity();
                    }

                    // Attempt to get PayPal Transaction ID from request
                    $payPalTransactionID = $this->request->getQuery('transaction_id',
                                             $this->request->getQuery('paypal_order_id',
                                                                  $this->request->getQuery('orderID')
                                                                 )
                                            );

                    $paymentHistoryData = [
                        'booking_id' => $bookingId,
                        'customer_id' => $booking->customer_id,
                        'paypal_transaction_id' => $payPalTransactionID,
                        'paypal_payer_id' => $this->request->getQuery('payer_id', $this->request->getQuery('paypal_payer_id')),
                        'payment_amount' => $booking->total_cost,
                        'payment_currency' => 'AUD',
                        'payment_status' => 'Completed',
                        'payment_method' => 'PayPal',
                        'payment_date' => FrozenTime::now()->format('Y-m-d H:i:s'),
                        'notes' => 'Payment confirmed via client-side success redirect.'
                    ];

                    $paymentHistory = $this->PaymentHistories->patchEntity($paymentHistory, $paymentHistoryData);

                    $saveResult = $this->PaymentHistories->save($paymentHistory);

                    if ($saveResult) {
                        $this->Flash->success(__('Payment successful! Your booking is confirmed and paid. You will receive an invoice(paid) via email shortly.'));

                        // Send invoice email
                        try {
                            $bookingWithDetails = $this->Bookings->get($bookingId, [
                                'contain' => [
                                    'Customers',
                                    'BookingsServices' => ['Services', 'Stylists'],
                                    'BookingsStylists.Stylists',
                                ]
                            ]);

                            // Generate and save PDF invoice
                            $this->_generateInvoicePdf($bookingWithDetails, $paymentHistory, true);

                            $mailer = new InvoiceMailer();
                            $mailer->sendPaidInvoice($bookingWithDetails, $paymentHistory);
                            Log::info("Invoice email sent for Booking ID: {$bookingId} to {$bookingWithDetails->customer->email}");
                        } catch (\Exception $e) {
                            Log::error("Failed to send invoice email for Booking ID: {$bookingId}. Error: " . $e->getMessage());
                        }
                    } else {
                        Log::error('Failed to save PaymentHistory in SUCCESS.');
                        Log::error('Validation errors after save attempt: ' . json_encode($paymentHistory->getErrors()));
                        Log::error("Failed to save payment history for Booking ID: {$bookingId}. Errors: " . json_encode($paymentHistory->getErrors()), ['scope' => ['payment_success_debug']]);
                        $this->Flash->error(__('Payment was successful, but there was an issue recording payment details. Please contact support.'));
                    }
                } else {
                    Log::error("Failed to update booking status for Booking ID: {$bookingId} after payment. Errors: " . json_encode($booking->getErrors()), ['scope' => ['payment_success']]);
                    $this->Flash->error(__('Payment was successful, but there was an issue updating your booking status. Please contact support.'));
                }
            } else {
                $this->Flash->error(__('Booking not found. Payment status cannot be updated.'));
                 Log::warning("Booking ID: {$bookingId} not found in success action.", ['scope' => ['payment_success']]);
            }

        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            $this->Flash->error(__('Booking not found. Payment status cannot be updated.'));
            Log::error("RecordNotFoundException in success action for Booking ID: {$bookingId}. Error: " . $e->getMessage(), ['scope' => ['payment_success']]);
        } catch (\Exception $e) {
            $this->Flash->error(__('An unexpected error occurred while finalizing your payment details. Please contact support.'));
            Log::error("Unexpected exception in success action for Booking ID: {$bookingId}. Error: " . $e->getMessage(), ['scope' => ['payment_success']]);
        }

        return $this->redirect(['controller' => 'Bookings', 'action' => 'customerview', $bookingId]);
    }

    public function cancel($bookingId)
    {
        $this->Flash->warning(__('Payment was cancelled.'));
        return $this->redirect(['controller' => 'Bookings', 'action' => 'customerview', $bookingId]);
    }

    public function adminIndex()
    {
        // Check if user is admin
        $user = $this->Authentication->getIdentity();
        if (!$user || $user->type !== 'admin') {
            $this->Flash->error('Access denied. Admin only area.');
            return $this->redirect(['controller' => 'Pages', 'action' => 'display', 'home']);
        }

        // Build the query first
        $query = $this->PaymentHistories->find()
            ->contain(['Bookings' => ['Customers']])
            ->order(['PaymentHistories.payment_date' => 'DESC']);

        $payments = $this->paginate($query);

        $this->set(compact('payments'));
    }

    public function processGuestPayment()
    {
        $this->viewBuilder()->setLayout('default');

        $bookingData = $this->request->getSession()->read('GuestBooking.pending_details');

        if (!$bookingData) {
            $this->request->getSession()->delete('GuestBooking.pending_details');
            $this->Flash->error(__('Your booking session has expired or is invalid. Please try creating your booking again.'));
            return $this->redirect(['controller' => 'Bookings', 'action' => 'guestbooking']);
        }

        // Log the data being passed to the view for debugging
        Log::debug('[ProcessGuestPayment] Booking data for view: ' . json_encode($bookingData), ['scope' => ['paypal']]);

        $paymentAmount = number_format((float)($bookingData['total_cost'] ?? 0), 2, '.', '');
        $currencyCode = 'AUD';
        $clientId = Configure::read('PayPal.clientId');
        $mode = Configure::read('PayPal.mode', 'sandbox');

        $temporaryBookingId = $bookingData['id'] ?? 'temp_id_' . uniqid();
        $finalSuccessUrl = Router::url(['controller' => 'Payments', 'action' => 'successGuest', $temporaryBookingId], true);
        $finalCancelUrl = Router::url(['controller' => 'Payments', 'action' => 'cancelGuest', $temporaryBookingId], true);

        $this->set(compact('bookingData', 'clientId', 'mode', 'paymentAmount', 'currencyCode', 'finalSuccessUrl', 'finalCancelUrl'));

        try {
            $this->render('process_guest_payment');
        } catch (\Cake\View\Exception\MissingTemplateException $e) {
            Log::error('Missing template for processGuestPayment: ' . $e->getMessage());
            $this->Flash->error(__('Could not display the payment page. Please contact support.'));
            return $this->redirect(['controller' => 'Pages', 'action' => 'display', 'home']);
        }
    }

    public function successGuest($temporaryBookingId = null)
    {
        Log::debug("[successGuest] Reached for temp ID: {$temporaryBookingId}. Query: " . json_encode($this->request->getQueryParams()));
        $session = $this->request->getSession();
        $bookingData = $session->read('GuestBooking.pending_details');

        if (!$bookingData) {
            $session->delete('GuestBooking.pending_details');
            Log::error('[successGuest] Session data for GuestBooking.pending_details not found.');
            $this->Flash->error(__('Your booking session has expired or payment data is missing. Please try again.'));
            return $this->redirect(['controller' => 'Bookings', 'action' => 'guestbooking']);
        }

        if ($temporaryBookingId && (!isset($bookingData['id']) || $bookingData['id'] !== $temporaryBookingId)) {
            Log::warning("[successGuest] Temporary ID mismatch. Session ID: " . ($bookingData['id'] ?? 'N/A') . ", URL ID: {$temporaryBookingId}");
        }

        $connection = $this->Bookings->getConnection();
        try {
            $connection->begin();
            Log::debug('[successGuest] Transaction started. Booking data from session: ' . json_encode($bookingData));

            // 1. Create and Save the Booking entity
            $newBooking = $this->Bookings->newEntity([
                'customer_id' => $bookingData['customer_id'],
                'booking_name' => $bookingData['booking_name'],
                'booking_date' => $bookingData['booking_date'],
                'total_cost' => $bookingData['total_cost'],
                'remaining_cost' => 0.00,
                'status' => 'Confirmed - Paid',
                'notes' => $bookingData['notes'] ?? null,
                'overall_start_time' => $bookingData['overall_start_time'] ?? null,
                'overall_end_time' => $bookingData['overall_end_time'] ?? null,
            ]);

            if (!$this->Bookings->save($newBooking)) {
                Log::error('[successGuest] Failed to save new Booking. Errors: ' . json_encode($newBooking->getErrors()));
                throw new \Exception('Could not save the booking details.');
            }
            $newBookingId = $newBooking->id;

            // Save BookingsServices
            $bookingsServicesTable = $this->fetchTable('BookingsServices');
            $bookingsStylistsTable = $this->fetchTable('BookingsStylists');
            $processedStylists = [];

            $bookingDateForStorage = null;
            if (!empty($bookingData['booking_date'])) {
                try {
                    $bookingDateForStorage = FrozenTime::parse($bookingData['booking_date'])->format('Y-m-d');
                } catch (\Exception $e) {
                    Log::error("[successGuest] Could not parse booking_date from session: " . $bookingData['booking_date'] . " Error: " . $e->getMessage());
                    throw new \Exception("Invalid booking date format in session.");
                }
            } else {
                Log::error("[successGuest] booking_date missing from session data.");
                throw new \Exception("Booking date is missing from session data.");
            }

            if (!empty($bookingData['bookings_services'])) {
                foreach ($bookingData['bookings_services'] as $serviceKey => $serviceDataFromSession) {
                    if (!isset($serviceDataFromSession['service_id'], $serviceDataFromSession['stylist_id'], $serviceDataFromSession['start_time_formatted'], $serviceDataFromSession['end_time_formatted'], $serviceDataFromSession['service_cost'])) {
                        Log::warning("[successGuest] Incomplete service data in session for key {$serviceKey}: " . json_encode($serviceDataFromSession));
                        throw new \Exception('Incomplete service details in session.');
                    }

                    $bookingService = $bookingsServicesTable->newEntity([
                        'booking_id' => $newBookingId,
                        'service_id' => $serviceDataFromSession['service_id'],
                        'stylist_id' => $serviceDataFromSession['stylist_id'],
                        'start_time' => $serviceDataFromSession['start_time_formatted'],
                        'end_time' => $serviceDataFromSession['end_time_formatted'],
                        'service_cost' => $serviceDataFromSession['service_cost'],
                    ]);
                    if (!$bookingsServicesTable->save($bookingService)) {
                        Log::error('[successGuest] Failed to save BookingsService. Data: ' . json_encode($serviceDataFromSession) . ' Errors: ' . json_encode($bookingService->getErrors()));
                        throw new \Exception('Could not save service details for the booking.');
                    }

                    $stylistId = (int)$serviceDataFromSession['stylist_id'];
                    if (!in_array($stylistId, $processedStylists)) {
                        $bookingStylist = $bookingsStylistsTable->newEntity([
                            'booking_id' => $newBookingId,
                            'stylist_id' => $stylistId,
                            'stylist_date' => $bookingDateForStorage,
                            'selected_cost' => $newBooking->total_cost,
                        ]);
                        if (!$bookingsStylistsTable->save($bookingStylist)) {
                            Log::error('[successGuest] Failed to save BookingsStylist for stylist ID: ' . $stylistId . ' Errors: ' . json_encode($bookingStylist->getErrors()));
                            throw new \Exception('Failed to save booking stylist details.');
                        }
                        $processedStylists[] = $stylistId;
                    }
                }
            } else {
                Log::warning("[successGuest] No 'bookings_services' found in session data for booking ID {$newBookingId}.");
            }

            // 3. Record Payment History
            $payPalTransactionID = $this->request->getQuery('transaction_id', $this->request->getQuery('paypal_order_id'));
            $payPalPayerID = $this->request->getQuery('paypal_payer_id');

            $dataForPaymentHistory = [
                'booking_id' => $newBookingId,
                'customer_id' => $newBooking->customer_id,
                'paypal_transaction_id' => $payPalTransactionID,
                'paypal_payer_id' => $payPalPayerID,
                'payment_amount' => $newBooking->total_cost,
                'payment_currency' => 'AUD',
                'payment_status' => 'Completed',
                'payment_method' => 'PayPal (Guest)',
                'payment_date' => FrozenTime::now()->format('Y-m-d H:i:s'),
                'notes' => 'Guest payment confirmed via client-side redirect. Original PayPal Order ID: ' . $this->request->getQuery('paypal_order_id'),
            ];
            $paymentHistory = $this->PaymentHistories->newEntity($dataForPaymentHistory);

            Log::debug('[successGuest] Attempting to save PaymentHistory entity: ' . json_encode($paymentHistory->toArray(), JSON_PRETTY_PRINT));
            Log::debug('[successGuest] PaymentHistory validation errors before save: ' . json_encode($paymentHistory->getErrors(), JSON_PRETTY_PRINT));

            if (!$this->PaymentHistories->save($paymentHistory)) {
                Log::error('[successGuest] Failed to save PaymentHistory. Booking ID: {$newBookingId}. Errors: ' . json_encode($paymentHistory->getErrors()));
                throw new \Exception('Failed to record payment history. Validation errors: ' . json_encode($paymentHistory->getErrors()));
            } else {
                Log::info("[successGuest] PaymentHistory saved. ID: {$paymentHistory->id} for Booking ID: {$newBookingId}");
            }

            // Generate PDF invoice
            try {
                $bookingWithDetails = $this->Bookings->get($newBookingId, [
                    'contain' => [
                        'Customers',
                        'BookingsServices' => ['Services', 'Stylists'],
                        'BookingsStylists.Stylists',
                    ]
                ]);

                // Generate and save PDF invoice
                $this->_generateInvoicePdf($bookingWithDetails, $paymentHistory, true);

            } catch (\Exception $e) {
                Log::error('Failed to generate PDF invoice for PaymentHistory ID: ' . $paymentHistory->id . '. Error: ' . $e->getMessage());
            }

            $connection->commit();
            Log::info("[successGuest] Transaction committed for Booking ID: {$newBookingId}");

            // Send invoice email
            try {
                $bookingWithDetails = $this->Bookings->get($newBookingId, [
                    'contain' => [
                        'Customers',
                        'BookingsServices' => ['Services', 'Stylists']
                    ]
                ]);

                $mailer = new InvoiceMailer();
                $mailer->sendPaidInvoice($bookingWithDetails, $paymentHistory);
                Log::info("Invoice email sent for Guest Booking ID: {$newBookingId} to {$bookingWithDetails->customer->email}");
            } catch (\Exception $e) {
                Log::error("Failed to send invoice email for Guest Booking ID: {$newBookingId}. Error: " . $e->getMessage());
            }

            // 4. Clear session data
            $session->delete('GuestBooking.pending_details');
            Log::debug('[successGuest] GuestBooking.pending_details session data deleted.');

            $this->Flash->success(__('Thank you! Your payment was successful and your booking is confirmed.'));
            return $this->redirect(['action' => 'guestPaymentSuccess', $newBookingId]);

        } catch (\Exception $e) {
            $connection->rollback();
            Log::error("[successGuest] Transaction rolled back. Error: " . $e->getMessage() . " Session Data: " . json_encode($bookingData));
            $this->Flash->error(__('An error occurred while finalizing your booking after payment: {0} Please contact support.', $e->getMessage()));
            return $this->redirect(['controller' => 'Bookings', 'action' => 'guestbooking']);
        }
    }

    public function cancelGuest($temporaryBookingId = null)
    {
        Log::debug("[cancelGuest] Reached for temp ID: {$temporaryBookingId}. Query: " . json_encode($this->request->getQueryParams()));
        $session = $this->request->getSession();
        $session->delete('GuestBooking.pending_details');
        Log::info('[cancelGuest] GuestBooking.pending_details session data deleted due to cancellation.');

        $this->Flash->warning(__('Your payment was cancelled. Your booking has not been confirmed. Please try again if you wish to book.'));
        return $this->redirect(['controller' => 'Bookings', 'action' => 'guestbooking']);
    }

    public function guestPaymentSuccess($bookingId = null)
    {
        if (!$bookingId) {
            $this->Flash->error('No booking specified.');
            return $this->redirect(['controller' => 'Pages', 'action' => 'display', 'home']);
        }
        try {
            $booking = $this->Bookings->get($bookingId, [
                'contain' => [
                    'Customers',
                    'BookingsServices' => ['Services', 'Stylists' => function ($q) {
                        return $q->select(['id', 'first_name', 'last_name']);
                    }]
                ]
            ]);
            $this->set(compact('booking'));
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            $this->Flash->error('Booking confirmation not found.');
            Log::error("[guestPaymentSuccess] Booking not found for ID: {$bookingId}");
            return $this->redirect(['controller' => 'Pages', 'action' => 'display', 'home']);
        }
    }

    /**
     * Allows a customer or admin to view/download an invoice PDF.
     * Ensures that only the customer who owns the invoice or an admin can access it.
     *
     * @param string|null $paymentHistoryId The ID of the payment history record.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When payment history not found.
     */
    public function viewInvoice($paymentHistoryId = null)
    {
        $currentUser = $this->Authentication->getIdentity();

        if (!$currentUser) {
            $this->Flash->error(__('Please log in to view invoices.'));
            return $this->redirect(['controller' => 'Auth', 'action' => 'login']);
        }

        if (!$paymentHistoryId) {
            $this->Flash->error(__('No invoice specified.'));
            return $this->redirect($this->referer(['controller' => 'Customers', 'action' => 'dashboard']));
        }

        try {
            if (!is_numeric($paymentHistoryId)) {
                $this->Flash->error(__('Invalid payment history ID.'));
                return $this->redirect(['controller' => 'Customers', 'action' => 'dashboard']);
            }

            $paymentHistory = $this->PaymentHistories->get($paymentHistoryId, [
                'contain' => ['Customers', 'Bookings']
            ]);

            if ($paymentHistory->customer_id != $currentUser->id && $currentUser->type !== 'admin') {
                $this->Flash->error(__('You do not have permission to view invoices for this booking.'));
                return $this->redirect(['controller' => 'Customers', 'action' => 'dashboard']);
            }
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            $this->Flash->error(__('Invoice record not found.'));
            Log::warning("[viewInvoice] PaymentHistory record not found for ID: {$paymentHistoryId}");
            return $this->redirect($this->referer(['controller' => 'Customers', 'action' => 'dashboard']));
        }

        // Check if the associated booking exists
        if (empty($paymentHistory->booking)) {
             $this->Flash->error(__('Associated booking not found for this invoice.'));
             Log::error("[viewInvoice] Associated Booking not found for PaymentHistory ID: {$paymentHistoryId}");
             return $this->redirect($this->referer(['controller' => 'Customers', 'action' => 'dashboard']));
        }

        Log::debug("[viewInvoice] Processing PaymentHistory ID: {$paymentHistoryId} with invoice_pdf: '{$paymentHistory->invoice_pdf}' and booking status: '{$paymentHistory->booking->status}'");

        // Check if invoice PDF exists or if we need to generate it
        if (empty($paymentHistory->invoice_pdf)) {
            Log::debug("[viewInvoice] invoice_pdf is EMPTY for PaymentHistory ID: {$paymentHistoryId}. Checking if on-the-fly generation is possible.");
            // No PDF saved, check if we can generate a "Payment Due" version
            if ($paymentHistory->booking->status === 'Confirmed - Payment Due') {
                 Log::info("[viewInvoice] Attempting ON-THE-FLY PDF generation for Payment Due. PaymentHistory ID: {$paymentHistoryId}, Booking ID: {$paymentHistory->booking->id}");
                 // Load full booking details needed for the PDF template
                 try {
                    $bookingIdToFetch = $paymentHistory->booking->id;
                    Log::info("[viewInvoice] Attempting to fetch Booking ID: {$bookingIdToFetch} for on-the-fly invoice.");
                    $bookingWithDetails = $this->Bookings->get($bookingIdToFetch, [
                        'contain' => [
                            'Customers',
                            'BookingsServices' => ['Services', 'Stylists'],
                            'BookingsStylists.Stylists',
                        ]
                    ]);
                    Log::info("[viewInvoice] Successfully fetched Booking ID: {$bookingIdToFetch} (SIMPLIFIED CONTAIN). Object type: " . (is_object($bookingWithDetails) ? get_class($bookingWithDetails) : gettype($bookingWithDetails)));
                    if (!$bookingWithDetails) {
                        Log::error("[viewInvoice] Fetching Booking ID: {$bookingIdToFetch} returned a non-object/falsey value.");
                        throw new \Exception("Failed to retrieve complete booking details for invoice generation.");
                    }

                    Log::info("[viewInvoice] Preparing to call toArray() on BookingWithDetails for Booking ID: {$bookingIdToFetch}");
                    $bookingDetailsArray = $bookingWithDetails->toArray();
                    Log::info("[viewInvoice] Successfully called toArray() on BookingWithDetails. Array has " . count($bookingDetailsArray) . " top-level elements.");

                    Log::debug("[viewInvoice] Data for on-the-fly PDF (BookingWithDetails): " . json_encode($bookingDetailsArray, JSON_PRETTY_PRINT), ['scope' => 'invoice_debug']);
                    
                    Log::info("[viewInvoice] Preparing to call toArray() on PaymentHistory for PH ID: {$paymentHistoryId}");
                    $paymentHistoryArray = $paymentHistory->toArray();
                    Log::info("[viewInvoice] Successfully called toArray() on PaymentHistory. Array has " . count($paymentHistoryArray) . " top-level elements.");

                    Log::debug("[viewInvoice] Data for on-the-fly PDF (PaymentHistory): " . json_encode($paymentHistoryArray, JSON_PRETTY_PRINT), ['scope' => 'invoice_debug']);
                    
                    // Generate and output PDF directly 
                    return $this->_generateInvoicePdf($bookingWithDetails, $paymentHistory, false);
                 } catch (\Cake\Datasource\Exception\RecordNotFoundException $rnfe) {
                    $this->Flash->error(__('The booking details associated with this invoice could not be found.'));
                    Log::error("[viewInvoice] RecordNotFoundException during on-the-fly invoice generation for PH ID: {$paymentHistoryId}, Booking ID: {$paymentHistory->booking->id}. Error: " . $rnfe->getMessage() . "\nTRACE: " . $rnfe->getTraceAsString());
                    return $this->redirect($this->referer(['controller' => 'Customers', 'action' => 'dashboard']));
                 } catch (\Exception $e) { 
                    $this->Flash->error(__('Could not generate the invoice preview. Please contact support.'));
                    Log::error("[viewInvoice] EXCEPTION during on-the-fly invoice generation for PH ID: {$paymentHistoryId}. Error: " . $e->getMessage() . "\nTRACE: " . $e->getTraceAsString());
                    return $this->redirect($this->referer(['controller' => 'Customers', 'action' => 'dashboard']));
                 }
            } else {
                Log::warning("[viewInvoice] invoice_pdf is EMPTY and status is NOT 'Confirmed - Payment Due' (it is '{$paymentHistory->booking->status}'). Cannot generate. PH ID: {$paymentHistoryId}");
                $this->Flash->error(__('Invoice PDF not found for this payment.'));
                return $this->redirect($this->referer(['controller' => 'Customers', 'action' => 'dashboard']));
            }
        } else {
            // Invoice PDF path exists, serve the file
            Log::info("[viewInvoice] invoice_pdf FOUND: '{$paymentHistory->invoice_pdf}'. Attempting to serve file. PH ID: {$paymentHistoryId}");
            $filePath = WWW_ROOT . $paymentHistory->invoice_pdf;

            if (!file_exists($filePath)) {
                $this->Flash->error(__('Invoice file could not be found. Please contact support.'));
                Log::error("[viewInvoice] Invoice PDF file not found at path: {$filePath} for PaymentHistory ID: {$paymentHistoryId}");
                return $this->redirect($this->referer(['controller' => 'Customers', 'action' => 'dashboard']));
            }

            $response = $this->response->withFile($filePath, [
                'download' => false,
                'name' => basename($paymentHistory->invoice_pdf)
            ]);
            $response = $response->withHeader('Content-Disposition', 'inline; filename="' . basename($paymentHistory->invoice_pdf) . '"');
            return $response;
        }
    }

    /**
     * Generates the Invoice PDF using Mpdf.
     *
     * @param \App\Model\Entity\Booking $booking The booking entity with necessary associations (Customer, Services, Stylists).
     * @param \App\Model\Entity\PaymentHistory $paymentHistory The payment history entity.
     * @param bool $saveToFile If true, saves the PDF to file and updates payment history. If false, outputs directly to browser.
     * @return string|null The file path if saved, or null if outputted to browser or an error occurred.
     */
    private function _generateInvoicePdf(\App\Model\Entity\Booking $booking, \App\Model\Entity\PaymentHistory $paymentHistory, bool $saveToFile = true): ?string
    {
        try {
            $view = new View();
            $view->set([
                'booking' => $booking,
                'paymentHistory' => $paymentHistory,
                'companyName' => Configure::read('MyApp.companyName', 'ChicCharm'),
                'companyAddress' => Configure::read('MyApp.companyAddress', '123 Beauty Lane, Styleville'),
                'companyPhone' => Configure::read('MyApp.companyPhone', '03 9000 0000'),
                'companyEmail' => Configure::read('MyApp.companyEmail', 'contact@chiccharm.com'),
                'companyABN' => Configure::read('MyApp.companyABN', '12 345 678 910'),
                'isPdfContext' => true,
            ]);
            $html = $view->render('email/html/invoice', false);
            $mpdf = new Mpdf();
            $mpdf->WriteHTML($html);

            if ($saveToFile) {
                $pdfDir = WWW_ROOT . 'invoices' . DS;
                if (!file_exists($pdfDir)) {
                    mkdir($pdfDir, 0775, true);
                }
                $pdfPath = 'invoices/invoice_' . $this->generateRandomString(10) . $paymentHistory->id . '.pdf';
                $fullPdfPath = WWW_ROOT . $pdfPath;
                $mpdf->Output($fullPdfPath, \Mpdf\Output\Destination::FILE);

                // Update and save PaymentHistory only if saving the file
                $paymentHistory->invoice_pdf = $pdfPath;
                if (!$this->PaymentHistories->save($paymentHistory)) {
                     Log::error("Failed to save invoice_pdf path ({$pdfPath}) for PaymentHistory ID: {$paymentHistory->id}. Errors: " . json_encode($paymentHistory->getErrors()));
                     return null; // Indicate failure
                }
                return $pdfPath;
            } else {
                // Output directly to browser (inline)
                $filename = 'invoice_' . ($booking->id ?? 'booking') . '.pdf';
                $mpdf->Output($filename, \Mpdf\Output\Destination::INLINE);
                // Since we are outputting directly, exit script after PDF is sent
                exit;
            }
        } catch (\Exception $e) {
            Log::error("Failed to generate PDF invoice for PaymentHistory ID: {$paymentHistory->id}. Error: " . $e->getMessage());
            return null; // Indicate failure
        }
    }

    private function generateRandomString($length = 10)
    {
        $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $randomString = '';

        try {
            for ($i = 0; $i < $length; $i++) {
                $randomIndex = random_int(0, strlen($characters) - 1);
                $randomString .= $characters[$randomIndex];
            }

            return $randomString;
        } catch (\Exception $e) {
            return '';
        }
    }
}
