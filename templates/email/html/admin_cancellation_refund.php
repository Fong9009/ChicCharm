<?php
/**
 * Email template for admin cancellation of a paid booking with refund information.
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Booking $booking The cancelled booking entity.
 * @var float $refundAmount The amount to be refunded.
 * @var string $customerName The customer's name.
 * @var string $companyName The company name.
 * @var string $companyPhone The company phone number.
 * @var string $companyEmail The company email address.
 */

// Ensure refundAmount is formatted as currency
$formattedRefundAmount = $this->Number->currency($refundAmount, 'AUD');
?>
<p>Dear <?= h($customerName) ?>,</p>

<p>
    We are writing to confirm that your ChicCharm booking (Booking ID: <strong>#<?= h($booking->id) ?></strong>),
    scheduled for <strong><?= h($booking->booking_date->format('F j, Y')) ?></strong>, has now been cancelled as requested. Our team has processed this for you.
</p>

<p>
    A refund of <strong><?= $formattedRefundAmount ?></strong> is due to you for this cancellation. This amount will be processed back to your original payment method.
    Please allow a few business days for the refund to reflect in your account, depending on your bank or card issuer.
</p>

<p>
    If you have any questions or if there's anything else we can assist you with regarding this cancellation, please do not hesitate to contact us:
    <br>
    Email: <?= h($companyEmail) ?><br>
    Phone: <?= h($companyPhone) ?>
</p>

<p>
    Thank you for your understanding.
</p>

<p>
    Sincerely,<br>
    The <?= h($companyName) ?> Team
</p> 