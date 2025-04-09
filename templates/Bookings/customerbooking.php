<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Booking $booking
 * @var \Cake\Collection\CollectionInterface|string[] $customers
 * @var \Cake\Collection\CollectionInterface|string[] $stylists
 */
?>
<div class="booking-form-wrapper">
    <div class="booking-form">
        <?= $this->Form->create($booking) ?>
        <fieldset>
            <legend><?= __('Add Booking') ?></legend>
            <p>Please provide booking information, you will be able to choose your stylists next.</p>
            <div class="form-group">
                <?= $this->Form->control('booking_name', [
                    'label' => __('Booking Name'),
                    'type' => 'text',
                    'class' => 'form-control',
                    'required' => true,
                    'placeholder' => __('Enter booking name'),
                    'error' => ['class' => 'invalid-feedback'],
                ]) ?>
            </div>
            <div class="form-group">
                <?= $this->Form->control('booking_date',[
                    'label' => __('Booking Date'),
                    'type' => 'date',
                    'class' => 'form-control',
                    'required' => true,
                    'placeholder' => __('Select booking date'),
                    'error' => ['class' => 'invalid-feedback'],
                ]) ?>
            </div>
            <?php
                echo $this->Form->control('total_cost', ['type' => 'hidden', 'value' => 0]);
                echo $this->Form->control('remaining_cost', ['type' => 'hidden', 'value' => 0]);
            ?>
        </fieldset>
        <?= $this->Form->button(__('Continue to Select Stylist'), [
            'class' => 'btn btn-primary',
            'type' => 'submit'
        ]) ?>
        <?= $this->Form->end() ?>
    </div>
</div>
