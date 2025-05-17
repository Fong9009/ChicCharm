<?php
/**
 * @var \App\View\AppView $this
 * @var array $bookingData
 * @var string $clientId PayPal Client ID // Added for payment
 * @var string $mode PayPal mode // Added for payment
 * @var string $paymentAmount // Added for payment
 * @var string $currencyCode // Added for payment
 * @var string $finalSuccessUrl // Added for payment
 * @var string $finalCancelUrl // Added for payment
 */
?>
<div class="login-wrapper">
    <div class="container mt-5 bookings view-pending-guest-booking">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-7">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0"><?= __('Confirm Your Booking & Pay') ?></h3>
                    </div>
                    <div class="card-body">
                        <div class="booking-summary">
                            <h4 class="mb-3">Booking Summary</h4>
                            <p><strong>Total Amount Due:</strong> <?= $this->Number->currency($bookingData['total_cost']) ?></p>

                            <?php if (isset($bookingData['booking_date'])) : ?>
                                <p><strong>Date:</strong> <?= h((new \Cake\I18n\FrozenDate($bookingData['booking_date']))->format('d/m/Y')) ?></p>
                            <?php endif; ?>

                            <?php if (!empty($bookingData['bookings_services_summary'])): ?>
                                <h5 class="mt-4 mb-2">Selected Services:</h5>
                                <ul class="list-unstyled">
                                    <?php foreach ($bookingData['bookings_services_summary'] as $item): ?>
                                        <li class="mb-2">
                                            <strong><?= h($item['service_name']) ?></strong>
                                            <?php if (!empty($item['stylist_name']) && $item['stylist_name'] !== 'Unknown Stylist'): ?>
                                                with <?= h($item['stylist_name']) ?>
                                            <?php endif; ?>
                                            <?php if (isset($item['start_time_formatted'])): ?>
                                                at <?= h((new \Cake\I18n\FrozenTime($item['start_time_formatted']))->format('h:i A')) ?>
                                            <?php endif; ?>
                                            (<?= $this->Number->currency($item['service_cost']) ?>)
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>

                            <?php if (!empty($bookingData['notes'])): ?>
                                <div class="mt-4">
                                    <h5>Notes:</h5>
                                    <p><?= nl2br(h($bookingData['notes'])) ?></p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <hr class="my-4">

                        <h5 class="mb-3">Payment</h5>
                        <p>Please complete your payment using PayPal to confirm your booking.</p>

                        <div id="paypal-button-container" style="max-width: 400px; margin: 20px auto;"></div>

                        <?php
                        // Render the PayPal payment element using variables set in BookingsController
                        echo $this->element('Bookings/paypal_payment', compact(
                            'paymentAmount',
                            'currencyCode',
                            'finalSuccessUrl',
                            'finalCancelUrl'
                            // 'clientId' and 'mode' are typically handled within the paypal_payment element itself or via JS config
                        ));
                        ?>

                        <p class="text-center text-muted small mt-3">
                            You will be redirected to PayPal to complete your payment securely.
                        </p>

                        <?php
                        if (!empty($bookingData['pending_booking_token'])): 
                            $bookingViewUrl = $this->Url->build(['controller' => 'Bookings', 'action' => 'viewPendingGuestBooking', $bookingData['pending_booking_token']], ['fullBase' => true]);
                        ?>
                        <div class="mt-4 pt-3 border-top">
                            <h5><?= __('Link to Your Pending Booking:') ?></h5>
                            <p><small><?= $this->Html->link($bookingViewUrl, $bookingViewUrl, ['target' => '_blank', 'rel' => 'noopener noreferrer']) ?></small></p>
                            <p class="text-muted"><small><?= __('You can use this link to return to this page and complete your payment, as long as your browser session is active or the booking has not expired.') ?></small></p>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer text-center">
                        <?= $this->Html->link(
                            __('Edit Booking Details'), // Changed from 'Edit Booking'
                            ['controller' => 'Bookings', 'action' => 'guestbooking'],
                            ['class' => 'btn btn-outline-secondary btn-sm']
                        ) ?>
                        <?= $this->Html->link(
                            __('Cancel Booking & Return to Form'), // Changed from 'Cancel Booking'
                            // Assuming AuthController::guestcancel clears session and redirects
                            // If not, this might need to go to a Bookings action that clears session and redirects to guestbooking form
                            ['controller' => 'Auth', 'action' => 'guestcancel'], 
                            ['class' => 'btn btn-outline-danger btn-sm ms-2', 'confirm' => __('Are you sure you want to cancel this pending booking?')]
                        ) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 