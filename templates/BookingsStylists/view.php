<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\BookingsStylist $bookingsStylist
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Bookings Stylist'), ['action' => 'edit', $bookingsStylist->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Bookings Stylist'), ['action' => 'delete', $bookingsStylist->id], ['confirm' => __('Are you sure you want to delete # {0}?', $bookingsStylist->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Bookings Stylists'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Bookings Stylist'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="bookingsStylists view content">
            <h3><?= h($bookingsStylist->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Booking') ?></th>
                    <td><?= $bookingsStylist->hasValue('booking') ? $this->Html->link($bookingsStylist->booking->id, ['controller' => 'Bookings', 'action' => 'view', $bookingsStylist->booking->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Stylist') ?></th>
                    <td><?= $bookingsStylist->hasValue('stylist') ? $this->Html->link($bookingsStylist->stylist->first_name, ['controller' => 'Stylists', 'action' => 'view', $bookingsStylist->stylist->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($bookingsStylist->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Duration') ?></th>
                    <td><?= $this->Number->format($bookingsStylist->duration) ?></td>
                </tr>
                <tr>
                    <th><?= __('Selected Cost') ?></th>
                    <td><?= $this->Number->format($bookingsStylist->selected_cost) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>