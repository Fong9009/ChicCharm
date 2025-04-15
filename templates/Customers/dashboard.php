<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Customer $customer
 */
$this->layout = 'default';
?>
<?= $this->Html->css('/landing-detail/css/styles.css') ?>
<?= $this->Html->css(['fonts', 'cake', 'custom']) ?>
<?= $this->Html->script('custom') ?>
<div class="customer-dashboard" style="background-image: url(<?= $this->Url->image('customerbackground.jpg')?>);">
    <div class="container" style="margin-top: -5px">
        <div class="card">
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-12">
                        <h2>Welcome, <?= h($customer->first_name) ?> <?= h($customer->last_name) ?>!</h2>
                    </div>
                </div>

                <!--Profile Picture and Summary-->
                <div class="row">
                    <div class="col-lg-6 col-md-12 col-sm-12 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h3 class="card-title">Profile Picture</h3>
                            </div>
                            <div class="card-body text-center">
                                <?php if (!empty($customer->profile_picture)) : ?>
                                    <img src="<?= $this->Url->image('profile/' . $customer->profile_picture) ?>"
                                         alt="Profile Picture"
                                         class="profile-picture img-fluid rounded mx-auto d-block">
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-12 mb-4 col-sm-12">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h3 class="card-title">Profile Summary</h3>
                                <?= $this->Html->link(
                                    'Edit Profile',
                                    ['action' => 'edit', $customer->id],
                                    ['class' => 'btn btn-primary']
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

                <!--Current Bookings-->
                <div class="row">
                    <div class="col-12 mb-4">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h3 class="card-title">Current Bookings</h3>
                                <div class="align-items-end">
                                    <?= $this->Html->link(
                                        'Make a Booking',
                                        ['controller' => 'Bookings', 'action' => 'customerbooking'],
                                        ['class' => 'btn btn-primary']
                                    ) ?>
                                    <?= $this->Html->link(
                                        'View All Bookings',
                                        ['controller' => 'Bookings', 'action' => 'customerindex'],
                                        ['class' => 'btn btn-primary']
                                    ) ?>
                                </div>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($customer->bookings)): ?>
                                    <div class="table-responsive">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th><?= __('Booking Date') ?></th>
                                                    <th><?= __('Services & Stylists') ?></th>
                                                    <th><?= __('Total Cost') ?></th>
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
                                                        <?= $this->Html->link(
                                                            'View',
                                                            ['controller' => 'Bookings', 'action' => 'customerview', $booking->id],
                                                            ['class' => 'button', 'style' => 'background-color: rgb(40, 167, 69); border-color: rgb(40, 167, 69);']
                                                        ) ?>
                                                        <?php if ($booking->status === 'active'): ?>
                                                            <?= $this->Form->postLink(
                                                                'Cancel Booking',
                                                                ['controller' => 'Bookings', 'action' => 'customerdelete', $booking->id],
                                                                [
                                                                    'method' => 'delete',
                                                                    'confirm' => __('Are you sure you want to cancel this booking?'),
                                                                    'class' => 'button',
                                                                    'style' => 'background-color: rgb(220, 53, 69); border-color: rgb(220, 53, 69);'
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
                                <?php else: ?>
                                    <p>No current bookings found.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!--Past Bookings-->
                <div class="row">
                    <div class="col-12 mb-4">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h3 class="card-title">Past Bookings</h3>
                            </div>
                            <div class="card-body">
                                <!-- Past bookings content -->
                            </div>
                        </div>
                    </div>
                </div>

                <!--Past Invoices-->
                <div class="row">
                    <div class="col-12 mb-4">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h3 class="card-title">Past Invoices</h3>
                            </div>
                            <div class="card-body">
                                <!-- Past invoices content -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
