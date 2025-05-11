<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Booking $booking
 */
?>
<div class="login-wrapper">
    <div class="container mt-5 payments guest-payment-success">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-7">
                <div class="card text-center">
                    <div class="card-header">
                        <h3 class="mb-0"><i class="fas fa-check-circle text-success me-2"></i><?= __('Booking Confirmed!') ?></h3>
                    </div>
                    <div class="card-body">
                        <p class="lead"><?= __('Thank you, your payment was successful and your booking has been confirmed.') ?></p>
                        
                        <?php if ($booking) : ?>
                            <h5 class="mt-4 mb-3">Booking Details:</h5>
                            <div style="text-align: left; max-width: 400px; margin: 0 auto;">
                                <p><strong>Name:</strong> <?= h($booking->booking_name) ?></p>
                                <p><strong>Date:</strong> <?= h($booking->booking_date->format('l, F jS, Y')) ?></p>
                                <?php if ($booking->start_time): ?>
                                    <p><strong>Time:</strong> <?= h($booking->start_time->format('h:i A')) ?>
                                        <?php if ($booking->end_time): ?>
                                            - <?= h($booking->end_time->format('h:i A')) ?>
                                        <?php endif; ?>
                                    </p>
                                <?php endif; ?>
                                <p><strong>Total Paid:</strong> <?= $this->Number->currency($booking->total_cost) ?></p>

                                <?php if (!empty($booking->bookings_services)): ?>
                                    <h6 class="mt-3">Services Booked:</h6>
                                    <ul class="list-unstyled">
                                        <?php foreach ($booking->bookings_services as $bs): ?>
                                            <li>
                                                <?= h($bs->service->service_name) ?>
                                                <?php if ($bs->stylist): ?>
                                                    with <?= h($bs->stylist->first_name) ?>
                                                <?php endif; ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </div>
                            <p class="mt-4 text-muted small">
                                You will receive a confirmation email shortly.
                                Please keep your Booking ID for reference.
                            </p>
                        <?php else: ?>
                            <p class="text-danger mt-4"><?= __('Could not retrieve full booking details at this time, but your payment was successful.') ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer">
                        <?= $this->Html->link(__('Return to Homepage'), '/', ['class' => 'btn btn-primary']) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 