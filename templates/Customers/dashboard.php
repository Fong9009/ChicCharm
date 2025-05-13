<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Customer $customer
 */
$this->layout = 'default';
?>
<?= $this->Html->css('/dashboard/profiledash.css') ?>
<?= $this->Html->css(['fonts', 'cake', 'custom', 'booking-cards']) ?>
<?= $this->Html->script('custom') ?>
<?= $this->Html->css('https://fonts.googleapis.com/icon?family=Material+Icons') ?>

<!-- Customer Dashboard Card -->
<div class="customer-dashboard" style="background-image: url(<?= $this->Url->image('customerbackground.jpg')?>);">
    <div class="container" style="margin-top: -5px">
        <div class="card">
            <div class="card-body background-colour">
                <div class="card h-100 mb-4">
                    <div class="card-header d-flex justify-content-between customer-card-header" style="background-color:#D7CCC8">
                        <h2 class="fw-bold">Welcome, <?= h($customer->first_name) ?>!</h2>
                    </div>
                    <?= $this->Flash->render() ?>
                    <div class="card-body">
                        <div class="row mb-4 p-1">
                            <div class="w-100" style="height: 2px; background-color: #6c757d;"></div>
                        </div>
                        <!--Profile Picture and Summary-->
                        <div class="row">
                            <div class="col-lg-4 col-md-12 col-sm-12 mb-4">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <?php if (!empty($customer->profile_picture)) : ?>
                                            <img src="<?= $this->Url->image('profile/' . $customer->profile_picture) ?>"
                                                 alt="Profile Picture"
                                                 class="profile-picture img-fluid rounded mx-auto d-block">
                                        <?php else : ?>
                                            <img src="<?= $this->Url->image('profile/blank-profile.png') ?>"
                                                 alt="Profile Picture"
                                                 class="profile-picture img-fluid rounded mx-auto d-block">
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-8 col-md-12 mb-4 col-sm-12">
                                <div class="card h-100">
                                    <div class="card-header d-flex justify-content-between align-items-center customer-card-header" style="background-color:#D7CCC8">
                                        <h3 class="card-title">Profile Summary</h3>
                                        <?= $this->Html->link(
                                            'Edit Profile <i class="material-icons view-icon small-icon">edit</i>',
                                            ['action' => 'edit', $customer->id],
                                            ['class' => 'btn btn-primary', 'escape' => false]
                                        ) ?>
                                    </div>
                                    <div class="card-body">
                                        <p><strong>Name:</strong> <?= h($customer->first_name) ?> <?= h($customer->last_name) ?></p>
                                        <p><strong>Email:</strong> <?= h($customer->email) ?></p>
                                        <p><strong>Member Since:</strong> <?= $customer->created->format('F Y') ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Current Bookings-->
                <div class="row">
                    <div class="col-12 mb-4">
                        <div class="card">
                            <div class="bookings-header" style="background-color:#D7CCC8">
                                <h3 class="card-title">Current Bookings</h3>
                                <div class="header-actions">
                                    <?= $this->Html->link(
                                        '<i class="material-icons">add</i> Make a Booking',
                                        ['controller' => 'Bookings', 'action' => 'customerbooking'],
                                        ['class' => 'make-booking-btn', 'escape' => false]
                                    ) ?>
                                    <?= $this->Html->link(
                                        '<i class="material-icons">visibility</i> View All Bookings',
                                        ['controller' => 'Bookings', 'action' => 'customerindex'],
                                        ['class' => 'view-all-btn', 'escape' => false]
                                    ) ?>
                                </div>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($activeBookings) && $activeBookings->count() > 0): ?>
                                    <div class="row justify-content-center">
                                        <?php foreach ($activeBookings as $booking): ?>
                                            <div class="col-12 col-xl-6">
                                                <div class="card booking-card">
                                                    <div class="d-flex">
                                                        <div class="booking-datetime">
                                                            <div class="month">
                                                                <?php if ($booking->booking_date): ?>
                                                                    <?= h($booking->booking_date->format('M')) ?>
                                                                <?php endif; ?>
                                                            </div>
                                                            <div class="day">
                                                                <?php if ($booking->booking_date): ?>
                                                                    <?= h($booking->booking_date->format('d')) ?>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>

                                                        <div class="booking-info d-flex flex-column justify-content-between">
                                                            <div>
                                                                <div class="status-badge">
                                                                    <span class="status-text <?= $booking->status === 'active' ? 'active' : ($booking->status === 'Confirmed - Payment Due' ? 'payment-due' : ($booking->status === 'Confirmed - Paid' ? 'paid' : '')) ?>">
                                                                        <?= strtoupper(h($booking->status)) ?>
                                                                    </span>
                                                                </div>

                                                                <div class="service-details">
                                                                    <?php
                                                                    // Group services by stylist
                                                                    $stylistServices = [];
                                                                    foreach ($booking->bookings_services as $bookingService) {
                                                                        $stylistId = $bookingService->stylist->id;
                                                                        if (!isset($stylistServices[$stylistId])) {
                                                                            $stylistServices[$stylistId] = [
                                                                                'stylist' => $bookingService->stylist,
                                                                                'booking_services' => []
                                                                            ];
                                                                        }
                                                                        $stylistServices[$stylistId]['booking_services'][] = $bookingService;
                                                                    }

                                                                    foreach ($stylistServices as $stylistData): ?>
                                                                        <div class="stylist-section">
                                                                            <div class="stylist-name" style="color: black;">
                                                                                <?= h($stylistData['stylist']->first_name) ?> <?= h($stylistData['stylist']->last_name) ?>:
                                                                            </div>
                                                                            <ul style="list-style: disc; padding-left: 1.5rem; margin-bottom: 0;">
                                                                                <?php foreach ($stylistData['booking_services'] as $bookingService): ?>
                                                                                    <li class="service-item" style="color: black;">
                                                                                        <?= h($bookingService->service->service_name) ?>
                                                                                        (<?= $this->Number->currency($bookingService->service->service_cost) ?>):

                                                                                        <?php if ($bookingService->start_time && $bookingService->end_time): ?>
                                                                                            <span class="service-time">
                                                                                                <?= h($bookingService->start_time->format('h:i A')) ?> - <?= h($bookingService->end_time->format('h:i A')) ?>
                                                                                            </span>
                                                                                        <?php elseif ($bookingService->start_time): ?>
                                                                                            <span class="service-time"><?= h($bookingService->start_time->format('h:i A')) ?></span>
                                                                                        <?php endif; ?>
                                                                                    </li>
                                                                            <?php endforeach; ?>
                                                                            </ul>
                                                                        </div>
                                                                    <?php endforeach; ?>
                                                                </div>
                                                                <div class="total-cost">
                                                                    <?= 'Total Cost: ' . $this->Number->currency($booking->total_cost) ?>
                                                                </div>
                                                            </div>

                                                            <div class="booking-actions d-flex flex-column flex-sm-row gap-2">
                                                                <?= $this->Html->link(
                                                                    'View Details',
                                                                    ['controller' => 'Bookings', 'action' => 'customerview', $booking->id],
                                                                    ['class' => 'view-btn']
                                                                ) ?>

                                                                <?php
                                                                // Show invoice link if paid (and PDF exists) OR if payment is due (and history exists)
                                                                if (!empty($booking->latest_payment_history) &&
                                                                    ( ($booking->status === 'Confirmed - Paid' && !empty($booking->latest_payment_history->invoice_pdf)) ||
                                                                      ($booking->status === 'Confirmed - Payment Due')
                                                                    )
                                                                ):
                                                                ?>
                                                                    <?php
                                                                        echo $this->Html->link(
                                                                            __('Check/Download Invoice'),
                                                                            ['controller' => 'Payments', 'action' => 'viewInvoice', $booking->latest_payment_history->id],
                                                                            [
                                                                                'class' => 'view-btn',
                                                                                'style' => 'background-color: #6c757d; border-color: #6c757d; color: white;',
                                                                                'onmouseover' => "this.style.backgroundColor='#5a6268'; this.style.borderColor='#5a6268';",
                                                                                'onmouseout' => "this.style.backgroundColor='#6c757d'; this.style.borderColor='#6c757d';",
                                                                                'target' => '_blank',
                                                                                'escape' => false
                                                                            ]
                                                                        );
                                                                    ?>
                                                                <?php endif; // End invoice link check ?>

                                                                <?php // Message only for paid bookings
                                                                if ($booking->status === 'Confirmed - Paid'): ?>
                                                                    <p class="text-muted small mt-1 mb-0 align-self-center">This booking is paid. Contact store for changes.</p>
                                                                <?php endif; ?>

                                                                <?php // Edit/Cancel buttons logic for other statuses (excluding Confirmed - Paid)
                                                                if (!in_array($booking->status, ['Confirmed - Paid'])):
                                                                ?>
                                                                    <?php
                                                                    $statusAllowsActions = in_array($booking->status, ['active', 'Confirmed - Payment Due']);
                                                                    $interactionAllowedByTime = true;
                                                                    $cancellationCutoffHours = 1;
                                                                    $cancellationMessage = "Cannot edit or cancel (within 1h)";

                                                                    if ($statusAllowsActions) {

                                                                        try {
                                                                            // Find the earliest start time among the booking's services
                                                                            $earliestStartTime = null;
                                                                            if (!empty($booking->bookings_services)) {
                                                                                foreach ($booking->bookings_services as $bs) {
                                                                                    if ($bs->start_time) {
                                                                                        if (is_null($earliestStartTime) || $bs->start_time < $earliestStartTime) {
                                                                                            $earliestStartTime = $bs->start_time;
                                                                                        }
                                                                                    }
                                                                                }
                                                                            }

                                                                            if ($booking->booking_date && $earliestStartTime) {
                                                                                $bookingDateTime = new \Cake\I18n\FrozenTime(
                                                                                    $booking->booking_date->format('Y-m-d') . ' ' . $earliestStartTime->format('H:i:s')
                                                                                );
                                                                                $cutoffTime = (new \Cake\I18n\FrozenTime())->addHours($cancellationCutoffHours);

                                                                                if ($bookingDateTime < $cutoffTime) {
                                                                                    $interactionAllowedByTime = false;
                                                                                }
                                                                            } else {
                                                                                error_log("Could not determine booking date or earliest start time for booking ID: " . $booking->id);
                                                                                $interactionAllowedByTime = false;
                                                                                $cancellationMessage = "Cannot determine booking time.";
                                                                            }
                                                                        } catch (Exception $e) {
                                                                            error_log("Error calculating booking time for booking ID " . $booking->id . ": " . $e->getMessage());
                                                                            $interactionAllowedByTime = false;
                                                                            $cancellationMessage = "Error checking time window.";
                                                                        }
                                                                    } else {
                                                                         $interactionAllowedByTime = false;
                                                                    }
                                                                    ?>

                                                                    <?php if ($statusAllowsActions): ?>
                                                                        <?php if ($interactionAllowedByTime): ?>
                                                                            <?= $this->Html->link(
                                                                                'Edit Booking',
                                                                                ['controller' => 'Bookings', 'action' => 'customeredit', $booking->id],
                                                                                ['class' => 'btn-edit-customer-dashboard']
                                                                            ) ?>
                                                                            <?= $this->Form->postLink(
                                                                                'Cancel Booking',
                                                                                ['controller' => 'Bookings', 'action' => 'customerdelete', $booking->id],
                                                                                [
                                                                                    'method' => 'delete',
                                                                                    'confirm' => __('Are you sure you want to cancel this booking?'),
                                                                                    'class' => 'cancel-btn'
                                                                                ]
                                                                            ) ?>
                                                                        <?php else: ?>
                                                                            <span class="text-muted small d-block mt-1 align-self-center"><?= h($cancellationMessage) ?></span>
                                                                        <?php endif; ?>
                                                                    <?php endif; ?>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center">
                                        <p>You have no current bookings.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 mb-4">
                        <div class="card">
                            <div class="bookings-header" style="background-color:#D7CCC8">
                                <h3 class="card-title">Past Bookings</h3>
                                <div class="header-actions">
                                    <?= $this->Html->link(
                                        '<i class="material-icons">history</i> View All Past Bookings',
                                        ['controller' => 'Bookings', 'action' => 'customerPastBookings'],
                                        ['class' => 'view-all-btn', 'escape' => false]
                                    ) ?>
                                </div>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($cancelledBookings) && $cancelledBookings->count() > 0): ?>
                                    <div class="row justify-content-center">
                                        <?php foreach ($cancelledBookings as $booking): ?>
                                            <div class="col-12 col-xl-6">
                                                <div class="card booking-card cancelled-card">
                                                    <div class="d-flex">
                                                        <div class="booking-datetime">
                                                            <div class="month">
                                                                <?php if ($booking->booking_date): ?>
                                                                    <?= h($booking->booking_date->format('M')) ?>
                                                                <?php endif; ?>
                                                            </div>
                                                            <div class="day">
                                                                <?php if ($booking->booking_date): ?>
                                                                    <?= h($booking->booking_date->format('d')) ?>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>

                                                        <div class="booking-info d-flex flex-column justify-content-between">
                                                            <div>
                                                                <div class="status-badge">
                                                                     <span class="status-text <?= $booking->status === 'active' ? 'active' : ($booking->status === 'Confirmed - Payment Due' ? 'payment-due' : ($booking->status === 'Confirmed - Paid' ? 'paid' : ($booking->status === 'cancelled' ? 'cancelled' :'' ) )) ?>">
                                                                        <?= strtoupper(h($booking->status)) ?>
                                                                    </span>
                                                                </div>

                                                                <div class="service-details">
                                                                    <?php
                                                                    // Group services by stylist
                                                                    $stylistServices = [];
                                                                    if (!empty($booking->bookings_services)) {
                                                                        foreach ($booking->bookings_services as $bookingService) {
                                                                             $stylistId = $bookingService->stylist->id ?? 'unknown';
                                                                             $stylistName = isset($bookingService->stylist) ? h($bookingService->stylist->first_name) . ' ' . h($bookingService->stylist->last_name) : 'Unknown Stylist';

                                                                            if (!isset($stylistServices[$stylistId])) {
                                                                                $stylistServices[$stylistId] = [
                                                                                    'stylist_name' => $stylistName,
                                                                                    'booking_services' => []
                                                                                ];
                                                                            }
                                                                            $stylistServices[$stylistId]['booking_services'][] = $bookingService;
                                                                        }
                                                                    }

                                                                    foreach ($stylistServices as $stylistData): ?>
                                                                        <div class="stylist-section">
                                                                            <div class="stylist-name" style="color: black;">
                                                                                <?= $stylistData['stylist_name'] ?>:
                                                                            </div>
                                                                            <ul style="list-style: disc; padding-left: 1.5rem; margin-bottom: 0;">
                                                                                <?php foreach ($stylistData['booking_services'] as $bookingService): ?>
                                                                                    <li class="service-item" style="color: black;">
                                                                                        <?= h($bookingService->service->service_name ?? 'Unknown Service') ?>
                                                                                         (<?= isset($bookingService->service) ? $this->Number->currency($bookingService->service->service_cost) : '$?.??' ?>):

                                                                                        <?php if ($bookingService->start_time && $bookingService->end_time): ?>
                                                                                            <span class="service-time">
                                                                                                <?= h($bookingService->start_time->format('h:i A')) ?> - <?= h($bookingService->end_time->format('h:i A')) ?>
                                                                                            </span>
                                                                                        <?php elseif ($bookingService->start_time): ?>
                                                                                            <span class="service-time"><?= h($bookingService->start_time->format('h:i A')) ?></span>
                                                                                        <?php endif; ?>
                                                                                    </li>
                                                                            <?php endforeach; ?>
                                                                            </ul>
                                                                        </div>
                                                                    <?php endforeach; ?>
                                                                     <?php if (empty($stylistServices)): ?>
                                                                         <p style="color: black;">No service details available.</p>
                                                                     <?php endif; ?>
                                                                </div>
                                                                <div class="total-cost">
                                                                    <?= 'Total Cost: ' . $this->Number->currency($booking->total_cost) ?>
                                                                </div>
                                                            </div>

                                                             <div class="booking-actions d-flex flex-column flex-sm-row gap-2">
                                                                 <?= $this->Html->link(
                                                                    'View Details',
                                                                    ['controller' => 'Bookings', 'action' => 'customerview', $booking->id],
                                                                    ['class' => 'view-btn']
                                                                ) ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center">
                                        <p>You have no recently cancelled bookings.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
