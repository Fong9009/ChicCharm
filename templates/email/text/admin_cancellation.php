<?php
/**
 * Text Email template for admin cancellation of a non-paid booking.
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Booking $booking The cancelled booking entity.
 * @var string $customerName The customer's name.
 * @var string $companyName The company name.
 * @var string $companyPhone The company phone number.
 * @var string $companyEmail The company email address.
 */
?>
Dear <?= h($customerName) ?>,

We are writing to inform you that your ChicCharm booking (Booking ID: #<?= h($booking->id) ?>),
scheduled for <?= h($booking->booking_date->format('F j, Y')) ?>, has been cancelled by our administration.

If you did not request this cancellation or have any questions, please do not hesitate to contact us:
Email: <?= h($companyEmail) ?>
Phone: <?= h($companyPhone) ?>

We apologize for any inconvenience this may cause.

Sincerely,
The <?= h($companyName) ?> Team 