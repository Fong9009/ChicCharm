<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Stylist $stylist
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Stylist'), ['action' => 'edit', $stylist->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Stylist'), ['action' => 'delete', $stylist->id], ['confirm' => __('Are you sure you want to delete # {0}?', $stylist->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Stylists'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Stylist'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="stylists view content">
            <h3><?= h($stylist->first_name) ?></h3>
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
                    <th><?= __('Type') ?></th>
                    <td><?= h($stylist->type) ?></td>
                </tr>
                <tr>
                    <th><?= __('Profile Picture') ?></th>
                    <td><?= h($stylist->profile_picture) ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($stylist->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Nonce') ?></th>
                    <td><?= h($stylist->nonce) ?></td>
                </tr>
                <tr>
                    <th><?= __('Nonce Expiry') ?></th>
                    <td><?= h($stylist->nonce_expiry) ?></td>
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
                            <th><?= __('Id') ?></th>
                            <th><?= __('Booking Date') ?></th>
                            <th><?= __('Total Cost') ?></th>
                            <th><?= __('Remaining Cost') ?></th>
                            <th><?= __('Customer Id') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($stylist->bookings as $booking) : ?>
                        <tr>
                            <td><?= h($booking->id) ?></td>
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
                            <th><?= __('Id') ?></th>
                            <th><?= __('Service Name') ?></th>
                            <th><?= __('Service Cost') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($stylist->services as $service) : ?>
                        <tr>
                            <td><?= h($service->id) ?></td>
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