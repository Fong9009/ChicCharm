<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Booking $booking
 */
?>
<div class="booking-details-wrapper">
    <div class="booking-details">
        <h2><?= __('Booking Details') ?></h2>

        <div class="booking-info">
            <div class="info-group">
                <label><?= __('Booking Name') ?></label>
                <p class="form-control-static"><?= h($booking->booking_name) ?></p>
            </div>

            <div class="info-group">
                <label><?= __('Booking Date & Time') ?></label>
                <p class="form-control-static">
                    <?= h($booking->booking_date->format('F j, Y')) ?><br>
                    <?php if ($booking->start_time && $booking->end_time): ?>
                        <?= h($booking->start_time->format('g:i A')) ?> - <?= h($booking->end_time->format('g:i A')) ?>
                    <?php endif; ?>
                </p>
            </div>

            <?php if (!empty($booking->bookings_stylists)): ?>
            <div class="info-group">
                <label><?= __('Selected Stylists and Services') ?></label>
                <div class="stylists-list">
                    <?php foreach ($booking->bookings_stylists as $bookingStylist): ?>
                        <div class="stylist-item">
                            <p class="stylist-service">
                                <strong><?= h($bookingStylist->stylist->first_name) ?> <?= h($bookingStylist->stylist->last_name) ?></strong>:
                                <?php
                                $stylistServices = collection($booking->bookings_services)
                                    ->filter(function ($bookingService) use ($bookingStylist) {
                                        return $bookingService->stylist_id === $bookingStylist->stylist_id;
                                    })
                                    ->toArray();

                                if (!empty($stylistServices)) {
                                    $serviceNames = [];
                                    foreach ($stylistServices as $bookingService) {
                                        $serviceNames[] = h($bookingService->service->service_name);
                                    }
                                    echo implode(', ', $serviceNames);
                                } else {
                                    echo 'No services assigned';
                                }
                                ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <div class="info-group">
                <label><?= __('Total Cost') ?></label>
                <p class="form-control-static">$<?= number_format($booking->total_cost, 2) ?></p>
            </div>
            <div class="info-group">
                <label><?= __('Booking Notes') ?></label>
                <p class="form-control-static"><?= h($booking->notes) ?></p>
            </div>
        </div>

        <div class="actions">
            <?= $this->Html->link(
                __('Back to Dashboard'),
                ['controller' => 'Customers', 'action' => 'dashboard'],
                ['class' => 'btn btn-secondary']
            ) ?>
            <?php if ($booking->status === 'active'): ?>
                <?= $this->Form->postLink(
                    __('Cancel Booking'),
                    ['action' => 'customerdelete', $booking->id],
                    [
                        'method' => 'delete',
                        'confirm' => __('Are you sure you want to cancel this booking?'),
                        'class' => 'btn btn-danger',
                    ]
                ) ?>
            <?php endif; ?>
        </div>
    </div>
</div>
