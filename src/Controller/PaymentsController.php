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

                // Record payment history
                $paymentHistory = $this->PaymentHistories->newEmptyEntity();

                $captureDetails = $captureData['purchase_units'][0]['payments']['captures'][0] ?? null;
                $paypalApiStatus = strtolower($captureData['status'] ?? 'unknown');
                $internalPaymentStatus = ($paypalApiStatus === 'completed') ? 'Completed' : $paypalApiStatus;

                $paymentHistoryData = [
                    'booking_id' => $bookingId,
                    'customer_id' => $booking->customer_id,
                    'paypal_transaction_id' => $captureData['id'] ?? $orderID,
                    'paypal_payer_id' => $captureData['payer']['payer_id'] ?? null,
                    'payment_amount' => $captureDetails ? ($captureDetails['amount']['value'] ?? null) : $booking->total_cost,
                    'payment_currency' => $captureDetails ? ($captureDetails['amount']['currency_code'] ?? 'AUD') : 'AUD',
                    'payment_status' => $internalPaymentStatus,
                    'payment_method' => 'paypal',
                    'payment_date' => isset($captureDetails['create_time'])
                                        ? \Cake\I18n\FrozenTime::parse($captureDetails['create_time'])->format('Y-m-d H:i:s')
                                        : gmdate("Y-m-d H:i:s"),
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
                    // Record Payment History
                    $paymentHistory = $this->PaymentHistories->newEmptyEntity();

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
                        'payment_method' => 'PayPal (Client-side)',
                        'payment_date' => FrozenTime::now()->format('Y-m-d H:i:s'),
                        'notes' => 'Payment confirmed',
                    ];
                    $paymentHistory = $this->PaymentHistories->patchEntity($paymentHistory, $paymentHistoryData);

                    $saveResult = $this->PaymentHistories->save($paymentHistory);

                    if ($saveResult) {
                        $this->Flash->success(__('Payment successful! Your booking is confirmed and paid.'));

                        // Send invoice email
                        try {
                            $bookingWithDetails = $this->Bookings->get($bookingId, [
                                'contain' => [
                                    'Customers',
                                    'BookingsServices' => ['Services', 'Stylists']
                                ]
                            ]);

                            $mailer = new InvoiceMailer();
                            $mailer->sendInvoice($bookingWithDetails, $paymentHistory);
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
                 // This case should ideally not be reached if get() throws RecordNotFoundException
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
                'start_time' => $bookingData['overall_start_time'] ?? null,
                'end_time' => $bookingData['overall_end_time'] ?? null,
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

            $connection->commit();
            Log::info("[successGuest] Transaction committed for Booking ID: {$newBookingId}");

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

}
