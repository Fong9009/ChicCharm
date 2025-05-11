<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Booking> $bookings
 */
?>
<?= $this->Html->css('/utility/indexes/indexes.css') ?>
<?= $this->Html->css('/dashboard/profiledash.css') ?>
<?= $this->Html->css('https://fonts.googleapis.com/icon?family=Material+Icons') ?>
<?= $this->Html->css('booking-cards.css') ?>
<div class="customer-index-dashboard" style="background-image: url(<?= $this->Url->image('customerbackground.jpg')?>);">
    <div class="container" style="margin-top: -5px">
        <div class="card" style=" border-radius: 1rem;">
            <div class="card-body background-colour">
                <div class="card h-100 mb-4">
                    <div class="card-header d-flex justify-content-between customer-card-header" style="background-color:#D7CCC8">
                        <h2 class="fw-bold">Actions</h2>
                    </div>
                    <?= $this->Flash->render() ?>
                    <div class="card-body">
                        <div class="row mb-4 p-1">
                            <div class="w-100" style="height: 2px; background-color: #6c757d;"></div>
                        </div>
                        <div class="row gx-2">
                            <div class="col-lg-4 col-md-6 col-sm-12 mb-3 side-nav-item">
                                <?= $this->Html->link(
                                    '<div class="card h-100">
                                    <div class="card-header homepage-card-header d-flex justify-content-between align-items-center flex-wrap">
                                        <h4 class="view-card-h4 mb-0 flex-grow-1 text-truncate">Homepage</h4>
                                        <i class="material-icons view-icon ms-2">home</i>
                                    </div>
                                    <div class="card-body homepage-card-body"></div>
                                    <div class="card-footer homepage-card-footer">
                                        <span class="mb-0 text-truncate">Back To homepage</span>
                                    </div>
                                </div>',
                                    ['controller' => 'Pages', 'action' => 'display'],
                                    ['escape' => false, 'class' => 'card-link-wrapper d-block text-decoration-none']
                                ) ?>
                            </div>
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
                                    ['action' => 'customerPastBookings'],
                                    ['escape' => false, 'class' => 'card-link-wrapper d-block text-decoration-none']
                                ) ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 mb-4">
                        <div class="card h-100 mb-4">
                            <div class="card-header d-flex justify-content-between customer-card-header" style="background-color:#D7CCC8">
                                <h2 class="fw-bold">All My Bookings</h2>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive mt-4">
                                    <table class="dashboard-table">
                                        <thead>
                                            <tr>
                                                <th><?= $this->Paginator->sort('booking_date & time') ?></th>
                                                <th><?= __('Stylists & Services') ?></th>
                                                <th><?= $this->Paginator->sort('total_cost') ?></th>
                                                <th><?= __('Notes') ?></th>
                                                <th><?= __('Status') ?></th>
                                                <th class="actions"><?= __('Actions') ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($bookings as $booking): ?>
                                            <tr>
                                                <td>
                                                    <?php if ($booking->booking_date): ?>
                                                        <?= h($booking->booking_date->format('d/m/Y')) ?><br>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (!empty($booking->bookings_services)): ?>
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
                                                        <ul style="list-style: none; padding-left: 0;">
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
                                                                                    <?php // Time Slot ?>
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
                                                <td><?= $this->Number->currency($booking->total_cost) ?></td>
                                                <td style="word-wrap: break-word; overflow-wrap: break-word; white-space: normal;">
                                                    <?= !empty($booking->notes) ? h($booking->notes) : 'No notes' ?>
                                                </td>
                                                <td>
                                                    <?php 
                                                    $statusClass = '';
                                                    switch ($booking->status) {
                                                        case 'active':
                                                            $statusClass = 'active';
                                                            break;
                                                        case 'Confirmed - Payment Due':
                                                            $statusClass = 'payment-due';
                                                            break;
                                                        case 'Confirmed - Paid':
                                                            $statusClass = 'paid';
                                                            break;
                                                        case 'cancelled':
                                                            $statusClass = 'cancelled';
                                                            break;
                                                        case 'finished':
                                                            $statusClass = 'finished';
                                                            break;
                                                        default:
                                                            $statusClass = 'text-muted';
                                                    }
                                                    ?>
                                                    <span class="status-text <?= $statusClass ?>">
                                                        <?= strtoupper(h($booking->status)) ?>
                                                    </span>
                                                </td>
                                                <td class="actions">
                                                    <?= $this->Html->link(
                                                        'View',
                                                        ['action' => 'customerview', $booking->id],
                                                        ['class' => 'button', 'style' => 'background-color: #59B3B3; border-color: #59B3B3; transition: background-color 0.2s;', 
                                                        'onmouseover' => 'this.style.backgroundColor="#4A9595"; this.style.borderColor="#4A9595"', 
                                                        'onmouseout' => 'this.style.backgroundColor="#59B3B3"; this.style.borderColor="#59B3B3"'
                                                        ]
                                                    ) ?>

                                                    <?php if ($booking->status === 'Confirmed - Paid'): ?>
                                                        <?php if (!empty($booking->latest_payment_history) && !empty($booking->latest_payment_history->invoice_pdf)):
                                                            echo $this->Html->link(
                                                                __('Download/Check Invoice'),
                                                                '/' . h($booking->latest_payment_history->invoice_pdf),
                                                                [
                                                                    'class' => 'button button-outline',
                                                                    'style' => 'background-color: #6c757d; border-color: #6c757d; color: white; margin-top: 5px;',
                                                                    'target' => '_blank'
                                                                ]
                                                            );
                                                        endif; ?>
                                                        <p class="text-muted small mb-0 mt-2" style="white-space: normal;">This booking is paid. Contact store for changes.</p>
                                                    <?php else: ?>
                                                        <?php 
                                                        $statusAllowsActionsIndex = in_array($booking->status, ['active', 'Confirmed - Payment Due']);
                                                        $interactionAllowedByIndex = true;

                                                        if ($statusAllowsActionsIndex) {
                                                            try {
                                                                $bookingDateTimeIndex = new \Cake\I18n\FrozenTime($booking->booking_date->format('d-m-Y') . ' ' . ($booking->start_time ? $booking->start_time->format('H:i:s') : '00:00:00'));
                                                                if ($bookingDateTimeIndex < (new \Cake\I18n\FrozenTime())->addHours(24)) {
                                                                    $interactionAllowedByIndex = false;
                                                                }
                                                            } catch (Exception $e) {
                                                                $interactionAllowedByIndex = false;
                                                            }
                                                        }
                                                        ?>

                                                        <?php if ($statusAllowsActionsIndex): ?>
                                                            <?php if ($interactionAllowedByIndex): ?>
                                                                <?= $this->Html->link(__('Edit'), ['action' => 'customeredit', $booking->id], [
                                                                    'class' => 'button',
                                                                    'style' => 'background-color: #007bff; border-color: #007bff; color: white; transition: background-color 0.2s, border-color 0.2s;',
                                                                    'onmouseover' => 'this.style.backgroundColor="#0056b3"; this.style.borderColor="#0056b3"',
                                                                    'onmouseout' => 'this.style.backgroundColor="#007bff"; this.style.borderColor="#007bff"'
                                                                ]) ?>
                                                                <?= $this->Form->postLink(
                                                                    __('Cancel'),
                                                                    ['action' => 'customerdelete', $booking->id],
                                                                    [
                                                                        'method' => 'delete',
                                                                        'confirm' => __('Are you sure you want to cancel this booking?'),
                                                                        'class' => 'button',
                                                                        'style' => 'background-color: #dc3545; border-color: #dc3545; transition: background-color 0.2s;',
                                                                        'onmouseover' => 'this.style.backgroundColor="#bb2d3b"; this.style.borderColor="#bb2d3b"',
                                                                        'onmouseout' => 'this.style.backgroundColor="#dc3545"; this.style.borderColor="#dc3545"'
                                                                    ]
                                                                ) ?>
                                                            <?php else: ?>
                                                                <?= $this->Html->link(__('Edit'), ['action' => 'customeredit', $booking->id], [
                                                                    'class' => 'button',
                                                                    'style' => 'background-color: #007bff; border-color: #007bff; color: white; transition: background-color 0.2s, border-color 0.2s;',
                                                                    'onmouseover' => 'this.style.backgroundColor="#0056b3"; this.style.borderColor="#0056b3"',
                                                                    'onmouseout' => 'this.style.backgroundColor="#007bff"; this.style.borderColor="#007bff"'
                                                                ]) ?>
                                                                <p class="text-muted small mb-0" style="white-space: normal;">Cannot cancel (within 24h).</p>
                                                            <?php endif; ?>
                                                        <?php endif; ?>
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
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
