<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Booking $booking
 * @var string[]|\Cake\Collection\CollectionInterface $customers
 * @var string[]|\Cake\Collection\CollectionInterface $stylists
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $booking->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $booking->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Bookings'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="bookings form content">
            <?= $this->Form->create($booking) ?>
            <fieldset>
                <legend><?= __('Edit Booking') ?></legend>
                <?php
                    echo $this->Form->control('booking_date', [
                        'type' => 'date',
                        'class' => 'form-control'
                    ]);
                    echo $this->Form->control('start_time', [
                        'type' => 'time',
                        'class' => 'form-control',
                        'interval' => 15
                    ]);
                    echo $this->Form->control('end_time', [
                        'type' => 'time',
                        'class' => 'form-control',
                        'interval' => 15
                    ]);
                    echo $this->Form->control('services._ids', [
                        'options' => $services,
                        'multiple' => true,
                        'class' => 'form-control',
                        'style' => 'height: 150px;'
                    ]);
                    echo $this->Form->control('total_cost');
                    echo $this->Form->control('remaining_cost');
                    echo $this->Form->control('customer_id', ['options' => $customers, 'empty' => true]);
                    echo $this->Form->control('stylists._ids', ['options' => $stylists]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
