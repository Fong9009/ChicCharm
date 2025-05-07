<?php
declare(strict_types=1);

namespace App\Mailer;

use Cake\Mailer\Mailer;
use Cake\Core\Configure;
use Cake\Log\Log;

class NewsletterMailer extends Mailer
{
    /**
     * Sends a welcome/confirmation email upon newsletter subscription.
     *
     * @param string $recipientEmail The email address of the new subscriber.
     * @return void
     */
    public function sendWelcome(string $recipientEmail): void
    {
        $fromEmail = Configure::read('Email.default.from_address', env('EMAIL_FROM_ADDRESS', 'nemobyte071@gmail.com'));
        $fromName = Configure::read('Email.default.from_name', env('EMAIL_FROM_NAME', 'ChicCharm Newsletter'));

        $unsubscribeToken = hash('sha256', $recipientEmail . microtime()); 

        $this
            ->setTo($recipientEmail)
            ->setFrom([$fromEmail => $fromName])
            ->setSubject('Welcome to the ChicCharm Newsletter!')
            ->setViewVars([
                'recipientEmail' => $recipientEmail,
                'websiteUrl' => Configure::read('App.fullBaseUrl', 'http://localhost:8765'), 
                'unsubscribeUrl' => \Cake\Routing\Router::url([
                    'controller' => 'Newsletter',
                    'action' => 'unsubscribe',
                    '?' => ['email' => $recipientEmail, 'token' => $unsubscribeToken] 
                ], true), 
                'companyName' => Configure::read('MyApp.companyName', 'Chic Charm'),
            ])
            ->setEmailFormat('both') 
            ->viewBuilder()
                ->setTemplate('newsletter_welcome') 
                ->setLayout('default');

        Log::debug("NewsletterMailer: Attempting to deliver welcome email to {$recipientEmail} from {$fromEmail}");

        try {
            $this->deliver();
            Log::debug("NewsletterMailer: deliver() method completed for {$recipientEmail}");
        } catch (\Exception $e) {
            Log::error("NewsletterMailer: Error during deliver() for {$recipientEmail}: " . $e->getMessage());
            throw $e; 
        }
    }
} 