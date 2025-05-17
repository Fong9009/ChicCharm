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
$preselected_service_id = $this->request->getQuery('service_id');
?>
<?= $this->Html->css('/utility/forms/forms.css') ?>
<?= $this->Html->css('https://fonts.googleapis.com/icon?family=Material+Icons') ?>
<script>
    const apiUrl = '<?= $this->Url->build("/bookings/get-stylists-for-service/") ?>';
    const apiUrl2 = '<?= $this->Url->build("/bookings/get-available-time-slots") ?>';
    const apiUrl3 = '<?= $this->Url->build("/bookings/get-availability-count") ?>';
</script>
<div class="customer-form-wrapper customer-background" style="background-image: url(<?= $this->Url->image('customerbackground.jpg')?>);">
    <div class="row customer-form">
        <div class="column-edit">
            <div class="bookings form content admin-border w-100" id="booking-form">
                <div class="row">
                    <div class="col-3 col-sm-4 col-6 text-center">
                        <?= $this->Html->link(
                            '<i class="fa fa-arrow-left"></i> ' . __('Back'),
                            ['controller' => 'Customers', 'action' => 'dashboard'],
                            ['class' => 'side-nav-item', 'escape' => false]
                        ) ?>
                    </div>
                </div>
                <?= $this->Form->create($booking) ?>
                <fieldset>
                    <h2 class="text-center"><?= __('Add Booking') ?></h2><br>
                    <div class="row">
                        <div class="col-md-8">
                            <h4>Select Services</h4>
                            <div class="service-list">
                                <?php foreach ($services as $service): ?>
                                    <div class="form-check">
                                        <input class="form-check-input service-checkbox" type="checkbox"
                                               value="<?= $service->id ?>"
                                               id="service-<?= $service->id ?>"
                                               data-duration="<?= $service->duration_minutes ?>"
                                               data-cost="<?= $service->service_cost ?>"
                                               <?= $preselected_service_id == $service->id ? 'checked' : '' ?>>
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
                        <div class="col-md-12">
                            <h5>Please Select The Date</h5>
                            <?php
                             echo $this->Form->control('booking_date', [
                                'type' => 'date',
                                'required' => true,
                                'class' => 'form-control' . ($this->Form->isFieldError('booking_date') ? ' is-invalid' : ''),
                                'id' => 'booking-date',
                                'label' => false,
                                'error' => ['class' => 'invalid-feedback']
                             ]);
                            ?>
                             <small class="text-muted">Select services first</small>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-12">
                            <h4>Select Stylists for Each Service</h4>
                            <div id="service-stylist-selections"></div>
                        </div>
                    </div>
                    <div class="row" style="display: none;">
                        <div class="col-md-6">
                            <?php
                                echo $this->Form->control('total_cost', [
                                    'readonly' => true,
                                    'value' => '0.00',
                                    'id' => 'total-cost',
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
                <div class="selected-services-summary mt-4 p-3 border rounded">
                    <h5>Selected Services: <span id="service-count">0</span></h5>
                    <div id="selected-services-list"></div><br>
                    <h5>Total: $<span id="service-total">0.00</span></h5>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <?= $this->Form->button(__('Create Booking'), ['class' => 'btn btn-primary']) ?>
                        <?= $this->Html->link(__('Cancel'), ['controller' => 'Customers', 'action' => 'dashboard'], ['class' => 'btn btn-secondary']) ?>
                    </div>
                </div>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>
<?php $this->append('script'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if ($preselected_service_id): ?>
    const preselectedCheckbox = document.getElementById('service-<?= $preselected_service_id ?>');
    if (preselectedCheckbox && preselectedCheckbox.checked) {
        const event = new Event('change', { bubbles: true });
        preselectedCheckbox.dispatchEvent(event);
    }
    <?php endif; ?>
});
</script>
<?php $this->end(); ?>
