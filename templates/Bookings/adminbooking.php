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
    const apiUrl2 = '<?= $this->Url->build("/bookings/get-available-time-slots") ?>';
</script>
<div class="booking-form-wrapper">
    <div class="row">
        <div class="side-nav">
        <h4 class="heading"><?= __('Actions') ?></h4>
        <?= $this->Html->link(__('My Bookings'), ['action' => 'customerindex'], ['class' => 'side-nav-item']) ?>
        </div>
        <div class="column-edit">
            <div class="bookings form content">
                <?= $this->Form->create($booking) ?>
                <fieldset>
                    <h2 class="text-center"><?= __('Add Booking') ?></h2>
                    <div class="row">
                        <div class="col-md-8">
                            <h5>Please Select A Customer</h5>
                            <div class="service-options">
                                <?= $this->Form->control('customer_id', [
                                    'label' => 'Select Customer',
                                    'options' => $customers,
                                    'empty' => 'Please select a customer...',
                                    'class' => 'form-control',
                                    'required' => true,
                                    'error' => ['class' => 'invalid-feedback'],
                                ]) ?>
                            </div>
                            <br>
                            <h5>Please Select The Services you would like to book</h5>
                            <div class="service-list">
                                <?php foreach ($services as $service): ?>
                                    <div class="form-check">
                                        <input class="form-check-input service-checkbox" type="checkbox"
                                               name="bookings_services[<?= $service->id ?>][service_id]"
                                               value="<?= $service->id ?>"
                                               id="service-<?= $service->id ?>"
                                               data-duration="<?= $service->duration_minutes ?>"
                                               data-cost="<?= $service->service_cost ?>">
                                        <label class="form-check-label" for="service-<?= $service->id ?>">
                                            <?= h($service->service_name) ?>
                                            (<?= h($service->duration_minutes) ?> mins) -
                                            $<?= number_format($service->service_cost, 2) ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Please Select The Date</h5>
                            <?php
                                echo $this->Form->control('booking_date', [
                                    'type' => 'date',
                                    'required' => true,
                                    'class' => 'form-control' . ($this->Form->isFieldError('booking_date') ? ' is-invalid' : ''),
                                    'id' => 'booking-date',
                                    'disabled' => true,
                                    'label' => false,
                                    'min' => date('Y-m-d'),
                                    'max' => date('Y-m-d', strtotime('+1 year')),
                                    'error' => ['class' => 'invalid-feedback']
                                ]);
                            ?>
                            <small class="text-muted">Please select at least one service first</small>
                        </div>
                        <div class="col-md-6">
                            <h5>Please Select The Time</h5>
                            <?php
                                echo $this->Form->control('start_time', [
                                    'type' => 'select',
                                    'options' => array_reduce(
                                        range(9 * 4, 17 * 4),
                                        function($options, $i) {
                                            $hour = floor($i / 4);
                                            $minute = ($i % 4) * 15;
                                            $timeStr = sprintf('%02d:%02d', $hour, $minute);
                                            $displayTime = date('g:i A', strtotime($timeStr));
                                            $options[$timeStr] = $displayTime;
                                            return $options;
                                        },
                                        ['' => 'Select a time slot']
                                    ),
                                    'class' => 'form-control',
                                    'id' => 'start-time',
                                    'disabled' => true,
                                    'label' => false,
                                    'required' => true
                                ]);
                            ?>
                            <small class="text-muted">Please select at least one service first</small>
                            <div id="time-range-display" class="mt-2" style="display: none;" hidden>
                                <span id="start-time-display"></span> - <span id="end-time-display"></span>
                            </div>
                            <?php echo $this->Form->hidden('end_time', ['id' => 'end-time']); ?>
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