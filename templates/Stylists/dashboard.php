<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Stylist $stylist
 */
$this->layout = 'default';
?>
<?= $this->Html->css('/dashboard/profiledash.css') ?>
<?= $this->Html->css(['fonts', 'cake', 'custom', 'booking-cards']) ?>
<?= $this->Html->script('custom') ?>
<?= $this->Html->css('https://fonts.googleapis.com/icon?family=Material+Icons') ?>

<!-- Customer Dashboard Card -->
<div class="customer-dashboard" style="background-image: url(<?= $this->Url->image('stylistbackground.jpg')?>);">
    <div class="container" style="margin-top: -5px">
        <div class="card">
            <div class="card-body background-colour">
                <div class="card h-100 mb-4">
                    <div class="card-header d-flex justify-content-between customer-card-header" style="background-color:#D7CCC8">
                        <h2 class="fw-bold">Welcome, <?= h( $stylist->first_name) ?>!</h2>
                    </div>
                    <?= $this->Flash->render() ?>
                    <div class="card-body">
                    </div>
                </div>
                <!--Current Bookings-->
                <div class="row">
                    <div class="col-12 mb-4">
                        <div class="card">
                            <div class="bookings-header" style="background-color:#D7CCC8">
                                <h3 class="fw-bold">Current Bookings</h3>
                                <div class="header-actions">
                                    <?= $this->Html->link(
                                        '<i class="material-icons">visibility</i> View All Bookings',
                                        ['controller' => 'Bookings', 'action' => 'stylistindex'],
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
                                                                    <span class="status-text <?= $booking->status === 'active' ? 'active' : '' ?>">
                                                                        <?= strtoupper(h($booking->status)) ?>
                                                                    </span>
                                                                </div>

                                                                <div class="service-details">
                                                                    <?php if ($booking->booking_name): ?>
                                                                        <h5>Booking Name: <?= h($booking->booking_name) ?></h5>
                                                                    <?php else: ?>
                                                                        <h1>No Booking Name</h1>
                                                                    <?php endif; ?>
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
                                                                    'View',
                                                                    ['controller' => 'Bookings', 'action' => 'stylistview', $booking->id],
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
                                        <p>No one has booked you yet.</p>
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
                                <h3 class="fw-bold">Past Bookings</h3>
                                <div class="header-actions">
                                    <?= $this->Html->link(
                                        '<i class="material-icons">history</i> View All Past Bookings',
                                        ['controller' => 'Bookings', 'action' => 'stylistPastBookings'],
                                        ['class' => 'view-all-btn', 'escape' => false]
                                    ) ?>
                                </div>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($finishedBookings) && $finishedBookings->count() > 0): ?>
                                    <div class="row justify-content-center">
                                        <?php foreach ($finishedBookings as $booking): ?>
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
                                                                     <span class="status-text cancelled">
                                                                        <?= strtoupper(h($booking->status)) ?>
                                                                    </span>
                                                                </div>

                                                                <div class="service-details">
                                                                    <?php if ($booking->booking_name): ?>
                                                                        <h5>Booking Name: <?= h($booking->booking_name) ?></h5>
                                                                    <?php else: ?>
                                                                        <h1>No Booking Name</h1>
                                                                    <?php endif; ?>
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
                                                                    ['controller' => 'Bookings', 'action' => 'stylistview', $booking->id],
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
                <div class="card h-100 mb-4">
                    <div class="card-header d-flex justify-content-between customer-card-header" style="background-color:#D7CCC8">
                        <h2 class="fw-bold">Your Profile, <?= h( $stylist->first_name) ?>!</h2>
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
                                        <?php if (!empty($stylist->profile_picture)) : ?>
                                            <img src="<?= $this->Url->image('profile/' . $stylist->profile_picture) ?>"
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
                                            ['action' => 'stylistedit', $stylist->id],
                                            ['class' => 'btn btn-primary', 'escape' => false]
                                        ) ?>
                                    </div>
                                    <div class="card-body">
                                        <p><strong>Name:</strong> <?= h($stylist->first_name) ?> <?= h($stylist->last_name) ?></p>
                                        <p><strong>Email:</strong> <?= h($stylist->email) ?></p>
                                        <p><strong>Member Since:</strong> <?= $stylist->created->format('F Y') ?></p>
                                        <p><strong>Motto:</strong> <?= h($stylist->stylist_motto) ?></p>
                                        <p><strong>Bio:</strong> <?= h($stylist->stylist_bio) ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
