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
<?= strtoupper($companyName) ?>
<?= h($companyAddress) ?>
Phone: <?= h($companyPhone) ?> | Email: <?= h($companyEmail) ?>
<?php if (!empty($companyABN)): ?>ABN: <?= h($companyABN) ?><?php endif; ?>

INVOICE / BOOKING CONFIRMATION
==================================================

Details:
--------------------------------------------------
Invoice #: PAY-<?= h($paymentHistory->id) ?>
Booking Ref #: <?= h($booking->id) ?>
Date Issued: <?= h($paymentDateFormatted) ?>
Booking Date: <?= h($bookingDateFormatted) ?>
<?php if ($bookingOverallStartTime && $bookingOverallEndTime): ?>
Booking Time: <?= h($bookingOverallStartTime) ?> - <?= h($bookingOverallEndTime) ?>
<?php elseif ($bookingOverallStartTime): ?>
Booking Start Time: <?= h($bookingOverallStartTime) ?>
<?php endif; ?>

BILLED TO:
--------------------------------------------------
<?= h($booking->customer->full_name ?? 'N/A') ?>
<?= h($booking->customer->email ?? 'N/A') ?>
<?php if (!empty($booking->customer->phone_number)): ?>
Phone: <?= h($booking->customer->phone_number) ?>
<?php endif; ?>

SERVICES:
--------------------------------------------------
<?php if (!empty($booking->bookings_services)): ?>
<?php foreach ($booking->bookings_services as $bs): ?>
<?php
    $serviceTimeInfo = 'N/A';
    if ($bs->start_time && $bs->end_time) {
        $serviceStartTime = $bs->start_time->format('h:i A');
        $serviceEndTime = $bs->end_time->format('h:i A');
        $serviceTimeInfo = $serviceStartTime . ' - ' . $serviceEndTime;
    } elseif ($bs->service && $bs->service->duration) {
        $serviceTimeInfo = $bs->service->duration . ' mins';
    }
?>
- Service: <?= h($bs->service->service_name ?? 'N/A') ?>
  Time/Duration: <?= h($serviceTimeInfo) ?>
  Cost: <?= $this->Number->currency($bs->service_cost ?? 0, 'AUD') ?>

<?php endforeach; ?>
<?php else: ?>
No services detailed for this booking.
<?php endif; ?>

<?php if (!empty($booking->bookings_stylists)): ?>
STYLISTS:
--------------------------------------------------
<?php foreach ($booking->bookings_stylists as $bookingStylist): ?>
- <?= h($bookingStylist->stylist->full_name ?? 'N/A') ?>
<?php endforeach; ?>

<?php endif; ?>
TOTAL AMOUNT PAID: <?= $this->Number->currency($paymentHistory->payment_amount ?? 0, 'AUD') ?>

PAYMENT DETAILS:
--------------------------------------------------
Transaction ID: <?= h($paymentHistory->paypal_transaction_id ?? 'N/A') ?>
Payment Method: <?= h($paymentHistory->payment_method ?? 'N/A') ?>
Payment Status: <?= h($paymentHistory->payment_status ?? 'N/A') ?>

<?php if (!empty($booking->notes)): ?>
NOTES:
--------------------------------------------------
<?= h($booking->notes) ?>
<?php endif; ?>

==================================================
Thank you for your booking with <?= h($companyName) ?>!

If you have any questions, please contact us at <?= h($companyEmail) ?> or call <?= h($companyPhone) ?>. 