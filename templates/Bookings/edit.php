<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Booking $booking
 * @var string[]|\Cake\Collection\CollectionInterface $customers
 * @var string[]|\Cake\Collection\CollectionInterface $stylists
 */
?>
<?= $this->Html->css('/utility/edits/edits.css') ?>
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
                <!-- Delete Service -->
                <?php if ($booking->status === 'active'): ?>
                <?php else: ?>
                    <div class="col-lg-3 col-md-6 col-sm-12 mb-3 side-nav-item">
                        <?= $this->Form->postLink(
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
                            [
                                'method' => 'delete',
                                'confirm' => __('Are you sure you want to permanently delete this cancelled booking?'),
                                'class' => 'card-link-wrapper d-block text-decoration-none',
                                'escape' => false,
                            ]
                        ) ?>
                    </div>
                <?php endif; ?>

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
        </div>
    </aside>
    <div class="column ">
        <div class="row">
            <div class="container">
                <div class="row align-items-center">
                    <h4 class="px-4"><?= __('Stylists In Booking') ?></h4>
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
                                <td><?= h($stylist->profile_picture) ?></td>
                                <td class="actions">
                                    <?= $this->Html->link(__('View'), ['controller' => 'Stylists', 'action' => 'view', $stylist->id], ['class' => 'button']) ?>
                                    <?= $this->Html->link(
                                        __('Remove Stylist'),
                                        ['controller' => 'Bookings', 'action' => 'removeStylist', $stylist->id, $booking->id],
                                        [
                                            'class' => 'button',
                                            'confirm' => __('Are you sure you want to remove # {0} from booking?', ($stylist->first_name . ' ' . $stylist->last_name)),
                                        ]
                                    ) ?>
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
