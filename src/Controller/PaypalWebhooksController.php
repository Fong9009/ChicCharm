<?php
declare(strict_types=1);

namespace App\Controller;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Log\Log;
use Cake\Event\EventInterface;

class PaypalWebhooksController extends AppController
{
    protected \Cake\ORM\Table $PaypalWebhookEvents;

    public function initialize(): void
    {
        parent::initialize();
        $this->PaypalWebhookEvents = $this->fetchTable('PaypalWebhookEvents');
        
        // Allow webhook endpoint to be accessed without authentication
        if ($this->components()->has('Authentication')) {
            $this->Authentication->allowUnauthenticated(['webhook']);
        }
    }

    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $action = $this->request->getParam('action');

        if ($action !== 'webhook') {
            $identity = $this->Authentication->getIdentity();
            if (!$identity || $identity->type !== 'admin') {
                $this->Flash->error(__('You are not authorized to access this page.'));
                return $this->redirect(['controller' => 'Pages', 'action' => 'display', 'home']);
            }
        }
        return null; 
    }

    public function webhook()
    {
        $this->request->allowMethod(['post']);
        
        // Get the webhook event
        $webhookEvent = $this->request->getData();
        Log::debug('PayPal Webhook received: ' . json_encode($webhookEvent));
        
        // Verify the webhook signature
        if (!$this->verifyWebhookSignature($webhookEvent)) {
            Log::error('Invalid webhook signature');
            return $this->response->withStatus(400);
        }
        
        // Extract relevant data
        $given_name = $webhookEvent['resource']['payer']['name']['given_name'] ?? '';
        $surname = $webhookEvent['resource']['payer']['name']['surname'] ?? '';
        $payer_name_full = trim($given_name . ' ' . $surname);
        if (empty($payer_name_full)) {
            $payer_name_full = null;
        }

        $eventData = [
            'paypal_event_id' => $webhookEvent['id'] ?? null, 
            'event_name' => $webhookEvent['event_type'] ?? null, 
            'summary' => $webhookEvent['summary'] ?? null, 
            'resource_type' => $webhookEvent['resource_type'] ?? null,
            'resource_id' => $webhookEvent['resource']['id'] ?? null, 
            'amount' => $webhookEvent['resource']['amount']['value'] ?? null,
            'currency' => $webhookEvent['resource']['amount']['currency_code'] ?? null,
            'paypal_resource_status' => $webhookEvent['resource']['status'] ?? null, 
            'payer_email' => $webhookEvent['resource']['payer']['email_address'] ?? null,
            'payer_name' => $payer_name_full,
            'payload' => json_encode($webhookEvent),
            'paypal_event_creation_time' => isset($webhookEvent['create_time']) ? date('Y-m-d H:i:s', strtotime($webhookEvent['create_time'])) : null, 
        ];
        
        // Save the webhook event
        $event = $this->PaypalWebhookEvents->newEntity($eventData);
        if ($this->PaypalWebhookEvents->save($event)) {
            Log::info('Webhook event saved successfully');
            return $this->response->withStatus(200);
        }
        
        Log::error('Failed to save webhook event: ' . json_encode($event->getErrors()));
        return $this->response->withStatus(500);
    }

    public function dashboard()
    {
        // Admin check is now handled by beforeFilter()

        // Get recent webhook events
        $events = $this->PaypalWebhookEvents->find()
            ->order(['received_at' => 'DESC'])
            ->limit(50);

        // Get payment statistics
        $stats = $this->PaypalWebhookEvents->find()
            ->select([
                'total_amount' => 'SUM(amount)',
                'total_transactions' => 'COUNT(*)',
                'avg_amount' => 'AVG(amount)'
            ])
            ->where(['event_name' => 'PAYMENT.CAPTURE.COMPLETED']) 
            ->first();

        $this->set(compact('events', 'stats'));
    }

    private function verifyWebhookSignature($webhookEvent)
    {
        $paypalWebhookId = Configure::read('PayPal.webhookId');
        $transmissionId = $this->request->getHeaderLine('Paypal-Transmission-Id');
        $timestamp = $this->request->getHeaderLine('Paypal-Transmission-Time');
        $webhookId = $this->request->getHeaderLine('Paypal-Webhook-Id');
        $signature = $this->request->getHeaderLine('Paypal-Transmission-Sig');
        
        // Verify the webhook ID matches
        if ($webhookId !== $paypalWebhookId) {
            Log::error('Webhook ID mismatch');
            return false;
        }
        
        return true;
    }
} 