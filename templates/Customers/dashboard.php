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
                        <h2 class="fw-bold">Welcome, <?= h($customer->first_name) ?> <?= h($customer->last_name) ?>!</h2>
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
                            <div class="bookings-header">
                                <h3>Current Bookings</h3>
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
                                                            <div class="time">
                                                                <?php if ($booking->start_time && $booking->end_time): ?>
                                                                    <?= h($booking->start_time->format('h:i A')) ?> 
                                                                    <div class="time-separator">-</div>
                                                                    <?= h($booking->end_time->format('h:i A')) ?>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>

                                                        <div class="booking-info d-flex flex-column justify-content-between">
                                                            <div>
                                                                <div class="status-badge">
                                                                    <span class="status-text <?= $booking->status === 'active' ? 'active' : '' ?>">
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
                                                                                'services' => []
                                                                            ];
                                                                        }
                                                                        $stylistServices[$stylistId]['services'][] = $bookingService->service;
                                                                    }
                                                                    
                                                                    foreach ($stylistServices as $stylistData): ?>
                                                                        <div class="stylist-section">
                                                                            <div class="stylist-name">
                                                                                <?= h($stylistData['stylist']->first_name) ?> <?= h($stylistData['stylist']->last_name) ?>
                                                                            </div>
                                                                            <?php foreach ($stylistData['services'] as $service): ?>
                                                                                <div class="service-item">
                                                                                    • <?= h($service->service_name) ?> (<?= $this->Number->currency($service->service_cost) ?>)
                                                                                </div>
                                                                            <?php endforeach; ?>
                                                                        </div>
                                                                    <?php endforeach; ?>
                                                                </div>
                                                            </div>

                                                            <div class="booking-actions">
                                                                <?= $this->Html->link(
                                                                    'View',
                                                                    ['controller' => 'Bookings', 'action' => 'customerview', $booking->id],
                                                                    ['class' => 'view-btn']
                                                                ) ?>
                                                                <?php if ($booking->status === 'active'): ?>
                                                                    <?= $this->Form->postLink(
                                                                        'Cancel Booking',
                                                                        ['controller' => 'Bookings', 'action' => 'customerdelete', $booking->id],
                                                                        [
                                                                            'method' => 'delete',
                                                                            'confirm' => __('Are you sure you want to cancel this booking?'),
                                                                            'class' => 'cancel-btn'
                                                                        ]
                                                                    ) ?>
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

                <!--Past Bookings-->
                <div class="row">
                    <div class="col-12 mb-4">
                        <div class="card">
                            <div class="bookings-header">
                                <h3>Past Bookings</h3>
                                <div class="header-actions">
                                    <?= $this->Html->link(
                                        '<i class="material-icons">visibility</i> View All Past Bookings',
                                        ['controller' => 'Bookings', 'action' => 'customerPastBookings'],
                                        ['class' => 'view-all-btn', 'escape' => false]
                                    ) ?>
                                </div>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($pastBookings) && $pastBookings->count() > 0): ?>
                                    <div class="row justify-content-center">
                                        <?php foreach ($pastBookings as $booking): ?>
                                            <?php if ($booking->status === 'finished' || $booking->status === 'cancelled'): ?>
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
                                                                <div class="time">
                                                                    <?php if ($booking->start_time && $booking->end_time): ?>
                                                                        <?= h($booking->start_time->format('h:i A')) ?> 
                                                                        <div class="time-separator">-</div>
                                                                        <?= h($booking->end_time->format('h:i A')) ?>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>

                                                            <div class="booking-info d-flex flex-column justify-content-between">
                                                                <div>
                                                                    <div class="status-badge">
                                                                        <span class="status-text <?= $booking->status === 'finished' ? 'finished' : 'cancelled' ?>">
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
                                                                                    'services' => []
                                                                                ];
                                                                            }
                                                                            $stylistServices[$stylistId]['services'][] = $bookingService->service;
                                                                        }
                                                                        
                                                                        foreach ($stylistServices as $stylistData): ?>
                                                                            <div class="stylist-section">
                                                                                <div class="stylist-name">
                                                                                    <?= h($stylistData['stylist']->first_name) ?> <?= h($stylistData['stylist']->last_name) ?>
                                                                                </div>
                                                                                <?php foreach ($stylistData['services'] as $service): ?>
                                                                                    <div class="service-item">
                                                                                        • <?= h($service->service_name) ?> (<?= $this->Number->currency($service->service_cost) ?>)
                                                                                    </div>
                                                                                <?php endforeach; ?>
                                                                            </div>
                                                                        <?php endforeach; ?>
                                                                    </div>
                                                                </div>

                                                                <div class="booking-actions">
                                                                    <?= $this->Html->link(
                                                                        'View',
                                                                        ['controller' => 'Bookings', 'action' => 'customerview', $booking->id],
                                                                        ['class' => 'view-btn']
                                                                    ) ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center">
                                        <p>You have no past bookings.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!--Past Invoices-->
                <div class="row">
                    <div class="col-12 mb-4">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center"  style="background-color:#D7CCC8">
                                <h3 class="card-title">Past Invoices</h3>
                            </div>
                            <div class="card-body">
                                <!-- Past invoices content -->
                                <p>No past invoices found.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
