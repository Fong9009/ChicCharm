<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Event\EventInterface;
use Cake\Mailer\Mailer;
use App\Mailer\NewsletterMailer;
use Cake\Log\Log;

/**
 * Newsletter Controller
 */
class NewsletterController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('Authentication.Authentication');
        $this->Authentication->allowUnauthenticated(['subscribe']);
    }

    /**
     * Subscribe method - Handles newsletter subscription requests
     *
     * @return \Cake\Http\Response|null|void Redirects on successful subscription
     */
    public function subscribe()
    {
        if ($this->request->is('post')) {
            $email = $this->request->getData('email');

            // Basic validation
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                if ($this->request->is('ajax')) {
                    return $this->response
                        ->withType('application/json')
                        ->withStringBody(json_encode(['success' => false, 'message' => 'Please enter a valid email address.']))
                        ->withStatus(400);
                }
                return $this->redirect($this->referer() . '#footer'); 
            }

            Log::debug("NewsletterController::subscribe - Processing email: {$email}");

            try {
                // TO Be Implemented
                // 1. Check if the email is already subscribed (important for production)
                // 2. Store the email in a subscribers table

                $mailer = new NewsletterMailer();
                $mailer->sendWelcome($email);
                Log::info("Newsletter welcome email sent successfully to {$email}");

                if ($this->request->is('ajax')) {
                    return $this->response
                        ->withType('application/json')
                        ->withStringBody(json_encode(['success' => true, 'message' => 'Thank you for subscribing!']));
                } else {
                    $this->request->getSession()->write('newsletter_success', true);
                    return $this->redirect($this->referer() . '#footer'); 
                }

            } catch (\Exception $e) {
                Log::error("Newsletter subscription error for {$email}: " . $e->getMessage());
                if ($this->request->is('ajax')) {
                    return $this->response
                        ->withType('application/json')
                        ->withStringBody(json_encode(['success' => false, 'message' => 'Subscription failed. Please try again later.']))
                        ->withStatus(500);
                } else {
                    return $this->redirect($this->referer() . '#footer'); 
                }
            }
        }
        return $this->redirect($this->referer('/') . '#footer');
    }

    /**
     * Unsubscribe method - Handles newsletter unsubscribe requests
     * This is a placeholder - in a real app, you would implement proper unsubscribe functionality
     *
     * @return \Cake\Http\Response|null|void Redirects after unsubscribe
     */
    public function unsubscribe()
    {
        $email = $this->request->getQuery('email');

        if (!empty($email)) {
            // TO Be Implemented
            // 1. Validate the email
            // 2. Remove or mark the email as unsubscribed in your database

            $this->Flash->success(__('You have been unsubscribed from our newsletter.'));
        }

        return $this->redirect('/');
    }
}
