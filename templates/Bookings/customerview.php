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
                <label><?= __('Booking Date') ?></label>
                <p class="form-control-static"><?= h($booking->booking_date->format('F j, Y')) ?></p>
            </div>

            <?php if (!empty($booking->stylists)): ?>
            <div class="info-group">
                <label><?= __('Selected Stylists') ?></label>
                <div class="stylists-list">
                    <?php foreach ($booking->stylists as $stylist): ?>
                        <div class="stylist-item">
                            <p><?= h($stylist->first_name) ?> <?= h($stylist->last_name) ?></p>
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
                <label><?= __('Remaining Cost') ?></label>
                <p class="form-control-static">$<?= number_format($booking->remaining_cost, 2) ?></p>
            </div>
        </div>

        <div class="actions">
            <?= $this->Html->link(
                __('Back to Dashboard'),
                ['controller' => 'Customers', 'action' => 'dashboard'],
                ['class' => 'btn btn-secondary']
            ) ?>
            <?= $this->Html->link(
                __('Add Stylist'),
                ['controller' => 'BookingsStylists', 'action' => 'customerstylistadd', $booking->id],
                ['class' => 'btn btn-primary']
            ) ?>
            <?= $this->Form->postLink(
                __('Delete Booking'),
                ['action' => 'customerdelete', $booking->id],
                [
                    'confirm' => __('Are you sure you want to delete this booking?'),
                    'class' => 'btn btn-danger'
                ]
            ) ?>
        </div>
    </div>
</div>
