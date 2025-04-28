<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Booking $booking
 */
?>

<div class="booking-details-wrapper">
    <div class="booking-details">
        <h2><?= __('My Booking Details') ?></h2>

        <div class="booking-info">
            <div class="info-group">
                <label><?= __('Booking Date') ?></label>
                <p class="form-control-static">
                    <?= h($booking->booking_date->format('F j, Y')) ?>
                </p>
            </div>

            <?php if (!empty($booking->bookings_stylists)): ?>
            <div class="info-group">
                <label><?= __('Selected Stylists and Services') ?></label>
                <div class="stylists-list">
                    <?php foreach ($booking->bookings_stylists as $bookingStylist): ?>
                        <div class="stylist-item mb-3">
                            <p class="stylist-name mb-1">
                                <?= h($bookingStylist->stylist->first_name) ?> <?= h($bookingStylist->stylist->last_name) ?>:
                            </p>
                            <div class="services-under-stylist ps-3">
                                <?php
                                $stylistServices = collection($booking->bookings_services)
                                    ->filter(function ($bookingService) use ($bookingStylist) {
                                        return $bookingService->stylist_id === $bookingStylist->stylist_id;
                                    })
                                    ->toArray();

                                if (!empty($stylistServices)) {
                                    foreach ($stylistServices as $bookingService) {
                                        ?>
                                        <p class="service-detail mb-1">
                                            <?= h($bookingService->service->service_name) ?>
                                            (<?= $this->Number->currency($bookingService->service->service_cost) ?>):

                                            <?php if ($bookingService->start_time && $bookingService->end_time): ?>
                                                <span class="service-time">
                                                    <?= h($bookingService->start_time->format('h:i A')) ?> - <?= h($bookingService->end_time->format('h:i A')) ?>
                                                </span>
                                            <?php elseif ($bookingService->start_time): ?>
                                                <span class="service-time"><?= h($bookingService->start_time->format('h:i A')) ?></span>
                                            <?php else: ?>
                                                 <span class="text-muted">(Time not specified)</span>
                                            <?php endif; ?>
                                        </p>
                                        <?php
                                    }
                                } else {
                                    echo '<p class="text-muted ps-3">No services assigned to this stylist.</p>';
                                }
                                ?>
                            </div>
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
                <p class="form-control-static" style="word-wrap: break-word; overflow-wrap: break-word; white-space: normal;">
                    <?= !empty($booking->notes) ? nl2br(h($booking->notes)) : 'No Notes' ?>
                </p>
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
