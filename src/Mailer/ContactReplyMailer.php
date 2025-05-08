<?php
declare(strict_types=1);

namespace App\Mailer;

use Cake\Mailer\Mailer;
use App\Model\Entity\Contact; // Assuming you have a Contact entity
use Cake\Core\Configure;
use Cake\Log\Log; // Added Log

class ContactReplyMailer extends Mailer
{
    /**
     * Sends an admin reply to a contact message.
     *
     * @param \App\Model\Entity\Contact $contact The original contact message entity.
     * @param array $replyData Data for the reply (e.g., subject, message from admin).
     * @return void
     */
    public function sendAdminReply(Contact $contact, array $replyData): void
    {
        $fromEmail = Configure::read('Email.default.from_address', env('EMAIL_FROM_ADDRESS', 'nemobyte071@gmail.com'));
        $fromName = Configure::read('Email.default.from_name', env('EMAIL_FROM_NAME', 'ChicCharm Staff'));

        $this
            ->setTo($contact->email, ($contact->first_name ?? '') . ' ' . ($contact->last_name ?? ''))
            ->setFrom([$fromEmail => $fromName])
            ->setSubject($replyData['subject'] ?? 'Re: Your Enquiry with ChicCharm') 
            ->setViewVars([
                'first_name' => $contact->first_name,
                'last_name' => $contact->last_name,
                'message' => $replyData['message'] ?? '',
                'originalMessage' => $contact->message, 
                'companyName' => Configure::read('MyApp.companyName', 'Chic Charm'), 
            ])
            ->setEmailFormat('both') 
            ->viewBuilder()
                ->setTemplate('contact_reply') 
                ->setLayout('default');

        Log::debug("[ContactReplyMailer] Attempting to deliver reply to {$contact->email} for Contact ID {$contact->id}");

        try {
            $this->deliver();
            Log::debug("[ContactReplyMailer] deliver() method completed for Contact ID {$contact->id}");
        } catch (\Exception $e) {
            Log::error("[ContactReplyMailer] Error during deliver() for Contact ID {$contact->id}: " . $e->getMessage());
            throw $e;
        }
    }
} 