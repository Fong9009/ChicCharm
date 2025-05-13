<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Booking $booking
 * @var \App\Model\Entity\PaymentHistory $paymentHistory
 * @var \App\Model\Entity\PaymentHistory|null $editedPaymentHistory
 * @var array|null $changeDetails
 * @var bool|null $isPdfContext
 * @var bool|null $isAdminEditNotification
 * @var string $companyName
 * @var string $companyAddress
 * @var string $companyPhone
 * @var string $companyEmail
 * @var string $companyABN
 */


// Format booking date and times, providing fallbacks
$bookingDateFormatted = $booking->booking_date ? $booking->booking_date->format('l, F jS, Y') : 'Date N/A';
$bookingOverallStartTime = $booking->start_time ? $booking->start_time->format('h:i A') : null;
$bookingOverallEndTime = $booking->end_time ? $booking->end_time->format('h:i A') : null;

// Ensure $paymentHistory is available for date formatting, fallback for $editedPaymentHistory if primary is null
$dateSourceForPayment = $paymentHistory ?? $editedPaymentHistory;
$paymentDateFormatted = $dateSourceForPayment && $dateSourceForPayment->payment_date ? $dateSourceForPayment->payment_date->format('d/m/Y H:i') : 'N/A';

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            border: 1px solid #ddd;
            padding: 30px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            background-color: #fff;
            border-radius: 8px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #d9534f;   
        }
        .header h1 {
            margin: 0 0 10px 0;
            color: #d9534f; 
            font-size: 2.5em;
        }
        .header p {
            font-size: 0.9em;
            color: #555;
            line-height: 1.4;
        }
        .invoice-details, .customer-details, .booking-summary, .payment-summary, .stylist-details, .booking-notes {
            margin-bottom: 25px;
        }
        .invoice-details h4, .customer-details h4, .booking-summary h4, .payment-summary h4, .stylist-details h4, .booking-notes h4 {
            color: #d9534f;
            margin-top: 0;
            margin-bottom: 10px;
            font-size: 1.2em;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }
        .invoice-details table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0; 
        }
         .booking-summary table {
            width: 100%;
            border-collapse: collapse;
        }
        .invoice-details th, .invoice-details td,
        .booking-summary th, .booking-summary td {
            border: 1px solid #eee;
            padding: 10px;
            text-align: left;
            font-size: 0.95em;
        }
        .booking-summary th {
            background-color: #f9f9f9;
        }
        .total-row td {
            font-weight: bold;
            font-size: 1.1em !important; 
            background-color: #f5f5f5; 
        }
        .total-row td:first-child {
            text-align:right;
        }
        .footer {
            text-align: center;
            font-size: 0.9em;
            color: #777;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        .company-info strong {
            display: block;
            margin-bottom: 5px;
        }
        .stylist-list {
            list-style: none;
            padding-left: 0;
        }
        .stylist-list li {
            padding: 5px 0;
        }

    </style>
</head>
<body>
    <div class="invoice-container">
        <?php // Admin Edit Notification Block - Primarily for Email Body ?>
        <?php if (!empty($isAdminEditNotification) && empty($isPdfContext) && isset($changeDetails)): ?>
            <div class="admin-edit-summary" style="padding: 15px; margin-bottom: 20px; border: 1px solid #ffc107; background-color: #fff3cd; border-radius: 5px;">
                <h3 style="margin-top: 0; color: #856404;">Update Regarding Your Recent Booking Changes</h3>
                <p>An administrator has recently updated your booking (ID: <?= h($booking->id) ?>).</p>
                
                <?php if ($changeDetails['type'] === 'refund_due'): ?>
                    <p style="font-weight: bold; color: #155724;">
                        A refund of <?= $this->Number->currency($changeDetails['amount'], 'AUD') ?> is due to you because of these changes.
                    </p>
                    <p>Your new booking total is <?= $this->Number->currency($booking->total_cost, 'AUD') ?>.
                       This refund will be processed manually by our team shortly. Please allow a few business days for it to reflect in your account.
                    </p>
                     <?php if ($editedPaymentHistory && $editedPaymentHistory->invoice_pdf): ?>
                        <p>A credit note with details of this refund is attached to this email.</p>
                    <?php endif; ?>
                <?php elseif ($changeDetails['type'] === 'additional_payment_due'): ?>
                    <p style="font-weight: bold; color: #721c24;">
                        An additional payment of <?= $this->Number->currency($changeDetails['amount'], 'AUD') ?> is required due to these changes.
                    </p>
                    <p>Your new booking total is <?= $this->Number->currency($booking->total_cost, 'AUD') ?>.
                       Please settle the outstanding amount of <?= $this->Number->currency($booking->remaining_cost > 0 ? $booking->remaining_cost : $changeDetails['amount'], 'AUD') ?> when you arrive for your appointment, or via the payment link if available.
                    </p>
                    <?php if ($editedPaymentHistory && $editedPaymentHistory->invoice_pdf): ?>
                        <p>An updated invoice reflecting this additional amount is attached to this email.</p>
                    <?php endif; ?>
                <?php endif; ?>
                <hr style="border-top: 1px solid #ffc107;">
                <p>Below is a summary of your updated booking details. For full details, please see the attached document (if applicable).</p>
            </div>
        <?php endif; ?>

        <div class="container">
            <div class="header">
                <h1><?= h($companyName) ?></h1>
                <p><?= nl2br(h($companyAddress)) ?><br>
                   Phone: <?= h($companyPhone) ?> | Email: <?= h($companyEmail) ?><br>
                   <?php if (!empty($companyABN)): ?>ABN: <?= h($companyABN) ?><?php endif; ?>
                </p>
                <?php
                $documentTitle = "Invoice / Booking Confirmation"; // Default
                if (!empty($isPdfContext) && !empty($isAdminEditNotification) && isset($changeDetails)) {
                    if ($changeDetails['type'] === 'refund_due') {
                        $documentTitle = "Credit Note / Refund Confirmation";
                    } elseif ($changeDetails['type'] === 'additional_payment_due') {
                        $documentTitle = "Updated Invoice / Additional Amount Due";
                    }
                }
                ?>
                <h2><?= h($documentTitle) ?></h2>
            </div>

            <div class="invoice-details">
                <h4>Details:</h4>
                <table>
                    <tr>
                        <th>Invoice #:</th>
                        <?php 
                        $invoiceIdToShow = 'N/A';
                        if (!empty($isAdminEditNotification) && $editedPaymentHistory) {
                            $invoiceIdToShow = 'ADJ-' . h($editedPaymentHistory->id);
                        } elseif ($paymentHistory) {
                            $invoiceIdToShow = 'PAY-' . h($paymentHistory->id);
                        }
                        ?>
                        <td><?= $invoiceIdToShow ?></td>
                    </tr>
                     <?php if (!empty($isAdminEditNotification) && $paymentHistory && $editedPaymentHistory && $paymentHistory->id !== $editedPaymentHistory->id): ?>
                    <tr>
                        <th>Original Invoice #:</th>
                        <td>PAY-<?= h($paymentHistory->id) ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <th>Booking Ref #:</th>
                        <td><?= h($booking->id) ?></td>
                    </tr>
                    <tr>
                        <th>Date Issued:</th>
                        <?php
                        $dateIssuedToShow = 'N/A';
                        if (!empty($isAdminEditNotification) && $editedPaymentHistory && $editedPaymentHistory->payment_date) {
                            $dateIssuedToShow = $editedPaymentHistory->payment_date->format('d/m/Y H:i');
                        } elseif ($paymentHistory && $paymentHistory->payment_date) {
                            $dateIssuedToShow = $paymentHistory->payment_date->format('d/m/Y H:i');
                        }
                        ?>
                        <td><?= h($dateIssuedToShow) ?></td>
                    </tr>
                    <tr>
                        <th>Booking Date:</th>
                        <td><?= h($bookingDateFormatted) ?></td>
                    </tr>
                    <?php if ($bookingOverallStartTime && $bookingOverallEndTime): ?>
                    <tr>
                        <th>Booking Time:</th>
                        <td><?= h($bookingOverallStartTime) ?> - <?= h($bookingOverallEndTime) ?></td>
                    </tr>
                    <?php elseif ($bookingOverallStartTime): ?>
                     <tr>
                        <th>Booking Start Time:</th>
                        <td><?= h($bookingOverallStartTime) ?></td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>

            <div class="customer-details">
                <h4>Billed To:</h4>
                <p>
                    <?php 
                    $customerName = 'N/A';
                    if ($booking->customer && !empty(trim((string)$booking->customer->full_name)) && strtolower(trim((string)$booking->customer->full_name)) !== 'guest user') {
                        $customerName = h($booking->customer->full_name);
                    } elseif (!empty($booking->booking_name)) {
                        if (stripos($booking->booking_name, 'Booking for ') === 0) {
                            $extractedName = trim(substr($booking->booking_name, strlen('Booking for ')));
                            if (!empty($extractedName)) {
                                $customerName = h($extractedName);
                            }
                        } else {
                            $customerName = h($booking->booking_name); 
                        }
                    }
                    ?>
                    <?= $customerName ?><br>
                    <?= h($booking->customer->email ?? 'N/A') ?><br>
                    <?php if (!empty($booking->customer->phone_number)): ?>
                        Phone: <?= h($booking->customer->phone_number) ?><br>
                    <?php endif; ?>
                </p>
            </div>

            <div class="booking-summary">
                <h4>Services:</h4>
                <table>
                    <thead>
                        <tr>
                            <th>Service</th>
                            <th>Duration/Time</th>
                            <th style="text-align: right;">Cost</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($booking->bookings_services)): ?>
                            <?php foreach ($booking->bookings_services as $bs): ?>
                                <?php
                                    $serviceTimeInfo = 'N/A';
                                    if ($bs->start_time && $bs->end_time) {
                                        $serviceStartTime = $bs->start_time->format('h:i A');
                                        $serviceEndTime = $bs->end_time->format('h:i A');
                                        $serviceTimeInfo = $serviceStartTime . ' - ' . $serviceEndTime;
                                    }

                                    $stylistNameDisplay = 'N/A'; 
                                    if ($bs && !empty($bs->stylist)) {
                                        $fullName = trim((string)($bs->stylist->full_name ?? ''));
                                        $firstName = trim((string)($bs->stylist->first_name ?? ''));
                                        $lastName = trim((string)($bs->stylist->last_name ?? ''));

                                        $resolvedName = '';
                                        if (!empty($fullName) && !in_array(strtolower($fullName), ['unknown stylist', 'unknown', 'n/a', ''])) {
                                            $resolvedName = $fullName;
                                        } 
                                        else {
                                            $constructedName = trim($firstName . ' ' . $lastName);
                                            if (!empty($constructedName) && !in_array(strtolower($constructedName), ['unknown stylist', 'unknown', 'n/a', ''])) {
                                                if (!in_array(strtolower($firstName), ['unknown stylist', 'unknown', 'n/a', '']) && 
                                                    !in_array(strtolower($lastName), ['unknown stylist', 'unknown', 'n/a', ''])) {
                                                    $resolvedName = $constructedName;
                                                }
                                            }
                                        }

                                        if (!empty($resolvedName)) {
                                            $stylistNameDisplay = h($resolvedName);
                                        }
                                    }
                                ?>
                                <tr>
                                    <td>
                                        <?= h($bs->service->service_name ?? 'N/A') ?><br>
                                        
                                    </td>
                                    <td><?= h($serviceTimeInfo) ?></td>
                                    <td style="text-align: right;"><?= $this->Number->currency($bs->service_cost ?? 0, 'AUD') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="3">No services detailed for this booking.</td></tr>
                        <?php endif; ?>
                        
                        <?php if (!empty($isPdfContext) && !empty($isAdminEditNotification) && isset($changeDetails) && isset($editedPaymentHistory) && $editedPaymentHistory): ?>
                            <tr>
                                <td colspan="2" style="text-align:right;">Original Booking Total:</td>
                                <td style="text-align: right;"><?= $this->Number->currency($changeDetails['original_total'] ?? 0, 'AUD') ?></td>
                            </tr>

                            <?php if ($changeDetails['type'] === 'additional_payment_due'): ?>
                                <?php if (array_key_exists('paid_so_far', $changeDetails)): ?>
                                    <tr>
                                        <td colspan="2" style="text-align:right;">Amount Previously Paid:</td>
                                        <td style="text-align: right;"><?= $this->Number->currency($changeDetails['paid_so_far'], 'AUD') ?></td>
                                    </tr>
                                <?php endif; ?>
                                <tr>
                                    <td colspan="2" style="text-align:right;">New Booking Total:</td>
                                    <td style="text-align: right;"><?= $this->Number->currency($booking->total_cost ?? $changeDetails['new_total'] ?? 0, 'AUD') ?></td>
                                </tr>
                                <tr class="total-row">
                                    <td colspan="2" style="text-align:right;"><strong>Amount Due:</strong></td>
                                    <td style="text-align: right;"><strong><?= $this->Number->currency($changeDetails['amount'], 'AUD') ?></strong></td>
                                </tr>
                            <?php elseif ($changeDetails['type'] === 'refund_due'): ?>
                                <tr class="total-row" style="color: #155724; border-top: 1px solid #c3e6cb; background-color: #d4edda !important;">
                                    <td colspan="2" style="text-align:right;"><strong>Credit / Refund Due to You:</strong></td>
                                    <td style="text-align: right;"><strong><?= $this->Number->currency($changeDetails['amount'], 'AUD') ?></strong></td>
                                </tr>
                                <tr class="total-row">
                                    <td colspan="2" style="text-align:right;"><strong>New Booking Total (After Credit):</strong></td>
                                    <td style="text-align: right;"><strong><?= $this->Number->currency($booking->total_cost ?? 0, 'AUD') ?></strong></td>
                                </tr>
                            <?php endif; ?>

                        <?php else: ?>
                            <tr class="total-row">
                                <?php 
                                $showPaymentDue = $booking->status === 'Confirmed - Payment Due';
                                if (!$isPdfContext && !empty($isAdminEditNotification) && !empty($changeDetails) && $changeDetails['type'] === 'additional_payment_due' && $booking->remaining_cost > 0) {
                                    $showPaymentDue = true;
                                }

                                if ($showPaymentDue): ?>
                                    <td colspan="2" style="text-align:right;"><strong>Total Amount Due:</strong></td>
                                    <td style="text-align: right;"><strong><?= $this->Number->currency($booking->remaining_cost ?? $booking->total_cost ?? 0, 'AUD') ?></strong></td>
                                <?php else: ?>  
                                    <?php
                                        $amountToDisplay = 0;
                                        $basePaymentEntity = $editedPaymentHistory ?? $paymentHistory;
                                        if ($basePaymentEntity) {
                                            $amountToDisplay = $basePaymentEntity->payment_amount ?? 0;
                                        }
                                        
                                        $labelText = "Total Amount Paid:";
                                        if (!empty($isAdminEditNotification) && isset($changeDetails)) {
                                            if ($changeDetails['type'] === 'refund_due') {
                                                $labelText = "New Booking Total (After Refund):";
                                                $amountToDisplay = $booking->total_cost ?? 0;
                                            } elseif ($changeDetails['type'] === 'additional_payment_due') {
                                                $labelText = "New Booking Total:";
                                                $amountToDisplay = $booking->total_cost ?? 0;
                                            }
                                        } elseif ($booking->status !== 'Confirmed - Payment Due' && $paymentHistory) {
                                            // Standard paid invoice
                                             $amountToDisplay = $paymentHistory->payment_amount ?? 0;
                                        } elseif ($booking->status === 'Confirmed - Payment Due') {
                                            $labelText = "Total Booking Cost:";
                                            $amountToDisplay = $booking->total_cost ?? 0;
                                        }
                                    ?>
                                    <td colspan="2" style="text-align:right;"><strong><?= h($labelText) ?></strong></td>
                                    <td style="text-align: right;"><strong><?= $this->Number->currency($amountToDisplay, 'AUD') ?></strong></td>
                                <?php endif; ?>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if (!empty($isPdfContext) && !empty($isAdminEditNotification) && isset($changeDetails) && $changeDetails['type'] === 'refund_due'): ?>
            <div class="refund-details" style="padding: 15px; margin-bottom: 25px; border: 1px solid #eee; background-color: #f9f9f9; border-radius: 5px;">
                <h4 style="color: #d9534f; margin-top:0; border-bottom: 1px solid #eee; padding-bottom: 5px;">Credit Note / Refund Due</h4>
                <p style="font-size: 1.1em;">Due to recent changes to your booking (Ref #<?= h($booking->id) ?>), the following credit is due to you.</p>
                <p style="font-size: 1.2em; font-weight: bold;">
                    Credit Amount: <?= $this->Number->currency($changeDetails['amount'], 'AUD') ?>
                </p>
                <p>This amount will be processed by our team and returned to your original payment method. Please allow 5-7 business days for the transaction to reflect in your account once processed.</p>
                <p>Your new final booking total is: <?= $this->Number->currency($booking->total_cost ?? 0, 'AUD') ?></p>
            </div>
            <?php endif; ?>

            <?php 
            $displayPaymentLink = false;
            if (empty($isPdfContext) && $booking->status === 'Confirmed - Payment Due' && $booking->remaining_cost > 0) {
                $displayPaymentLink = true;
            }
            // Also show payment link in email if admin edit results in additional payment and there's a remaining balance
            if (empty($isPdfContext) && !empty($isAdminEditNotification) && isset($changeDetails) && $changeDetails['type'] === 'additional_payment_due' && $booking->remaining_cost > 0) {
                 $displayPaymentLink = true;
            }

            if ($displayPaymentLink): ?>
                <div class="payment-link" style="margin-top: 25px; padding: 15px; border: 1px solid #007bff; background-color: #e7f3ff; text-align: center; border-radius: 5px;">
                    <p style="margin-bottom: 10px; font-size: 1.1em;"><strong>Complete Your Booking Payment Online:</strong></p>
                    <?= $this->Html->link(
                        'Click Here to Pay via PayPal',
                        ['controller' => 'Bookings', 'action' => 'customerview', $booking->id, '_full' => true],
                        ['style' => 'display: inline-block; padding: 12px 25px; background-color: #005ea6; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 1.1em;']
                    ) ?>
                    <p style="margin-top: 10px; font-size: 0.9em; color: #555;">You will be redirected to the booking details page where you can complete the payment.</p>
                </div>
            <?php endif; ?>

            <?php 
            // Determine which payment history to show details for
            $paymentDetailsToShow = null;
            if (!empty($isAdminEditNotification) && isset($editedPaymentHistory) && $editedPaymentHistory) {
            } elseif ($booking->status !== 'Confirmed - Payment Due' && isset($paymentHistory) && $paymentHistory) {
                // For standard paid invoices, show the original payment history
                $paymentDetailsToShow = $paymentHistory; 
            }

            if ($paymentDetailsToShow): ?>
            <div class="payment-summary">
                <h4>Payment Details</h4>
                <p>
                    <strong>Transaction ID:</strong> <?= h($paymentDetailsToShow->paypal_transaction_id) ?><br>
                    <strong>Payment Method:</strong> <?= h($paymentDetailsToShow->payment_method) ?><br>
                    <strong>Payment Status:</strong> <?= h($paymentDetailsToShow->payment_status) ?>
                </p>
            </div>
            <?php endif; ?>


            <?php if (!empty($booking->notes)): ?>
            <div class="booking-notes">
                <h4>Notes:</h4>
                <p><?= nl2br(h($booking->notes)) ?></p>
            </div>
            <?php endif; ?>

            <div class="footer">
                <p>Thank you for your booking with <?= h($companyName) ?>!</p>
                <p>If you have any questions, please contact us at <?= h($companyEmail) ?> or call <?= h($companyPhone) ?>.</p>
            </div>
        </div>
    </div>
</body>
</html> 