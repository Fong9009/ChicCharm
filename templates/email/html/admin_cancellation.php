<?php
/**
 * Email template for admin cancellation of a non-paid booking.
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Booking $booking The cancelled booking entity.
 * @var string $customerName The customer's name.
 * @var string $companyName The company name.
 * @var string $companyPhone The company phone number.
 * @var string $companyEmail The company email address.
 */
?>
<p>Dear <?= h($customerName) ?>,</p>

<p>
    We are writing to inform you that your ChicCharm booking (Booking ID: <strong>#<?= h($booking->id) ?></strong>),
    scheduled for <strong><?= h($booking->booking_date->format('F j, Y')) ?></strong>, has been cancelled by our administration.
</p>

<p>
    If you did not request this cancellation or have any questions, please do not hesitate to contact us:
    <br>
    Email: <?= h($companyEmail) ?><br>
    Phone: <?= h($companyPhone) ?>
</p>

<p>
    We apologize for any inconvenience this may cause.
</p>

<p>
    Sincerely,<br>
    The <?= h($companyName) ?> Team
</p> 