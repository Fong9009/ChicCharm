<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Booking $booking
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Booking'), ['action' => 'edit', $booking->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Booking'), ['action' => 'delete', $booking->id], ['confirm' => __('Are you sure you want to delete # {0}?', $booking->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Bookings'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Booking'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="bookings view content">
            <h3><?= h($booking->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Customer') ?></th>
                    <td><?= $booking->hasValue('customer') ? $this->Html->link($booking->customer->first_name, ['controller' => 'Customers', 'action' => 'view', $booking->customer->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($booking->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Total Cost') ?></th>
                    <td><?= $this->Number->format($booking->total_cost) ?></td>
                </tr>
                <tr>
                    <th><?= __('Remaining Cost') ?></th>
                    <td><?= $this->Number->format($booking->remaining_cost) ?></td>
                </tr>
                <tr>
                    <th><?= __('Booking Date') ?></th>
                    <td><?= h($booking->booking_date) ?></td>
                </tr>
                <tr>
                    <th><?= __('Start Time') ?></th>
                    <td><?= h($booking->start_time) ?></td>
                </tr>
                <tr>
                    <th><?= __('End Time') ?></th>
                    <td><?= h($booking->end_time) ?></td>
                </tr>
            </table>
            <div class="related">
                <h4><?= __('Related Services') ?></h4>
                <?php if (!empty($booking->services)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Service Name') ?></th>
                            <th><?= __('Cost') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($booking->services as $service) : ?>
                        <tr>
                            <td><?= h($service->service_name) ?></td>
                            <td><?= $this->Number->currency($service->service_cost) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Services', 'action' => 'view', $service->id]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <div class="related">
                <h4><?= __('Related Stylists') ?></h4>
                <?php if (!empty($booking->stylists)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('First Name') ?></th>
                            <th><?= __('Last Name') ?></th>
                            <th><?= __('Email') ?></th>
                            <th><?= __('Password') ?></th>
                            <th><?= __('Nonce') ?></th>
                            <th><?= __('Nonce Expiry') ?></th>
                            <th><?= __('Created') ?></th>
                            <th><?= __('Modified') ?></th>
                            <th><?= __('Type') ?></th>
                            <th><?= __('Profile Picture') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($booking->stylists as $stylist) : ?>
                        <tr>
                            <td><?= h($stylist->id) ?></td>
                            <td><?= h($stylist->first_name) ?></td>
                            <td><?= h($stylist->last_name) ?></td>
                            <td><?= h($stylist->email) ?></td>
                            <td><?= h($stylist->password) ?></td>
                            <td><?= h($stylist->nonce) ?></td>
                            <td><?= h($stylist->nonce_expiry) ?></td>
                            <td><?= h($stylist->created) ?></td>
                            <td><?= h($stylist->modified) ?></td>
                            <td><?= h($stylist->type) ?></td>
                            <td><?= h($stylist->profile_picture) ?></td>
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