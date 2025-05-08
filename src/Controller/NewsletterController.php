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
                // Just return without setting success message
                return $this->redirect($this->referer() . '#footer');
            }

            // Add log HERE to confirm this point is reached
            Log::debug("NewsletterController::subscribe - Passed validation for email: {$email}");

            // TO Be Implemented
            // 1. Check if the email is already subscribed
            // 2. Store the email in a subscribers table
            // 3. Add any additional user data (name, preferences, etc.)

            // For now, we'll just set a success flag and send the confirmation email
            try {
                $mailer = new NewsletterMailer();
                $mailer->sendWelcome($email);
                Log::info("Newsletter welcome email attempt successful for {$email}");
                
                // Set a session variable instead of using Flash
                $this->request->getSession()->write('newsletter_success', true);
            } catch (\Exception $e) {
                // Just log the error, don't show anything to user
                $this->log('Newsletter subscription error: ' . $e->getMessage(), 'error');
            }

            // Redirect to the same page but with #footer anchor to keep focus on the footer area
            return $this->redirect($this->referer() . '#footer');
        }
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
