<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Booking $booking
 * @var \App\Model\Entity\PaymentHistory $paymentHistory
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
$paymentDateFormatted = $paymentHistory->payment_date ? $paymentHistory->payment_date->format('d/m/Y H:i') : 'N/A';

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
    <div class="container">
        <div class="header">
            <h1><?= h($companyName) ?></h1>
            <p><?= nl2br(h($companyAddress)) ?><br>
               Phone: <?= h($companyPhone) ?> | Email: <?= h($companyEmail) ?><br>
               <?php if (!empty($companyABN)): ?>ABN: <?= h($companyABN) ?><?php endif; ?>
            </p>
            <h2>Invoice / Booking Confirmation</h2>
        </div>

        <div class="invoice-details">
            <h4>Details:</h4>
            <table>
                <tr>
                    <th>Invoice #:</th>
                    <td>PAY-<?= h($paymentHistory->id) ?></td>
                </tr>
                <tr>
                    <th>Booking Ref #:</th>
                    <td><?= h($booking->id) ?></td>
                </tr>
                <tr>
                    <th>Date Issued:</th>
                    <td><?= h($paymentDateFormatted) ?></td>
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
                                    <small class="text-muted">Stylist: <?= $stylistNameDisplay ?></small>
                                </td>
                                <td><?= h($serviceTimeInfo) ?></td>
                                <td style="text-align: right;"><?= $this->Number->currency($bs->service_cost ?? 0, 'AUD') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="3">No services detailed for this booking.</td></tr>
                    <?php endif; ?>
                    <tr class="total-row">
                        <td colspan="2" style="text-align:right;"><strong>Total Amount Paid:</strong></td>
                        <td style="text-align: right;"><strong><?= $this->Number->currency($paymentHistory->payment_amount ?? 0, 'AUD') ?></strong></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <?php if (!empty($booking->bookings_stylists)): ?>
        <div class="stylist-details">
            <h4>Stylists:</h4>
            <ul class="stylist-list">
                <?php foreach ($booking->bookings_stylists as $bookingStylist): ?>
                    <li><?= h($bookingStylist->stylist->full_name ?? 'N/A') ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <div class="payment-summary">
            <h4>Payment Details:</h4>
            <p>
                <strong>Transaction ID:</strong> <?= h($paymentHistory->paypal_transaction_id ?? 'N/A') ?><br>
                <strong>Payment Method:</strong> <?= h($paymentHistory->payment_method ?? 'N/A') ?><br>
                <strong>Payment Status:</strong> <?= h($paymentHistory->payment_status ?? 'N/A') ?>
            </p>
        </div>

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
</body>
</html> 