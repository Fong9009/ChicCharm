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
<?= $this->Html->css('/utility/indexes/indexes.css') ?>
<?= $this->Html->css('https://fonts.googleapis.com/icon?family=Material+Icons') ?>
<script>
    const apiUrl = '<?= $this->Url->build("/bookings/get-stylists-for-service/") ?>';
    const apiUrl2 = '<?= $this->Url->build("/bookings/get-available-time-slots") ?>';
    const apiUrl3 = '<?= $this->Url->build("/bookings/get-availability-count") ?>';
</script>
<div class="booking-form-wrapper">
    <div class="row">
        <aside class="column">
            <div class="side-nav">
                <h4 class="heading"><?= __('Actions') ?></h4>
                <div class="row gx-2">
                    <div class="col-lg-4 col-md-6 col-sm-12 mb-3 side-nav-item">
                        <?= $this->Html->link(
                            '<div class="card h-100">
                                <div class="card-header dashboard-card-header d-flex justify-content-between align-items-center flex-wrap">
                                    <h4 class="view-card-h4 mb-0 flex-grow-1 text-truncate">Dashboard</h4>
                                    <i class="material-icons view-icon ms-2">person</i>
                                </div>
                                <div class="card-body dashboard-card-body"></div>
                                <div class="card-footer dashboard-card-footer">
                                    <span class="mb-0 text-truncate">Back To Dashboard</span>
                                </div>
                            </div>',
                            ['controller' => 'Admins', 'action' => 'dashboard'],
                            ['escape' => false, 'class' => 'card-link-wrapper d-block text-decoration-none']
                        ) ?>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-12 mb-3 side-nav-item">
                        <?= $this->Html->link(
                            '<div class="card h-100">
                                <div class="card-header list-card-header d-flex justify-content-between align-items-center flex-wrap">
                                    <h4 class="view-card-h4 mb-0 flex-grow-1 text-truncate">List Bookings</h4>
                                    <i class="material-icons view-icon ms-2">menu</i>
                                </div>
                                <div class="card-body list-card-body"></div>
                                <div class="card-footer list-card-footer">
                                    <span class="mb-0 text-truncate">View All Bookings</span>
                                </div>
                            </div>',
                            ['action' => 'index'],
                            ['escape' => false, 'class' => 'card-link-wrapper d-block text-decoration-none']
                        ) ?>
                    </div>
                </div>
            </div>
        </aside>
        <div class="column-edit">
            <div class="bookings form content">
                <?= $this->Form->create($booking) ?>
                <fieldset>
                    <h2 class="text-center"><?= __('Add Booking') ?></h2>
                    <div class="row">
                        <div class="col-md-12">
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
                            <small class="text-muted">Please select at least one service and stylist first</small>
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
                                    'error' => ['class' => 'invalid-feedback'],
                                    'maxlength' => 1000,
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
