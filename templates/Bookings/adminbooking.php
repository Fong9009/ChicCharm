<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Booking $booking
 * @var \Cake\Collection\CollectionInterface|string[] $stylists
 * @var \Cake\Collection\CollectionInterface|string[] $services
 * @var \Cake\Collection\CollectionInterface|string[] $customers
 */

// Add the JavaScript file
$this->Html->script('booking', ['block' => 'script']);
?>
<script>
    const apiUrl = '<?= $this->Url->build("/bookings/get-stylists") ?>';
</script>
<div class="booking-form-wrapper">
    <div class="row">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Bookings'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
        <div class="column-edit">
            <div class="bookings form content">
                <?= $this->Form->create($booking) ?>
                <fieldset>
                    <h2 class="text-center"><?= __('Add Booking') ?></h2><br>
                    <div class="row">
                        <div class="col-md-6">

                            <h5>Please Select A Customer</h5>
                            <div class="service-options">
                                <?= $this->Form->control('customer_id', [
                                    'label' => 'Select Customer',
                                    'options' => $customers,
                                    'empty' => 'Please select a customer...',
                                    'class' => 'form-control',
                                    'required' => true,
                                    'error' => ['class' => 'invalid-feedback']
                                ]) ?>
                            </div>
                            <h5>Please Select The Services you would like to book</h5>
                            <div class="service-options">
                                <?php foreach ($services as $id => $service): ?>
                                    <div class="form-check">
                                        <?= $this->Form->checkbox('service_ids[]', [
                                            'value' => $id,
                                            'id' => 'service-' . $id,
                                            'hiddenField' => false,
                                            'class' => 'form-check-input service-checkbox'
                                        ]) ?>
                                        <label class="form-check-label" for="service-<?= $id ?>">
                                            <?= h($service) ?> Per Hour
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <h5>Please Select The Date</h5>
                            <?php
                            echo $this->Form->control('booking_date', [
                                'type' => 'date',
                                'required' => true,
                                'class' => 'form-control' . ($this->Form->isFieldError('booking_date') ? ' is-invalid' : ''),
                                'id' => 'booking-date',
                                'error' => ['class' => 'invalid-feedback']
                            ]);
                            ?>
                        </div>
                        <div class="col-md-4">
                            <h5>Please Select The Start Time</h5>
                            <?php
                            echo $this->Form->control('start_time', [
                                'type' => 'time',
                                'required' => true,
                                'class' => 'form-control' . ($this->Form->isFieldError('start_time') ? ' is-invalid' : ''),
                                'id' => 'start-time',
                                'interval' => 15,
                                'error' => ['class' => 'invalid-feedback']
                            ]);
                            ?>
                        </div>
                        <div class="col-md-4">
                            <h5>Please Select The End Time</h5>
                            <?php
                            echo $this->Form->control('end_time', [
                                'type' => 'time',
                                'required' => true,
                                'class' => 'form-control' . ($this->Form->isFieldError('end_time') ? ' is-invalid' : ''),
                                'id' => 'end-time',
                                'interval' => 15,
                                'error' => ['class' => 'invalid-feedback']
                            ]);
                            ?>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Select Stylists for Each Service</h5>
                            <div id="service-stylist-selections"></div>
                            <div id="stylist-ids-container"></div>
                        </div>
                    </div>
                    <div class="row" style="display: none;">
                        <div class="col-md-6">
                            <?php
                            echo $this->Form->control('total_cost', [
                                'readonly' => true,
                                'value' => '0.00',
                                'class' => 'form-control' . ($this->Form->isFieldError('total_cost') ? ' is-invalid' : ''),
                                'error' => ['class' => 'invalid-feedback']
                            ]);
                            ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <?php
                            echo $this->Form->control('notes', [
                                'type' => 'textarea',
                                'class' => 'form-control' . ($this->Form->isFieldError('notes') ? ' is-invalid' : ''),
                                'rows' => 3,
                                'placeholder' => 'Add any special notes or requirements...',
                                'error' => ['class' => 'invalid-feedback']
                            ]);
                            ?>
                        </div>
                    </div>
                </fieldset>
                <div class="selected-services-summary mt-3">
                    <h5>Selected Services: <span id="service-count">0</span></h5>
                    <div id="selected-services-list"></div><br>
                    <h5>Total: $<span id="service-total">0.00</span></h5>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <?= $this->Form->button(__('Create Booking'), ['class' => 'btn btn-primary']) ?>
                        <?= $this->Html->link(__('Cancel'), ['controller' => 'Bookings', 'action' => 'index'], ['class' => 'btn btn-secondary']) ?>
                    </div>
                </div>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>
