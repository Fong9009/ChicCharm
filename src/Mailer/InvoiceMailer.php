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

    /**
     * Sends a booking confirmation email (Payment Due) to the customer.
     *
     * @param \\App\\Model\\Entity\\Booking $booking The booking entity.
     * @param \\App\\Model\\Entity\\PaymentHistory $paymentHistory The placeholder payment history entity.
     * @return void
     */
    public function sendBookingConfirmedInvoice(Booking $booking, PaymentHistory $paymentHistory): void
    {
        if (empty($booking->customer) || empty($booking->customer->email)) {
            Log::warning("[InvoiceMailer] Customer email not available for booking ID: {$booking->id}. Cannot send booking confirmation email.");
            return;
        }

        $fromEmail = Configure::read('Email.default.from_address', env('EMAIL_FROM_ADDRESS', 'nemobyte071@gmail.com'));
        $fromName = Configure::read('Email.default.from_name', env('EMAIL_FROM_NAME', 'ChicCharm'));

        $this
            ->setTo($booking->customer->email, $booking->customer->full_name ?? $booking->customer->email)
            ->setFrom([$fromEmail => $fromName])
            // Set a subject indicating confirmation and payment needed
            ->setSubject(sprintf('Your ChicCharm Booking is Confirmed (Payment Due) - Ref #%s', $booking->id))
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
                ->setTemplate('invoice') // Use the same template
                ->setLayout('default');

        Log::debug("[InvoiceMailer] Attempting to deliver BOOKING CONFIRMED (Payment Due) email for Booking ID {$booking->id} to {$booking->customer->email}");

        try {
            $this->deliver();
            Log::debug("[InvoiceMailer] deliver() method completed for Booking ID {$booking->id} (Payment Due)");
        } catch (\Exception $e) {
            Log::error("[InvoiceMailer] Error during deliver() for Booking ID {$booking->id} (Payment Due): " . $e->getMessage());
            // Decide if re-throwing is necessary or just log
            // throw $e;
        }
    }

    /**
     * Sends a payment confirmation invoice email to the customer.
     * Attaches the generated PDF invoice.
     *
     * @param \\App\\Model\\Entity\\Booking $booking The booking entity.
     * @param \\App\\Model\\Entity\\PaymentHistory $paymentHistory The completed payment history entity.
     * @return void
     */
    public function sendPaidInvoice(Booking $booking, PaymentHistory $paymentHistory): void
    {
        if (empty($booking->customer) || empty($booking->customer->email)) {
            Log::warning("[InvoiceMailer] Customer email not available for booking ID: {$booking->id}. Cannot send PAID invoice.");
            return;
        }

        $fromEmail = Configure::read('Email.default.from_address', env('EMAIL_FROM_ADDRESS', 'nemobyte071@gmail.com'));
        $fromName = Configure::read('Email.default.from_name', env('EMAIL_FROM_NAME', 'ChicCharm'));

        $this
            ->setTo($booking->customer->email, $booking->customer->full_name ?? $booking->customer->email)
            ->setFrom([$fromEmail => $fromName])
            // Updated subject for payment confirmation
            ->setSubject(sprintf('Your ChicCharm Payment is Confirmed - Invoice Attached - Ref #%s', $booking->id))
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

        // Attach the PDF if the path exists
        if (!empty($paymentHistory->invoice_pdf)) {
            $pdfPath = WWW_ROOT . $paymentHistory->invoice_pdf;
            if (file_exists($pdfPath)) {
                $this->addAttachments([
                    basename($paymentHistory->invoice_pdf) => [
                        'file' => $pdfPath,
                        'mimetype' => 'application/pdf',
                    ]
                ]);
                Log::debug("[InvoiceMailer] Attached PDF: {$pdfPath} for Booking ID {$booking->id}");
            } else {
                 Log::warning("[InvoiceMailer] Invoice PDF path found in DB ({$paymentHistory->invoice_pdf}) but file does not exist at {$pdfPath} for Booking ID {$booking->id}. Sending email without attachment.");
            }
        } else {
            Log::warning("[InvoiceMailer] invoice_pdf path is empty for PaymentHistory ID {$paymentHistory->id} (Booking ID {$booking->id}). Sending PAID email without attachment.");
        }


        Log::debug("[InvoiceMailer] Attempting to deliver PAID invoice for Booking ID {$booking->id} to {$booking->customer->email}");

        try {
            $this->deliver();
            Log::debug("[InvoiceMailer] deliver() method completed for Booking ID {$booking->id} (Paid)");
        } catch (\Exception $e) {
            Log::error("[InvoiceMailer] Error during deliver() for Booking ID {$booking->id} (Paid): " . $e->getMessage());
            throw $e; // Re-throw for paid invoice errors? Or handle differently?
        }
    }
} 