<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Booking> $bookings
 */
?>
<div class="contacts index content">
    <div class="table-responsive">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-2" style="padding: 10px 20px;">
                    <?= $this->Html->link(__('Back to Dashboard'), ['controller' => 'Admins', 'action' => 'dashboard'], ['class' => 'btn btn-primary', 'style' => 'white-space: nowrap;']) ?>
                </div>
                <div class="col-8">
                    <h3><?= __('Bookings') ?></h3>
                </div>
            </div>
        </div>
        <table>
            <thead>
            <tr>
                <th><?= $this->Paginator->sort('booking_name') ?></th>
                <th><?= $this->Paginator->sort('booking_date') ?></th>
                <th><?= $this->Paginator->sort('total_cost') ?></th>
                <th><?= $this->Paginator->sort('remaining_cost') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($bookings as $booking): ?>
                <tr>
                    <td><?= h($booking->booking_name)?></td>
                    <td><?= h($booking->booking_date) ?></td>
                    <td><?= $this->Number->format($booking->total_cost) ?></td>
                    <td><?= $this->Number->format($booking->remaining_cost) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'customerview', $booking->id], ['class' => 'button']) ?>
                        <?= $this->Html->link(__('Add Stylist'), ['controller' => 'BookingsStylists', 'action' => 'customerstylistadd', $booking->id], ['class' => 'button']) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $booking->id], ['class' => 'button']) ?>
                        <?= $this->Form->postLink(
                            __('Delete'),
                            ['action' => 'customerdelete', $booking->id],
                            [
                                'method' => 'delete',
                                'confirm' => __('Are you sure you want to delete # {0}?', $booking->id),
                                'class' => 'button',
                            ]
                        ) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?= $this->Html->link(__('New Booking'), ['action' => 'customerbooking'], ['class' => 'btn btn-primary']) ?>
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
