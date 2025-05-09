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
                <div class="col-lg-4 col-md-6 col-sm-12 mb-3 side-nav-item">
                    <?= $this->Html->link(
                        '<div class="card h-100">
                            <div class="card-header new-card-header d-flex justify-content-between align-items-center flex-wrap">
                                <h4 class="view-card-h4 mb-0 flex-grow-1 text-truncate">Past Bookings</h4>
                                <i class="material-icons view-icon ms-2">history</i>
                            </div>
                            <div class="card-body new-card-body"></div>
                            <div class="card-footer new-card-footer">
                                <span class="mb-0 text-truncate">View Past Bookings</span>
                            </div>
                        </div>',
                        ['action' => 'adminPastBookings'],
                        ['escape' => false, 'class' => 'card-link-wrapper d-block text-decoration-none']
                    ) ?>
                </div>
            </div>
        </div>
    </aside>
    <div class="table-responsive mt-4">
        <div class="container">
            <div class="row align-items-center">
                <h3><?= __('Active Bookings') ?></h3>
            </div>
        </div>
        <table>
            <thead>
            <tr>
                <th><?= $this->Paginator->sort('booking_name') ?></th>
                <th><?= $this->Paginator->sort('booking_date') ?></th>
                <th><?= __('Stylists & Services') ?></th>
                <th><?= __('Notes') ?></th>
                <th><?= $this->Paginator->sort('total_cost') ?></th>
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
                    </td>
                    <td>
                        <?php if (!empty($booking->bookings_services)): ?>
                            <ul style="list-style: none; padding-left: 0;">
                            <?php 
                            // Group services by stylist
                            $stylistServices = [];
                            foreach ($booking->bookings_services as $bookingService) {
                                // Ensure service and stylist data are loaded
                                if (empty($bookingService->stylist) || empty($bookingService->service)) continue;
                                $stylistId = $bookingService->stylist->id;
                                if (!isset($stylistServices[$stylistId])) {
                                    $stylistServices[$stylistId] = [
                                        'stylist' => $bookingService->stylist,
                                        'booking_services' => [] 
                                    ];
                                }
                                $stylistServices[$stylistId]['booking_services'][] = $bookingService;
                            }
                            ?>
                            <?php foreach ($stylistServices as $stylistData): ?>
                                <li class="mb-2">
                                    <?= h($stylistData['stylist']->first_name) ?> 
                                    <?= h($stylistData['stylist']->last_name) ?>:
                                    <ul style="list-style: none; padding-left: 1rem; margin-top: 0.25rem;">
                                        <?php foreach ($stylistData['booking_services'] as $bookingService): ?>
                                            <li>
                                                <small>
                                                     <?= h($bookingService->service->service_name) ?> 
                                                    (<?= $this->Number->currency($bookingService->service->service_cost) ?>):
                                                    <?php if ($bookingService->start_time && $bookingService->end_time): ?>
                                                        <span class="service-time">
                                                            <?= h($bookingService->start_time->format('h:i A')) ?> - <?= h($bookingService->end_time->format('h:i A')) ?>
                                                        </span>
                                                    <?php elseif ($bookingService->start_time): ?>
                                                        <span class="service-time"><?= h($bookingService->start_time->format('h:i A')) ?></span>
                                                    <?php endif; ?>
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
                    <td style="word-wrap: break-word; overflow-wrap: break-word; white-space: normal; max-width: 250px;">
                        <?= !empty($booking->notes) ? nl2br(h($booking->notes)) : 'No notes' ?>
                    </td>
                    <td><?= $this->Number->currency($booking->total_cost) ?></td>
                    <td>
                        <?php 
                        $statusClassAdmin = ''; 
                        switch ($booking->status) {
                            case 'active':
                                $statusClassAdmin = 'active';
                                break;
                            case 'Confirmed - Payment Due':
                                $statusClassAdmin = 'payment-due';
                                break;
                            case 'Confirmed - Paid':
                                $statusClassAdmin = 'paid';
                                break;
                            case 'cancelled':
                                $statusClassAdmin = 'cancelled';
                                break;
                            case 'finished':
                                $statusClassAdmin = 'finished';
                                break;
                            default:
                                $statusClassAdmin = 'text-muted';
                        }
                        ?>
                        <span class="status-text <?= $statusClassAdmin ?>">
                            <?= strtoupper(h($booking->status)) ?>
                        </span>
                    </td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $booking->id], ['class' => 'button']) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $booking->id], ['class' => 'button']) ?>
                        <?= $this->Form->postLink(
                            __('Cancel'),
                            ['action' => 'delete', $booking->id],
                            [
                                'confirm' => __('Are you sure you want to cancel booking #{0} ({1})?', $booking->id, h($booking->booking_name)),
                                'class' => 'button btn-danger',
                            ]
                        ) ?>
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
