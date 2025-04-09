<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Customer $customer
 */
$this->layout = 'default';
?>
<div class="customer-dashboard" style="background-image: url(<?= $this->Url->image('customerbackground.jpg')?>);">
    <div class="container">
        <div class="card">
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-12">
                        <h2>Welcome, <?= h($customer->first_name) ?> <?= h($customer->last_name) ?>!</h2>
                    </div>
                </div>
                
                <!--Profile Picture and Summary-->
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h3 class="card-title">Profile Picture</h3>
                            </div>
                            <div class="card-body text-center">
                                <?php if (!empty($customer->profile_picture)) : ?>
                                    <img src="<?= $this->Url->image('profile/' . $customer->profile_picture) ?>"
                                         alt="Profile Picture"
                                         class="profile-picture">
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-4">
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
                                                    <th><?= __('Booking Name') ?></th>
                                                    <th><?= __('Booking Date') ?></th>
                                                    <th><?= __('Total Cost') ?></th>
                                                    <th><?= __('Actions') ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($customer->bookings as $booking): ?>
                                                <tr>
                                                    <td><?= h($booking->booking_name) ?></td>
                                                    <td><?= h($booking->booking_date->format('F j, Y')) ?></td>
                                                    <td>$<?= number_format($booking->total_cost, 2) ?></td>
                                                    <td class="actions">
                                                        <?= $this->Html->link(__('View'), ['controller' => 'Bookings', 'action' => 'customerview', $booking->id], ['class' => 'button']) ?>
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
                                <?= $this->Html->link(
                                    'View all Previous Bookings',
                                    ['action' => 'edit', $customer->id],
                                    ['class' => 'btn btn-primary']
                                ) ?>
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
                                <?= $this->Html->link(
                                    'View all Previous Invoices',
                                    ['action' => 'edit', $customer->id],
                                    ['class' => 'btn btn-primary']
                                ) ?>
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
