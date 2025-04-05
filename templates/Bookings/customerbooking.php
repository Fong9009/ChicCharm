<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Booking $booking
 * @var \Cake\Collection\CollectionInterface|string[] $customers
 * @var \Cake\Collection\CollectionInterface|string[] $stylists
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Bookings'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('Back to Dashboard'), ['controller' => 'Customers', 'action' => 'dashboard'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="enquiry-form">
            <?= $this->Form->create($booking) ?>
            <fieldset>
                <legend><?= __('Add Booking') ?></legend>
                <div>
                    <p>Please provide booking information, you will be able to choose your stylists next.</p>
                </div>
                <div class="form-group mb-3">
                    <?= $this->Form->control('booking_name', [
                        'label' => __('Booking Name'),
                        'type' => 'text',
                        'class' => 'form-control',
                        'required' => true,
                        'placeholder' => __('Enter booking Name'),
                        'error' => ['class' => 'invalid-feedback'],
                    ]) ?>
                </div>
                <div class="form-group mb-3">
                    <?= $this->Form->control('booking_date',[
                        'label' => __('Booking Date'),
                        'type' => 'date',
                        'class' => 'form-control',
                        'required' => true,
                        'placeholder' => __('Enter booking Date'),
                        'error' => ['class' => 'invalid-feedback'],
                       ]) ?>
                </div>
                <?php
                    echo $this->Form->control('total_cost', ['type' => 'hidden', 'value' => 0]);
                    echo $this->Form->control('remaining_cost', ['type' => 'hidden', 'value' => 0]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit'), ['class' => 'btn btn-primary']) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
