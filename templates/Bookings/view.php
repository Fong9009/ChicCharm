<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Booking $booking
 */
?>
<?= $this->Html->css('/views/views.css') ?>
<?= $this->Html->css('https://fonts.googleapis.com/icon?family=Material+Icons') ?>
<div class="row px-4"  style="padding-top: 80px;">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <!-- Edit Bookings -->
            <div class="row px-2">
                <div class="col-lg-3 mb-4 side-nav-item">
                    <?= $this->Html->link(
                        '<div class="card h-100">
                            <div class="card-header view-card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="view-card-h4">Edit Booking</h4>
                                </div>
                                <i class="material-icons view-icon">edit</i>
                            </div>
                            <div class="card-body view-card-body"></div>
                            <div class="card-footer view-card-footer">
                                <span>Edit Bookings</span>
                            </div>
                        </div>',
                        ['controller' => 'Bookings', 'action' => 'edit', $booking->id],
                        ['escape' => false, 'class' => 'card-link-wrapper d-block text-decoration-none']
                    ) ?>
                </div>
                <!-- Delete Booking -->
                <div class="col-lg-3 mb-4 side-nav-item">
                    <?= $this->Html->link(
                        '<div class="card h-100">
                            <div class="card-header delete-card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="view-card-h4">Delete Booking</h4>
                                </div>
                                <i class="material-icons view-icon">delete</i>
                            </div>
                            <div class="card-body delete-card-body"></div>
                            <div class="card-footer delete-card-footer">
                                <span>Delete Booking</span>
                            </div>
                        </div>',
                        ['controller' => 'Bookings', 'action' => 'delete', $booking->id],
                        ['escape' => false,'class' => 'card-link-wrapper d-block text-decoration-none',
                            'confirm' => __('Are you sure you want to delete # {0}?', $booking->booking_name)],
                    ) ?>
                </div>

                <!-- List Bookings -->
                <div class="col-lg-3 mb-4 side-nav-item">
                    <?= $this->Html->link(
                        '<div class="card h-100">
                            <div class="card-header list-card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="view-card-h4">List Bookings</h4>
                                </div>
                                <i class="material-icons view-icon">menu</i>
                            </div>
                            <div class="card-body list-card-body"></div>
                            <div class="card-footer list-card-footer">
                                <span>List Bookings</span>
                            </div>
                        </div>',
                        ['controller' => 'Bookings', 'action' => 'index'],
                        ['escape' => false, 'class' => 'card-link-wrapper d-block text-decoration-none']
                    ) ?>
                </div>

                <!-- New Booking -->
                <div class="col-lg-3 mb-4 side-nav-item">
                    <?= $this->Html->link(
                        '<div class="card h-100">
                            <div class="card-header new-card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="view-card-h4">Create New Booking</h4>
                                </div>
                                <i class="material-icons view-icon">add</i>
                            </div>
                            <div class="card-body new-card-body"></div>
                            <div class="card-footer new-card-footer">
                                <span>Add Stylist</span>
                            </div>
                        </div>',
                        ['controller' => 'Bookings', 'action' => 'add'],
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
    <div class="column">
        <div class="contacts view content">
            <h3><?= h($booking->booking_name) ?></h3>
            <table>
                <tr>
                    <th><?= __('Customer') ?></th>
                    <td><?= $booking->hasValue('customer') ? $this->Html->link($booking->customer->first_name, ['controller' => 'Customers', 'action' => 'view', $booking->customer->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Total Cost') ?></th>
                    <td>$<?= $this->Number->format($booking->total_cost, ['places' => 2]) ?></td>
                </tr>
                <tr>
                    <th><?= __('Remaining Cost') ?></th>
                    <td>$<?= $this->Number->format($booking->remaining_cost, ['places' => 2]) ?></td>
                </tr>
                <tr>
                    <th><?= __('Booking Date') ?></th>
                    <td><?= h($booking->booking_date) ?></td>
                </tr>
            </table>
            <div class="related">
                <h4><?= __('Related Stylists') ?></h4>
                <?php if (!empty($booking->stylists)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('First Name') ?></th>
                            <th><?= __('Last Name') ?></th>
                            <th><?= __('Email') ?></th>
                            <th><?= __('Created') ?></th>
                            <th><?= __('Modified') ?></th>

                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($booking->stylists as $stylist) : ?>
                        <tr>
                            <td><?= h($stylist->first_name) ?></td>
                            <td><?= h($stylist->last_name) ?></td>
                            <td><?= h($stylist->email) ?></td>
                            <td><?= h($stylist->created) ?></td>
                            <td><?= h($stylist->modified) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Stylists', 'action' => 'view', $stylist->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Stylists', 'action' => 'edit', $stylist->id]) ?>
                                <?= $this->Form->postLink(
                                    __('Delete'),
                                    ['controller' => 'Stylists', 'action' => 'delete', $stylist->id],
                                    [
                                        'method' => 'delete',
                                        'confirm' => __('Are you sure you want to delete # {0}?', $stylist->id),
                                    ]
                                ) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
