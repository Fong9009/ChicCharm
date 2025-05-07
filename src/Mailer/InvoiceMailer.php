<?php
declare(strict_types=1);

namespace App\Mailer;

use Cake\Mailer\Mailer;
use App\Model\Entity\Booking;
use App\Model\Entity\PaymentHistory;
use Cake\Core\Configure;
use Cake\Log\Log;

class InvoiceMailer extends Mailer
{
    /**
     * Sends an invoice email to the customer.
     *
     * @param \App\Model\Entity\Booking $booking The booking entity.
     * @param \App\Model\Entity\PaymentHistory $paymentHistory The payment history entity.
     * @return void
     */
    public function sendInvoice(Booking $booking, PaymentHistory $paymentHistory): void
    {
        if (empty($booking->customer) || empty($booking->customer->email)) {
            Log::warning("[InvoiceMailer] Customer email not available for booking ID: {$booking->id}. Cannot send invoice.");
            return;
        }

        $fromEmail = Configure::read('Email.default.from_address', env('EMAIL_FROM_ADDRESS', 'nemobyte071@gmail.com'));
        $fromName = Configure::read('Email.default.from_name', env('EMAIL_FROM_NAME', 'ChicCharm'));

        $this
            ->setTo($booking->customer->email, $booking->customer->full_name ?? $booking->customer->email)
            ->setFrom([$fromEmail => $fromName]) 
            ->setSubject(sprintf('Your ChicCharm Booking Invoice - Ref #%s', $booking->id))
            ->setViewVars([
                'booking' => $booking,  
                'paymentHistory' => $paymentHistory,
                'companyName' => Configure::read('MyApp.companyName', 'Chic Charm'), 
                'companyAddress' => Configure::read('MyApp.companyAddress', '123 Beauty Lane, Styleville'), 
                'companyPhone' => Configure::read('MyApp.companyPhone', '03 9000 0000'), 
                'companyEmail' => Configure::read('MyApp.companyEmail', 'contact@chiccharm.com'), 
                'companyABN' => Configure::read('MyApp.companyABN', '12 345 678 910'), 
            ])
            ->setEmailFormat('both')
            ->viewBuilder()
                ->setTemplate('invoice') 
                ->setLayout('default');

        Log::debug("[InvoiceMailer] Attempting to deliver invoice for Booking ID {$booking->id} to {$booking->customer->email}");

        try {
            $this->deliver();
            Log::debug("[InvoiceMailer] deliver() method completed for Booking ID {$booking->id}");
        } catch (\Exception $e) {
            Log::error("[InvoiceMailer] Error during deliver() for Booking ID {$booking->id}: " . $e->getMessage());
            throw $e; 
        }
    }
} 