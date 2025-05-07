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
        $this->Authentication->allowUnauthenticated(['success', 'cancel']);
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
            $orderAmount = number_format($booking->remaining_cost, 2, '.', ''); 
            if ($booking->remaining_cost <= 0) {
                Log::error("Cannot create PayPal order for booking ID {$bookingId} with zero or negative amount: {$booking->remaining_cost}", ['scope' => ['paypal']]);
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
                
                // ---- ADD THIS LOGGING ----
                Log::debug('Attempting to save PaymentHistory in CAPTURE_ORDER action.');
                Log::debug('Using Table: ' . $this->PaymentHistories->getTable());
                Log::debug('Using Entity: ' . $this->PaymentHistories->getEntityClass());
                Log::debug('Data to save: ' . json_encode($paymentHistory->toArray()));
                Log::debug('Validation errors before save: ' . json_encode($paymentHistory->getErrors()));
                // ---- END LOGGING ----

                if (!$this->PaymentHistories->save($paymentHistory)) {
                    // ---- ADD THIS LOGGING ----
                    Log::error('Failed to save PaymentHistory in CAPTURE_ORDER.');
                    Log::error('Validation errors after save attempt: ' . json_encode($paymentHistory->getErrors()));
                    // ---- END LOGGING ----
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
                        'notes' => 'Payment confirmed via client-side capture redirect.',
                    ];
                    $paymentHistory = $this->PaymentHistories->patchEntity($paymentHistory, $paymentHistoryData);
                    
                    // ---- ADD THIS LOGGING ----
                    Log::debug('Attempting to save PaymentHistory in SUCCESS action.');
                    Log::debug('Using Table: ' . $this->PaymentHistories->getTable());
                    Log::debug('Using Entity: ' . $this->PaymentHistories->getEntityClass());
                    Log::debug('Data to save: ' . json_encode($paymentHistory->toArray()));
                    Log::debug('Validation errors before save: ' . json_encode($paymentHistory->getErrors()));
                    // ---- END LOGGING ----

                    $saveResult = $this->PaymentHistories->save($paymentHistory);

                    if ($saveResult) {
                        // ---- ADD THIS LOGGING ----
                        Log::debug('PaymentHistory SAVED successfully in SUCCESS. ID: ' . $paymentHistory->id);
                        // ---- END LOGGING ----
                        $this->Flash->success(__('Payment successful! Your booking is confirmed and paid.'));
                    } else {
                        // ---- ADD THIS LOGGING ----
                        Log::error('Failed to save PaymentHistory in SUCCESS.');
                        Log::error('Validation errors after save attempt: ' . json_encode($paymentHistory->getErrors()));
                        // ---- END LOGGING ----
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

        $this->paginate = [
            'contain' => ['Bookings' => ['Customers']],
            'order' => ['created' => 'DESC']
        ];
        $payments = $this->paginate($this->PaymentHistories);

        $this->set(compact('payments'));
    }

} 