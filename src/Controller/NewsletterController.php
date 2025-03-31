<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Event\EventInterface;
use Cake\Mailer\Mailer;

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
            
            // In a real application, you would:
            // 1. Check if the email is already subscribed
            // 2. Store the email in a subscribers table
            // 3. Add any additional user data (name, preferences, etc.)
            
            // For now, we'll just set a success flag and send the confirmation email
            try {
                $this->sendSubscriptionConfirmation($email);
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
     * Send subscription confirmation email
     *
     * @param string $email The subscriber's email address
     * @return bool True if email was sent successfully
     */
    private function sendSubscriptionConfirmation($email)
    {
        $mailer = new Mailer('default');
        
        $mailer
            ->setEmailFormat('both')
            ->setTo($email)
            ->setSubject('Welcome to ChicCharm Newsletter!')
            ->setFrom(env('EMAIL_FROM_ADDRESS', 'chayfong9009@gmail.com'), env('EMAIL_FROM_NAME', 'ChicCharm'));

        $mailer
            ->viewBuilder()
            ->setTemplate('newsletter_subscription');
            
        $mailer->setViewVars([
            'websiteUrl' => 'https://chiccharm.com',
            'unsubscribeUrl' => 'https://chiccharm.com/newsletter/unsubscribe?email=' . urlencode($email)
        ]);
        
        return $mailer->deliver();
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
            // In a real application, you would:
            // 1. Validate the email
            // 2. Remove or mark the email as unsubscribed in your database
            
            $this->Flash->success(__('You have been unsubscribed from our newsletter.'));
        }
        
        return $this->redirect('/');
    }
} 