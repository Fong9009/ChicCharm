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
                        ['controller' => 'Customers', 'action' => 'dashboard'],
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
                        ['controller' => 'Bookings', 'action' => 'customerbooking'],
                        ['escape' => false, 'class' => 'card-link-wrapper d-block text-decoration-none']
                    ) ?>
                </div>
            </div>
        </div>
    </aside>
    <div class="table-responsive mt-4">
        <div class="container">
            <div class="row align-items-center">
                <h3><?= __('My Bookings') ?></h3>
            </div>
        </div>
        <table>
            <thead>
            <tr>
                <th><?= $this->Paginator->sort('booking_date') ?></th>
                <th><?= __('Services & Stylists') ?></th>
                <th><?= $this->Paginator->sort('total_cost') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($bookings as $booking): ?>
                <tr>
                    <td>
                        <?php if ($booking->booking_date): ?>
                            <?= h($booking->booking_date->format('Y-m-d')) ?><br>
                        <?php endif; ?>
                        <?php if ($booking->start_time && $booking->end_time): ?>
                            <?= h($booking->start_time->format('g:i A')) ?> - <?= h($booking->end_time->format('g:i A')) ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (!empty($booking->bookings_services)): ?>
                            <?php 
                            // Group services by stylist
                            $stylistServices = [];
                            foreach ($booking->bookings_services as $bookingService) {
                                $stylistId = $bookingService->stylist->id;
                                if (!isset($stylistServices[$stylistId])) {
                                    $stylistServices[$stylistId] = [
                                        'stylist' => $bookingService->stylist,
                                        'services' => []
                                    ];
                                }
                                $stylistServices[$stylistId]['services'][] = $bookingService->service;
                            }
                            ?>
                            <ul style="list-style: none; padding-left: 0;">
                            <?php foreach ($stylistServices as $stylistData): ?>
                                <li class="mb-2">
                                    <?= h($stylistData['stylist']->first_name) ?> 
                                    <?= h($stylistData['stylist']->last_name) ?>
                                    <ul style="list-style: none; padding-left: 1rem; margin-top: 0.25rem;">
                                        <?php foreach ($stylistData['services'] as $service): ?>
                                            <li>
                                                <small>
                                                    • <?= h($service->service_name) ?> 
                                                    (<?= $this->Number->currency($service->service_cost) ?>)
                                                </small>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </li>
                            <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p>No services booked</p>
                        <?php endif; ?>
                    </td>
                    <td><?= $this->Number->currency($booking->total_cost) ?></td>
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
                        <?php else: ?>
                            <span class="text-muted small">Booking is already cancelled</span>
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

