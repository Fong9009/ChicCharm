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
                'companyName' => Configure::read('MyApp.companyName', 'ChicCharm'), 
                'companyAddress' => Configure::read('MyApp.companyAddress', '123 Beauty Lane, Styleville'), 
                'companyPhone' => Configure::read('MyApp.companyPhone', '03 9000 0000'), 
                'companyEmail' => Configure::read('MyApp.companyEmail', 'contact@chiccharm.com'), 
                'companyABN' => Configure::read('MyApp.companyABN', '12 345 678 910'),
                'isPdfContext' => false,
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
                'companyName' => Configure::read('MyApp.companyName', 'ChicCharm'),
                'companyAddress' => Configure::read('MyApp.companyAddress', '123 Beauty Lane, Styleville'),
                'companyPhone' => Configure::read('MyApp.companyPhone', '03 9000 0000'),
                'companyEmail' => Configure::read('MyApp.companyEmail', 'contact@chiccharm.com'),
                'companyABN' => Configure::read('MyApp.companyABN', '12 345 678 910'),
                'isPdfContext' => false,
            ])
            ->setEmailFormat('both')
            ->viewBuilder()
                ->setTemplate('invoice') 
                ->setLayout('default');

        try {
            $this->deliver();
            Log::debug("[InvoiceMailer] deliver() method completed for Booking ID {$booking->id} (Payment Due)");
        } catch (\Exception $e) {
            Log::error("[InvoiceMailer] Error during deliver() for Booking ID {$booking->id} (Payment Due): " . $e->getMessage());
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
                'companyName' => Configure::read('MyApp.companyName', 'ChicCharm'),
                'companyAddress' => Configure::read('MyApp.companyAddress', '123 Beauty Lane, Styleville'),
                'companyPhone' => Configure::read('MyApp.companyPhone', '03 9000 0000'),
                'companyEmail' => Configure::read('MyApp.companyEmail', 'contact@chiccharm.com'),
                'companyABN' => Configure::read('MyApp.companyABN', '12 345 678 910'),
                'isPdfContext' => false,
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

    /**
     * Sends a notification to the customer after an admin edits their booking,
     * detailing refunds due or additional payments required.
     *
     * @param \App\Model\Entity\Booking $booking The updated booking entity (with Customer loaded).
     * @param array $changeDetails Details about the change (type: refund_due|additional_payment_due, amount, etc.).
     * @param \App\Model\Entity\PaymentHistory|null $editedPaymentHistory The new payment history record with PDF details, if applicable.
     * @return void
     */
    public function sendAdminEditNotification(Booking $booking, array $changeDetails, ?PaymentHistory $editedPaymentHistory = null): void
    {
        if (empty($booking->customer) || empty($booking->customer->email)) {
            Log::warning("[InvoiceMailer.sendAdminEditNotification] Customer email not available for booking ID: {$booking->id}");
            return;
        }

        $subject = '';
        if ($changeDetails['type'] === 'refund_due') {
            $subject = 'Update to your booking - Refund Due';
        } elseif ($changeDetails['type'] === 'additional_payment_due') {
            $subject = 'Update to your booking - Additional Payment Required';
        } else {
            $subject = 'Important Update to Your Booking'; 
        }

        // View variables
        $viewVars = [
            'booking' => $booking, 
            'changeDetails' => $changeDetails, 
            'paymentHistory' => $booking->latest_payment_history ?? null, 
            'companyName' => Configure::read('MyApp.companyName', 'ChicCharm'),
            'companyAddress' => Configure::read('MyApp.companyAddress', '123 Beauty Lane, Styleville'),
            'companyPhone' => Configure::read('MyApp.companyPhone', '03 9000 0000'),
            'companyEmail' => Configure::read('MyApp.companyEmail', 'contact@chiccharm.com'),
            'companyABN' => Configure::read('MyApp.companyABN', '12 345 678 910'),
            'isPdfContext' => false, 
            'isAdminEditNotification' => true,
            'editedPaymentHistory' => $editedPaymentHistory
        ];

        try {
            $this->setTo($booking->customer->email)
                ->setSubject($subject)
                ->setViewVars($viewVars)
                ->setEmailFormat('both')
                ->viewBuilder()
                    ->setTemplate('invoice')
                    ->setLayout('default');

            // Attach the PDF if available from the editedPaymentHistory
            if ($editedPaymentHistory && !empty($editedPaymentHistory->invoice_pdf)) {
                $pdfPath = WWW_ROOT . $editedPaymentHistory->invoice_pdf;
                if (file_exists($pdfPath)) {
                    $this->addAttachments([
                        basename($editedPaymentHistory->invoice_pdf) => [
                            'file' => $pdfPath,
                            'mimetype' => 'application/pdf',
                        ]
                    ]);
                    Log::info("[InvoiceMailer.sendAdminEditNotification] Attached PDF: {$pdfPath} for Booking ID {$booking->id}");
                } else {
                    Log::warning("[InvoiceMailer.sendAdminEditNotification] PDF path found in editedPaymentHistory ({$editedPaymentHistory->invoice_pdf}) but file does not exist at {$pdfPath} for Booking ID {$booking->id}. Sending email without attachment.");
                }
            } elseif ($editedPaymentHistory) {
                Log::warning("[InvoiceMailer.sendAdminEditNotification] editedPaymentHistory provided but invoice_pdf path is empty for PaymentHistory ID {$editedPaymentHistory->id} (Booking ID {$booking->id}). Sending email without attachment.");
            }

            $this->deliver();
            Log::info("[InvoiceMailer.sendAdminEditNotification] Email sent for Booking ID {$booking->id} to {$booking->customer->email}. Type: {$changeDetails['type']}");
        } catch (\Exception $e) {
            Log::error("[InvoiceMailer.sendAdminEditNotification] Error during deliver() for Booking ID {$booking->id}: " . $e->getMessage());
        }
    }

    /**
     * Sends an email notification to the customer after an admin cancels their paid booking,
     * informing them about the cancellation and the refund due.
     *
     * @param \App\Model\Entity\Booking $booking The cancelled booking entity (with Customer loaded).
     * @param float $refundAmount The total amount to be refunded.
     * @return void
     */
    public function sendAdminCancellationRefundNotification(Booking $booking, float $refundAmount): void
    {
        if (empty($booking->customer) || empty($booking->customer->email)) {
            Log::warning("[InvoiceMailer.sendAdminCancellationRefundNotification] Customer email not available for booking ID: {$booking->id}");
            return;
        }

        $fromEmail = Configure::read('Email.default.from_address', env('EMAIL_FROM_ADDRESS', 'nemobyte071@gmail.com'));
        $fromName = Configure::read('Email.default.from_name', env('EMAIL_FROM_NAME', 'ChicCharm'));

        $subject = sprintf('Your ChicCharm Booking #%s Has Been Cancelled - Refund Information', $booking->id);

        $viewVars = [
            'booking' => $booking,
            'refundAmount' => $refundAmount,
            'customerName' => $booking->customer->full_name ?? 'Valued Customer',
            'companyName' => Configure::read('MyApp.companyName', 'ChicCharm'),
            'companyPhone' => Configure::read('MyApp.companyPhone', '03 9000 0000'),
            'companyEmail' => Configure::read('MyApp.companyEmail', 'contact@chiccharm.com'),
        ];

        try {
            $this
                ->setTo($booking->customer->email, $booking->customer->full_name ?? $booking->customer->email)
                ->setFrom([$fromEmail => $fromName])
                ->setSubject($subject)
                ->setViewVars($viewVars)
                ->setEmailFormat('both') 
                ->viewBuilder()
                    ->setTemplate('admin_cancellation_refund') 
                    ->setLayout('default');
            
            $this->deliver();
            Log::info("[InvoiceMailer.sendAdminCancellationRefundNotification] Email sent for Booking ID {$booking->id} to {$booking->customer->email}.");
        } catch (\Exception $e) {
            Log::error("[InvoiceMailer.sendAdminCancellationRefundNotification] Error during deliver() for Booking ID {$booking->id}: " . $e->getMessage());
        }
    }
} 