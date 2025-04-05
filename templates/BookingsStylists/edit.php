<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\BookingsStylist $bookingsStylist
 * @var string[]|\Cake\Collection\CollectionInterface $bookings
 * @var string[]|\Cake\Collection\CollectionInterface $stylists
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $bookingsStylist->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $bookingsStylist->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Bookings Stylists'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="bookingsStylists form content">
            <?= $this->Form->create($bookingsStylist) ?>
            <fieldset>
                <legend><?= __('Edit Bookings Stylist') ?></legend>
                <?php
                    echo $this->Form->control('start_time');
                    echo $this->Form->control('end_time');
                    echo $this->Form->control('selected_cost');
                    echo $this->Form->control('booking_id', ['options' => $bookings]);
                    echo $this->Form->control('stylist_id', ['options' => $stylists]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
