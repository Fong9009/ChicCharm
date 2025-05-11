<?php
/**
 * @var \App\View\AppView $this
 * @var array $bookingData
 */
?>
<div class="login-wrapper">
    <div class="container mt-5 bookings view-pending-guest-booking">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-7">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0"><?= __('Pending Booking Details') ?></h3>
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

                        <?php 
                        if (!empty($bookingData['pending_booking_token'])): 
                            $bookingViewUrl = $this->Url->build(['controller' => 'Bookings', 'action' => 'viewPendingGuestBooking', $bookingData['pending_booking_token']], ['fullBase' => true]);
                        ?>
                        <div class="mt-4 pt-3 border-top">
                            <h5><?= __('Link to Your Pending Booking:') ?></h5>
                            <p><small><?= $this->Html->link($bookingViewUrl, $bookingViewUrl, ['target' => '_blank', 'rel' => 'noopener noreferrer']) ?></small></p>
                            <p class="text-muted"><small><?= __('You can use this link to return to this page and complete your payment, as long as your browser session is active or the booking has not expired.') ?></small></p>
                        </div>
                        <?php else: ?>
                        <?php 
                            // Fallback or debugging: If somehow pending_booking_token is not set in bookingData for this view
                            // This part is optional and mainly for graceful degradation or if old sessions exist without the token.
                            // Log::warning('[ViewPendingGuestBooking] pending_booking_token was not found in bookingData when trying to generate a link.'); 
                        ?>
                        <?php endif; ?>

                        <div class="mt-4">
                            <p class="text-muted">Please complete your payment to confirm this booking.</p>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <?= $this->Html->link(
                            __('Proceed to Payment'),
                            ['controller' => 'Payments', 'action' => 'processGuestPayment'],
                            ['class' => 'btn btn-primary']
                        ) ?>
                        <?= $this->Html->link(
                            __('Edit Booking'),
                            ['controller' => 'Bookings', 'action' => 'guestbooking'],
                            ['class' => 'btn btn-secondary ms-2']
                        ) ?>
                        <?= $this->Html->link(
                            __('Cancel Booking'),
                            ['controller' => 'Auth', 'action' => 'guestcancel'],
                            ['class' => 'btn btn-danger ms-2']
                        ) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 