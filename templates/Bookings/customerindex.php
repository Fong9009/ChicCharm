<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Booking> $bookings
 */
?>
<div class="custom-list-wrapper">
    <div class="row">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Back to Dashboard'), ['controller' => 'Customers', 'action' => 'dashboard'], ['class' => 'side-nav-item']) ?>
        </div>
        <div class="contacts index content">
            <div class="table-responsive">
                <div class="container">
                    <div class="row align-items-center">
                        <h3><?= __('My Bookings') ?></h3>
                    </div>
                </div>
                <table>
                    <thead>
                    <tr>
                        <th><?= $this->Paginator->sort('booking_name') ?></th>
                        <th><?= $this->Paginator->sort('booking_date') ?></th>
                        <th><?= __('Stylists') ?></th>
                        <th><?= __('Services') ?></th>
                        <th><?= $this->Paginator->sort('total_cost') ?></th>
                        <th><?= $this->Paginator->sort('remaining_cost') ?></th>
                        <th><?= $this->Paginator->sort('status') ?></th>
                        <th class="actions"><?= __('Actions') ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td><?= h($booking->booking_name)?></td>
                            <td>
                                <?php if ($booking->booking_date): ?>
                                    <?= h($booking->booking_date->format('Y-m-d')) ?><br>
                                <?php endif; ?>
                                <?php if ($booking->start_time && $booking->end_time): ?>
                                    <?= h($booking->start_time->format('H:i')) ?> - <?= h($booking->end_time->format('H:i')) ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php 
                                if (!empty($booking->bookings_stylists)): ?>
                                    <ul style="list-style: none; padding-left: 0;">
                                    <?php foreach ($booking->bookings_stylists as $bookingStylist): ?>
                                        <li><?= h($bookingStylist->stylist->first_name) ?> <?= h($bookingStylist->stylist->last_name) ?></li>
                                    <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <p>No stylists assigned</p>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($booking->services)): ?>
                                    <ul style="list-style: none; padding-left: 0;">
                                    <?php foreach ($booking->services as $service): ?>
                                        <li><?= h($service->service_name) ?> ($<?= $this->Number->format($service->service_cost) ?>)</li>
                                    <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </td>
                            <td>$<?= $this->Number->format($booking->total_cost) ?></td>
                            <td>$<?= $this->Number->format($booking->remaining_cost) ?></td>
                            <td><?= h($booking->status) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['action' => 'customerview', $booking->id], ['class' => 'button']) ?>
                                <?php if ($booking->status === 'active'): ?>
                                    <?= $this->Form->postLink(
                                        __('Cancel Booking'),
                                        ['action' => 'customerdelete', $booking->id],
                                        [
                                            'method' => 'delete',
                                            'confirm' => __('Are you sure you want to cancel this booking?'),
                                            'class' => 'button',
                                        ]
                                    ) ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?= $this->Html->link(__('New Booking'), ['action' => 'customerbooking'], ['class' => 'btn btn-primary']) ?>
            </div>
            <div class="paginator">
                <ul class="pagination">
                    <?= $this->Paginator->first('<< ' . __('first')) ?>
                    <?= $this->Paginator->prev('< ' . __('previous')) ?>
                    <?= $this->Paginator->numbers() ?>
                    <?= $this->Paginator->next(__('next') . ' >') ?>
                    <?= $this->Paginator->last(__('last') . ' >>') ?>
                </ul>
                <p><?= $this->Paginator->counter(__('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')) ?></p>
            </div>
        </div>
    </div>
</div>