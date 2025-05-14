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
                                                    $displayStatus = h($booking->status);
                                                    $statusClass = '';
                                                    $isRefundProcessed = false;

                                                    // Check if a refund was processed for this booking
                                                    if (!empty($booking->payment_histories)) {
                                                        foreach ($booking->payment_histories as $ph) {
                                                            if ($ph->payment_method === 'Admin Adjustment' && $ph->payment_status === 'Refunded - Admin Processed') {
                                                                $isRefundProcessed = true;
                                                                break;
                                                            }
                                                        }
                                                    }

                                                    if ($booking->refund_due_amount > 0) {
                                                        // Combine original status with refund pending info
                                                        $displayStatus = strtoupper(h($booking->status)) . ' <br><span style="font-size:0.9em; color:white;">(Refund Pending: ' . $this->Number->currency($booking->refund_due_amount, 'AUD') . ')</span>';
                                                        switch ($booking->status) {
                                                            case 'active': $statusClass = 'active refund-pending'; break;
                                                            case 'Confirmed - Payment Due': $statusClass = 'payment-due refund-pending'; break;
                                                            case 'Confirmed - Paid': $statusClass = 'paid refund-pending'; break;
                                                            default: $statusClass = 'text-muted refund-pending';
                                                        }
                                                    } elseif ($isRefundProcessed) { 
                                                        $displayStatus = strtoupper(h($booking->status)) . ' <br><span style="font-size:0.9em; color:white;">(Refund Processed)</span>';
                                                        switch ($booking->status) {
                                                            case 'active': $statusClass = 'active refund-processed'; break;
                                                            case 'Confirmed - Paid': $statusClass = 'paid refund-processed'; break;
                                                            default: $statusClass = 'text-muted refund-processed';
                                                        }
                                                    } else {
                                                        // Original status and class logic if no refund aspect
                                                        $displayStatus = strtoupper(h($booking->status));
                                                        switch ($booking->status) {
                                                            case 'active': $statusClass = 'active'; break;
                                                            case 'Confirmed - Payment Due': $statusClass = 'payment-due'; break;
                                                            case 'Confirmed - Paid': $statusClass = 'paid'; break;
                                                            case 'cancelled': $statusClass = 'cancelled'; break;
                                                            case 'finished': $statusClass = 'finished'; break;
                                                            default: $statusClass = 'text-muted';
                                                        }
                                                    }
                                                    ?>
                                                    <span class="status-text <?= $statusClass ?>">
                                                        <?= $displayStatus 
                                                        ?>
                                                    </span>
                                                </td>
                                                <td class="actions">
                                                    <?= $this->Html->link(
                                                        'View/Pay',
                                                        ['action' => 'customerview', $booking->id],
                                                        ['class' => 'button', 'style' => 'background-color: #59B3B3; border-color: #59B3B3; transition: background-color 0.2s;',
                                                        'onmouseover' => 'this.style.backgroundColor="#4A9595"; this.style.borderColor="#4A9595"',
                                                        'onmouseout' => 'this.style.backgroundColor="#59B3B3"; this.style.borderColor="#59B3B3"'
                                                        ]
                                                    ) ?>

                                                    <?php
                                                    /* 
                                                    if ($booking->status === 'Confirmed - Paid' || $booking->status === 'Confirmed - Payment Due') {
                                                        if (!empty($booking->payment_histories)) {
                                                            // Find the latest payment history entry, assuming it's the most relevant
                                                            $latestPaymentHistory = $booking->payment_histories[0]; // Assuming sorted by date DESC
                                                            echo $this->Html->link(
                                                                'Check/Download Invoice',
                                                                ['controller' => 'Payments', 'action' => 'viewInvoice', $latestPaymentHistory->id],
                                                                ['class' => 'button button-outline', 'style' => 'margin-left: 5px;', 'target' => '_blank']
                                                            );
                                                        } else {
                                                            // This case might occur if a booking is 'Confirmed - Payment Due' but has no payment_histories yet (e.g. cash payment pending)
                                                            // Or if data is inconsistent. For now, don't show the link.
                                                            // echo $this->Html->tag('span', 'Invoice N/A (No Payment History)', ['class' => 'text-muted', 'style' => 'margin-left: 5px;']);
                                                        }
                                                    }
                                                    */
                                                    ?>

                                                    <?php // Message for paid bookings OR Edit/Cancel buttons for other statuses
                                                    if ($booking->status === 'Confirmed - Paid'): ?>
                                                        <p class="text-muted small mb-0 mt-2" style="white-space: normal;">This booking is paid. Contact store for changes.</p>
                                                    <?php else: ?>
                                                        <?php
                                                        $statusAllowsActionsIndex = in_array($booking->status, ['active', 'Confirmed - Payment Due']);
                                                        $interactionAllowedByTimeIndex = true;
                                                        $cancellationMessageIndex = "";
                                                        $cancellationCutoffHoursIndex = 1; // Hours before booking to restrict cancellation
                                                        $editCutoffHoursIndex = 1;       // Hours before booking to restrict editing

                                                        if ($statusAllowsActionsIndex) {
                                                            try {
                                                                $earliestStartTimeIndex = null;
                                                                if (!empty($booking->bookings_services)) {
                                                                    foreach ($booking->bookings_services as $bs) {
                                                                        if ($bs->start_time) {
                                                                            if (is_null($earliestStartTimeIndex) || $bs->start_time < $earliestStartTimeIndex) {
                                                                                $earliestStartTimeIndex = $bs->start_time;
                                                                            }
                                                                        }
                                                                    }
                                                                }

                                                                if ($booking->booking_date && $earliestStartTimeIndex) {
                                                                    $bookingDateTimeIndex = new \Cake\I18n\FrozenTime(
                                                                        $booking->booking_date->format('Y-m-d') . ' ' . $earliestStartTimeIndex->format('H:i:s')
                                                                    );
                                                                    $nowIndex = new \Cake\I18n\FrozenTime();
                                                                    $editCutoffDateTimeIndex = $nowIndex->addHours($editCutoffHoursIndex);
                                                                    $cancelCutoffDateTimeIndex = $nowIndex->addHours($cancellationCutoffHoursIndex);

                                                                    if ($bookingDateTimeIndex <= $editCutoffDateTimeIndex && $bookingDateTimeIndex <= $cancelCutoffDateTimeIndex) {
                                                                        $interactionAllowedByTimeIndex = false;
                                                                        $cancellationMessageIndex = "Cannot edit or cancel (within 1h)";
                                                                    } 
                                                                } else {
                                                                    $interactionAllowedByTimeIndex = false;
                                                                    $cancellationMessageIndex = "Time N/A for edit/cancel check.";
                                                                }
                                                            } catch (Exception $e) {
                                                                $interactionAllowedByTimeIndex = false;
                                                                $cancellationMessageIndex = "Error checking time window.";
                                                                error_log("[customerindex] Time calc error for booking ID {$booking->id}: " . $e->getMessage());
                                                            }
                                                        } else {
                                                             $interactionAllowedByTimeIndex = false; 
                                                             $cancellationMessageIndex = "Status does not allow actions.";
                                                        }

                                                        if ($statusAllowsActionsIndex && $interactionAllowedByTimeIndex) {
                                                            // Edit Button
                                                            echo $this->Html->link(
                                                                'Edit',
                                                                ['action' => 'customeredit', $booking->id],
                                                                ['class' => 'button button-primary', 'style' => 'background-color: #f0ad4e; border-color: #eea236; margin-top: 5px;',
                                                                'onmouseover' => 'this.style.backgroundColor="#ec971f"; this.style.borderColor="#d58512"',
                                                                'onmouseout' => 'this.style.backgroundColor="#f0ad4e"; this.style.borderColor="#eea236"']
                                                            );
                                                            // Cancel Button
                                                            echo $this->Form->postLink(
                                                                'Cancel',
                                                                ['action' => 'customerdelete', $booking->id],
                                                                ['confirm' => __('Are you sure you want to cancel booking # {0}?', $booking->id), 'class' => 'button button-danger', 'style' => 'background-color: #c9302c; border-color: #ac2925; margin-top: 5px;',
                                                                'onmouseover' => 'this.style.backgroundColor="#ac2925"; this.style.borderColor="#8c2320"',
                                                                'onmouseout' => 'this.style.backgroundColor="#c9302c"; this.style.borderColor="#ac2925"']
                                                            );
                                                        } elseif (!$interactionAllowedByTimeIndex && !empty($cancellationMessageIndex)) {
                                                            echo '<p class="text-muted small mb-0" style="white-space: normal;">' . h($cancellationMessageIndex) . '</p>';
                                                        } 
                                                        ?>
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
