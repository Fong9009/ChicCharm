<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Booking $booking
 */
?>
<?= $this->Html->css('/utility/views/views.css') ?>
<?= $this->Html->css('https://fonts.googleapis.com/icon?family=Material+Icons') ?>
<div class="contacts index content">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <!-- Edit Services -->
            <div class="row gx-2">
                <div class="col-lg-3 col-md-6 col-sm-12 mb-3 side-nav-item">
                    <?= $this->Html->link(
                        '<div class="card h-100">
                            <div class="card-header view-card-header d-flex justify-content-between align-items-center flex-wrap">
                                <h4 class="view-card-h4 mb-0 flex-grow-1 text-truncate">Edit Booking</h4>
                                <i class="material-icons view-icon ms-2">edit</i>
                            </div>
                            <div class="card-body view-card-body"></div>
                            <div class="card-footer view-card-footer">
                                <span class="mb-0 text-truncate">Edit Booking</span>
                            </div>
                        </div>',
                        ['controller' => 'Bookings', 'action' => 'edit', $booking->id],
                        ['escape' => false, 'class' => 'card-link-wrapper d-block text-decoration-none']
                    ) ?>
                </div>
                <!-- Delete Service -->
                <div class="col-lg-3 col-md-6 col-sm-12 mb-3 side-nav-item">
                    <?= $this->Html->link(
                        '<div class="card h-100">
                            <div class="card-header delete-card-header d-flex justify-content-between align-items-center flex-wrap">
                                 <h4 class="view-card-h4 mb-0 flex-grow-1 text-truncate">Delete Booking</h4>
                                 <i class="material-icons view-icon ms-2">delete</i>
                            </div>
                            <div class="card-body delete-card-body"></div>
                            <div class="card-footer delete-card-footer">
                                <span class="mb-0 text-truncate">Delete Booking</span>
                            </div>
                        </div>',
                        ['controller' => 'Bookings', 'action' => 'delete', $booking->id],
                        ['escape' => false,'class' => 'card-link-wrapper d-block text-decoration-none',
                            'confirm' => __('Are you sure you want to delete # {0}?', $booking->id)],
                    ) ?>
                </div>

                <!-- List Service -->
                <div class="col-lg-3 col-md-6 col-sm-12 mb-3 side-nav-item">
                    <?= $this->Html->link(
                        '<div class="card h-100">
                            <div class="card-header list-card-header d-flex justify-content-between align-items-center flex-wrap">
                                <h4 class="view-card-h4 mb-0 flex-grow-1 text-truncate">List Bookings</h4>
                                <i class="material-icons view-icon ms-2">menu</i>
                            </div>
                            <div class="card-body list-card-body"></div>
                            <div class="card-footer list-card-footer">
                                <span class="mb-0 text-truncate">List Bookings</span>
                            </div>
                        </div>',
                        ['controller' => 'Bookings', 'action' => 'index'],
                        ['escape' => false, 'class' => 'card-link-wrapper d-block text-decoration-none']
                    ) ?>
                </div>

                <!-- New Service -->
                <div class="col-lg-3 col-md-6 col-sm-12 mb-3 side-nav-item">
                    <?= $this->Html->link(
                        '<div class="card h-100">
                            <div class="card-header new-card-header d-flex justify-content-between align-items-center flex-wrap">
                                 <h4 class="view-card-h4 mb-0 flex-grow-1 text-truncate">New Booking</h4>
                                 <i class="material-icons view-icon ms-2">add</i>
                            </div>
                            <div class="card-body new-card-body"></div>
                            <div class="card-footer new-card-footer">
                                <span class="mb-0 text-truncate">Add Booking</span>
                            </div>
                        </div>',
                        ['controller' => 'Bookings', 'action' => 'adminbooking'],
                        ['escape' => false, 'class' => 'card-link-wrapper d-block text-decoration-none']
                    ) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-2" style="padding: 10px 20px;">
                    <?= $this->Html->link(__('Back to Dashboard'), ['controller' => 'Admins', 'action' => 'dashboard'], ['class' => 'btn btn-primary text-white', 'style' => 'white-space: nowrap;']) ?>
                </div>
            </div>
        </div>
    </aside>
    <div class="table-responsive mt-3">
        <div class="row">
            <div class="container">
                <div class="row align-items-center">
                    <h3><?= h($booking->booking_name) ?></h3>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="table-responsive">
                <table class="table w-50">
                    <tbody>
                        <tr>
                            <th><?= __('Customer') ?></th>
                            <td><?= $booking->hasValue('customer') ? $this->Html->link($booking->customer->first_name, ['controller' => 'Customers', 'action' => 'view', $booking->customer->id]) : '' ?></td>
                        </tr>
                        <tr>
                            <th><?= __('Total Cost') ?></th>
                            <td><?= $this->Number->currency($booking->total_cost) ?></td>
                        </tr>
                        <tr>
                            <th><?= __('Remaining Cost') ?></th>
                            <td><?= $this->Number->currency($booking->remaining_cost) ?></td>
                        </tr>
                        <tr>
                            <th><?= __('Booking Date') ?></th>
                            <td><?= h($booking->booking_date) ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="container">
                <div class="row align-items-center">
                    <h4 class="px-4"><?= __('Related Services') ?></h4>
                </div>
            </div>
        </div>
        <!-- Services found in booking -->
        <div class="row">
            <?php if (!empty($booking->services)) : ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th><?= __('Service Name') ?></th>
                                <th><?= __('Cost') ?></th>
                                <th class="actions"><?= __('Actions') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($booking->services as $service) : ?>
                            <tr>
                                <td><?= h($service->service_name) ?></td>
                                <td><?= $this->Number->currency($service->service_cost) ?></td>
                                <td class="actions">
                                    <?= $this->Html->link(__('View'), ['controller' => 'Services', 'action' => 'view', $service->id] , ['class' => 'button']) ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
        <div class="row">
            <div class="container">
                <div class="row align-items-center">
                    <h4 class="px-4"><?= __('Related Stylists') ?></h4>
                </div>
            </div>
        </div>
        <div class="row">
            <?php if (!empty($booking->stylists)) : ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th><?= __('First Name') ?></th>
                                <th><?= __('Last Name') ?></th>
                                <th><?= __('Email') ?></th>
                                <th><?= __('Created') ?></th>
                                <th><?= __('Modified') ?></th>
                                <th><?= __('Type') ?></th>
                                <th><?= __('Profile Picture') ?></th>
                                <th class="actions"><?= __('Actions') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($booking->stylists as $stylist) : ?>
                            <tr>
                                <td><?= h($stylist->first_name) ?></td>
                                <td><?= h($stylist->last_name) ?></td>
                                <td><?= h($stylist->email) ?></td>
                                <td><?= h($stylist->created) ?></td>
                                <td><?= h($stylist->modified) ?></td>
                                <td><?= h($stylist->type) ?></td>
                                <td><?= h($stylist->profile_picture) ?></td>
                                <td class="actions">
                                    <?= $this->Html->link(__('View'), ['controller' => 'Stylists', 'action' => 'view', $stylist->id], ['class' => 'button']) ?>
                                    <?= $this->Html->link(__('Edit'), ['controller' => 'Stylists', 'action' => 'edit', $stylist->id], ['class' => 'button']) ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
