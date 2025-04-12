<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Stylist $stylist
 */
?>
<?= $this->Html->css('/utility/views/views.css') ?>
<?= $this->Html->css('https://fonts.googleapis.com/icon?family=Material+Icons') ?>
<div class="row px-4" style="padding-top: 80px;">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <!-- Edit Stylists -->
            <div class="row px-2">
                <div class="col-lg-3 mb-4 side-nav-item">
                    <?= $this->Html->link(
                        '<div class="card h-100">
                            <div class="card-header view-card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="view-card-h4">Edit Stylist</h4>
                                </div>
                                <i class="material-icons view-icon">edit</i>
                            </div>
                            <div class="card-body view-card-body"></div>
                            <div class="card-footer view-card-footer">
                                <span>Edit Stylists</span>
                            </div>
                        </div>',
                        ['controller' => 'Stylists', 'action' => 'edit', $stylist->id],
                        ['escape' => false, 'class' => 'card-link-wrapper d-block text-decoration-none']
                    ) ?>
                </div>
                <!-- Delete Stylist -->
                <div class="col-lg-3 mb-4 side-nav-item">
                    <?= $this->Html->link(
                        '<div class="card h-100">
                            <div class="card-header delete-card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="view-card-h4">Delete Stylist</h4>
                                </div>
                                <i class="material-icons view-icon">delete</i>
                            </div>
                            <div class="card-body delete-card-body"></div>
                            <div class="card-footer delete-card-footer">
                                <span>Delete Stylist</span>
                            </div>
                        </div>',
                        ['controller' => 'Stylists', 'action' => 'delete', $stylist->id],
                        ['escape' => false,'class' => 'card-link-wrapper d-block text-decoration-none',
                            'confirm' => __('Are you sure you want to delete # {0}?', $stylist->id)],
                    ) ?>
                </div>

                <!-- List Stylist -->
                <div class="col-lg-3 mb-4 side-nav-item">
                    <?= $this->Html->link(
                        '<div class="card h-100">
                            <div class="card-header list-card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="view-card-h4">List Stylists</h4>
                                </div>
                                <i class="material-icons view-icon">menu</i>
                            </div>
                            <div class="card-body list-card-body"></div>
                            <div class="card-footer list-card-footer">
                                <span>List Stylists</span>
                            </div>
                        </div>',
                        ['controller' => 'Stylists', 'action' => 'index'],
                        ['escape' => false, 'class' => 'card-link-wrapper d-block text-decoration-none']
                    ) ?>
                </div>

                <!-- New Stylist -->
                <div class="col-lg-3 mb-4 side-nav-item">
                    <?= $this->Html->link(
                        '<div class="card h-100">
                            <div class="card-header new-card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="view-card-h4">Create New Stylist</h4>
                                </div>
                                <i class="material-icons view-icon">add</i>
                            </div>
                            <div class="card-body new-card-body"></div>
                            <div class="card-footer new-card-footer">
                                <span>Add Stylist</span>
                            </div>
                        </div>',
                        ['controller' => 'Stylists', 'action' => 'add'],
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
            <h3><?= h($stylist->first_name) ?> <?= h($stylist->last_name) ?></h3>
            <table>
                <tr>
                    <th><?= __('First Name') ?></th>
                    <td><?= h($stylist->first_name) ?></td>
                </tr>
                <tr>
                    <th><?= __('Last Name') ?></th>
                    <td><?= h($stylist->last_name) ?></td>
                </tr>
                <tr>
                    <th><?= __('Email') ?></th>
                    <td><?= h($stylist->email) ?></td>
                </tr>
                <tr>
                    <th><?= __('Created') ?></th>
                    <td><?= h($stylist->created) ?></td>
                </tr>
                <tr>
                    <th><?= __('Modified') ?></th>
                    <td><?= h($stylist->modified) ?></td>
                </tr>
            </table>
            <div class="related">
                <h4><?= __('Related Bookings') ?></h4>
                <?php if (!empty($stylist->bookings)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Booking Date') ?></th>
                            <th><?= __('Total Cost') ?></th>
                            <th><?= __('Remaining Cost') ?></th>
                            <th><?= __('Customer Id') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($stylist->bookings as $booking) : ?>
                        <tr>
                            <td><?= h($booking->booking_date) ?></td>
                            <td><?= h($booking->total_cost) ?></td>
                            <td><?= h($booking->remaining_cost) ?></td>
                            <td><?= h($booking->customer_id) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Bookings', 'action' => 'view', $booking->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Bookings', 'action' => 'edit', $booking->id]) ?>
                                <?= $this->Form->postLink(
                                    __('Delete'),
                                    ['controller' => 'Bookings', 'action' => 'delete', $booking->id],
                                    [
                                        'method' => 'delete',
                                        'confirm' => __('Are you sure you want to delete # {0}?', $booking->id),
                                    ]
                                ) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <div class="related">
                <h4><?= __('Related Services') ?></h4>
                <?php if (!empty($stylist->services)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Service Name') ?></th>
                            <th><?= __('Service Cost') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($stylist->services as $service) : ?>
                        <tr>
                            <td><?= h($service->service_name) ?></td>
                            <td><?= h($service->service_cost) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Services', 'action' => 'view', $service->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Services', 'action' => 'edit', $service->id]) ?>
                                <?= $this->Form->postLink(
                                    __('Delete'),
                                    ['controller' => 'Services', 'action' => 'delete', $service->id],
                                    [
                                        'method' => 'delete',
                                        'confirm' => __('Are you sure you want to delete # {0}?', $service->id),
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
