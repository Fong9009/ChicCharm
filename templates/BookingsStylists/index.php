<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\BookingsStylist> $bookingsStylists
 */
?>
<div class="bookingsStylists index content">
    <?= $this->Html->link(__('New Bookings Stylist'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Bookings Stylists') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('duration') ?></th>
                    <th><?= $this->Paginator->sort('selected_cost') ?></th>
                    <th><?= $this->Paginator->sort('booking_id') ?></th>
                    <th><?= $this->Paginator->sort('stylist_id') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bookingsStylists as $bookingsStylist): ?>
                <tr>
                    <td><?= $this->Number->format($bookingsStylist->id) ?></td>
                    <td><?= $this->Number->format($bookingsStylist->duration) ?></td>
                    <td><?= $this->Number->format($bookingsStylist->selected_cost) ?></td>
                    <td><?= $bookingsStylist->hasValue('booking') ? $this->Html->link($bookingsStylist->booking->id, ['controller' => 'Bookings', 'action' => 'view', $bookingsStylist->booking->id]) : '' ?></td>
                    <td><?= $bookingsStylist->hasValue('stylist') ? $this->Html->link($bookingsStylist->stylist->first_name, ['controller' => 'Stylists', 'action' => 'view', $bookingsStylist->stylist->id]) : '' ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $bookingsStylist->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $bookingsStylist->id]) ?>
                        <?= $this->Form->postLink(
                            __('Delete'),
                            ['action' => 'delete', $bookingsStylist->id],
                            [
                                'method' => 'delete',
                                'confirm' => __('Are you sure you want to delete # {0}?', $bookingsStylist->id),
                            ]
                        ) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('first')) ?>
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
            <?= $this->Paginator->last(__('last') . ' >>') ?>
        </ul>
        <p><?= $this->Paginator->counter(__('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')) ?></p>
    </div>
</div>