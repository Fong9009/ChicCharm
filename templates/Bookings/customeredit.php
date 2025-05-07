<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Booking $booking
 * @var \Cake\Collection\CollectionInterface|string[] $stylists
 * @var \Cake\Collection\CollectionInterface|string[] $services
 * @var \Cake\Collection\CollectionInterface|string[] $customers 
 */

$this->Html->script('booking', ['block' => 'script']);

// Prepare existing services for JS
$existingServicesData = [];
if (!empty($booking->bookings_services)) {
    foreach ($booking->bookings_services as $bs) {
        // Ensure relationships are loaded
        if (empty($bs->service)) continue;

        // Calculate end time based on start time and duration
        $startTime = $bs->start_time ? \Cake\I18n\FrozenTime::parse($bs->start_time->format('H:i:s')) : null;
        $duration = $bs->service->duration_minutes ?? 0;
        $endTime = null;
        if ($startTime && $duration > 0) {
            $endTime = $startTime->addMinutes($duration);
        }

        $existingServicesData[] = [
            'original_service_id' => $bs->service_id,
            'original_stylist_id' => $bs->stylist_id,
            'original_start_time' => $bs->start_time ? $bs->start_time->format('H:i:s') : null,
            'original_end_time' => $endTime ? $endTime->format('H:i:s') : null,
            'service_cost' => $bs->service_cost,
            'service_duration' => $duration, 
        ];
    }
}
?>
<?= $this->Html->css('/utility/indexes/indexes.css') ?>
<?= $this->Html->css('https://fonts.googleapis.com/icon?family=Material+Icons') ?>

<script>
        // API URLs needed by booking.js
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
                            ['controller' => 'Customers', 'action' => 'dashboard'],     
                            ['escape' => false, 'class' => 'card-link-wrapper d-block text-decoration-none']
                        ) ?>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-12 mb-3 side-nav-item">
                        <?= $this->Html->link(
                            '<div class="card h-100">
                                <div class="card-header list-card-header d-flex justify-content-between align-items-center flex-wrap">
                                    <h4 class="view-card-h4 mb-0 flex-grow-1 text-truncate">My Bookings</h4>
                                    <i class="material-icons view-icon ms-2">menu</i>
                                </div>
                                <div class="card-body list-card-body"></div>
                                <div class="card-footer list-card-footer">
                                    <span class="mb-0 text-truncate">View Your Bookings</span>
                                </div>
                            </div>',
                            ['action' => 'customerindex'],  
                            ['escape' => false, 'class' => 'card-link-wrapper d-block text-decoration-none']
                        ) ?>
                    </div>
                </div>
            </div>
        </aside>
        <div class="column-edit">
            <div class="bookings form content">
                <?= $this->Form->create($booking, ['id' => 'booking-form']) ?>
                <fieldset>
                    <h2 class="text-center"><?= __('Edit Your Booking') ?></h2>
                    <div class="row">
                        <div class="col-md-8">
                            <h5><?= __('Customer') ?></h5>
                            <div class="service-options">
                                <?= $this->Form->control('customer_id', [
                                    'label' => false,
                                    'options' => $customers,
                                    'class' => 'form-control',
                                    'disabled' => true,
                                    'required' => true,
                                    'error' => ['class' => 'invalid-feedback'],
                                ]) ?>
                            </div>
                            <br>
                            <h5><?= __('Please Select The Services you would like to book') ?></h5>
                            <div class="service-list">
                                <?php foreach ($services as $service): ?>
                                    <?php
                                        // Determine if service is selected and get associated details
                                        $isChecked = false;
                                        $selectedStylistId = '';
                                        $selectedStartTime = '';
                                        foreach ($booking->bookings_services as $bs) {
                                            // Ensure relationship data is loaded before accessing properties
                                            if (isset($bs->service_id) && $bs->service_id == $service->id) {
                                                $isChecked = true;
                                                $selectedStylistId = $bs->stylist_id ?? '';
                                                // Format start time if it exists and is an object
                                                $selectedStartTime = ($bs->start_time instanceof \Cake\I18n\Time) ? $bs->start_time->format('H:i') : '';
                                                break;
                                            }
                                        }
                                    ?>
                                    <div class="form-check">
                                        <input class="form-check-input service-checkbox" type="checkbox"
                                               name="bookings_services[<?= $service->id ?>][service_id]"
                                               value="<?= $service->id ?>"
                                               id="service-<?= $service->id ?>"
                                               data-duration="<?= $service->duration_minutes ?? 0 ?>"
                                               data-cost="<?= $service->service_cost ?? '0.00' ?>"
                                               data-selected-stylist-id="<?= $selectedStylistId ?>"
                                               data-selected-start-time="<?= $selectedStartTime ?>"
                                               <?= $isChecked ? 'checked' : '' ?>
                                        >
                                        <label class="form-check-label" for="service-<?= $service->id ?>">
                                            <?= h($service->service_name) ?>
                                            (<?= h($service->duration_minutes ?? '?') ?> mins) -
                                            $<?= number_format((float)($service->service_cost ?? 0), 2) ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-12">
                            <h5><?= __('Please Select The Date') ?></h5>
                            <?php
                            $bookingDate = $booking->booking_date instanceof \Cake\I18n\Date
                                ? $booking->booking_date->format('Y-m-d')
                                : ($booking->booking_date ?? '');

                            echo $this->Form->control('booking_date', [
                                'type' => 'date',
                                'required' => true,
                                'class' => 'form-control' . ($this->Form->isFieldError('booking_date') ? ' is-invalid' : ''),
                                'id' => 'booking-date',
                                'label' => false,
                                'value' => $bookingDate,
                                'error' => ['class' => 'invalid-feedback']
                            ]);
                            ?>
                             <small class="text-muted"><?= __('Date selection might affect available times.') ?></small>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-12">
                            <h5><?= __('Select Stylists & Time for Each Service') ?></h5>
                            <div id="service-stylist-selections">
                                <?php /* This section is populated by booking.js */ ?>
                            </div>
                        </div>
                    </div>
                     <div class="row" style="display: none;"> <?php // Hidden total cost field used by JS ?>
                         <div class="col-md-6">
                             <?php
                             echo $this->Form->control('total_cost', [
                                 'readonly' => true,
                                 'value' => $booking->total_cost ?? '0.00', // Pre-fill with current cost
                                 'id' => 'total-cost-input', // Added ID for potential JS use
                                 'class' => 'form-control',
                                 'error' => ['class' => 'invalid-feedback']
                             ]);
                             ?>
                         </div>
                     </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h5><?= __('Notes (Optional)') ?></h5>
                            <?php
                                echo $this->Form->control('notes', [
                                    'type' => 'textarea',
                                    'label' => false,
                                    'class' => 'form-control' . ($this->Form->isFieldError('notes') ? ' is-invalid' : ''),
                                    'rows' => 3,
                                    'placeholder' => 'Add any special notes or requirements...',
                                    'style' => 'white-space: normal; word-wrap: break-word; overflow-wrap: break-word;',
                                    'error' => ['class' => 'invalid-feedback'],
                                    'maxlength' => 2000,
                                ]);
                            ?>
                        </div>
                    </div>
                </fieldset>
                <div class="selected-services-summary mt-3">
                    <h5><?= __('Selected Services:') ?> <span id="service-count">0</span></h5>
                    <div id="selected-services-list"></div><br>
                    <h5><?= __('Total:') ?> $<span id="service-total">0.00</span></h5>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <?= $this->Form->button(__('Update Your Booking'), [ // Changed Button Text
                            'class' => 'btn btn-primary',
                            // Removed admin-specific confirmation
                        ]) ?>
                        <?= $this->Html->link(__('Cancel Changes'), ['action' => 'customerindex'], ['class' => 'btn btn-secondary']) // Changed Cancel URL ?>
                    </div>
                </div>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>
