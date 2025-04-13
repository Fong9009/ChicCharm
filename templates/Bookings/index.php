<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Booking> $bookings
 */
?>
<?= $this->Html->css('/utility/indexes/indexes.css') ?>
<?= $this->Html->css('https://fonts.googleapis.com/icon?family=Material+Icons') ?>

<div class="contacts index content">
    <!-- Action Menu -->
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <div class="row gx-2">
                <div class="col-lg-4 col-md-6 col-sm-12 mb-3 side-nav-item">
                    <?= $this->Html->link(
                        '<div class="card h-100">
                            <div class="card-header dashboard-card-header d-flex justify-content-between align-items-center flex-wrap">
                                <h4 class="view-card-h4 mb-0 flex-grow-1 text-truncate">Dashboard</h4>
                                <i class="material-icons view-icon ms-2">person</i>
                            </div>
                            <div class="card-body dashboard-card-body"></div>
                            <div class="card-footer dashboard-card-footer">
                                <span class="mb-0 text-truncate">Back To Dashboard</span>
                            </div>
                        </div>',
                        ['controller' => 'Admins', 'action' => 'dashboard'],
                        ['escape' => false, 'class' => 'card-link-wrapper d-block text-decoration-none']
                    ) ?>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12 mb-3 side-nav-item">
                    <?= $this->Html->link(
                        '<div class="card h-100">
                            <div class="card-header new-card-header d-flex justify-content-between align-items-center flex-wrap">
                                <h4 class="view-card-h4 mb-0 flex-grow-1 text-truncate">New Booking</h4>
                                <i class="material-icons view-icon ms-2">add</i>
                            </div>
                            <div class="card-body new-card-body"></div>
                            <div class="card-footer new-card-footer">
                                <span  class="mb-0 text-truncate">Add Booking</span>
                            </div>
                        </div>',
                        ['controller' => 'Bookings', 'action' => 'adminbooking'],
                        ['escape' => false, 'class' => 'card-link-wrapper d-block text-decoration-none']
                    ) ?>
                </div>
            </div>
        </div>
    <div class="table-responsive mt-4">
        <div class="container">
            <div class="row align-items-center">
                <h3><?= __('Bookings') ?></h3>
            </div>
        </div>
        <table>
            <thead>
            <tr>
                <th><?= $this->Paginator->sort('booking_name') ?></th>
                <th><?= $this->Paginator->sort('booking_date') ?></th>
                <th><?= __('Customer') ?></th>
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
                    <td><?= h($booking->booking_name) ?></td>
                    <td>
                        <?php if ($booking->booking_date): ?>
                            <?= h($booking->booking_date->format('Y-m-d')) ?><br>
                        <?php endif; ?>
                        <?php if ($booking->start_time && $booking->end_time): ?>
                            <?= h($booking->start_time->format('H:i')) ?> - <?= h($booking->end_time->format('H:i')) ?>
                        <?php endif; ?>
                    </td>
                    <td><?php if (empty($booking->customer->first_name) || empty($booking->customer->last_name)): ?>
                            <p>No Name Available</p>
                        <?php else: ?>
                            <?= h($booking->customer->first_name) ?> <?= h($booking->customer->last_name) ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (!empty($booking->stylists)): ?>
                            <ul style="list-style: none; padding-left: 0;">
                            <?php foreach ($booking->stylists as $stylist): ?>
                                <li><?= h($stylist->first_name) ?> <?= h($stylist->last_name) ?></li>
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
                        <?= $this->Html->link(__('View'), ['action' => 'view', $booking->id], ['class' => 'button']) ?>
                        <?= $this->Html->link(__('Add Stylist'), [
                            'controller' => 'bookingsStylists',
                            'action' => 'customerstylistadd', $booking->id], ['class' => 'button']) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $booking->id], ['class' => 'button']) ?>
                        <?php if ($booking->status === 'active'): ?>
                            <?= $this->Form->postLink(
                                __('Delete'),
                                ['action' => 'delete', $booking->id],
                                [
                                    'method' => 'delete',
                                    'confirm' => __('Are you sure you want to delete # {0}?', $booking->id),
                                    'class' => 'button',
                                ]
                            ) ?>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
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
