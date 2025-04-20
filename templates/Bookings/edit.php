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
    const apiUrl = '<?= $this->Url->build("/bookings/get-stylists") ?>';
    const apiUrl2 = '<?= $this->Url->build("/bookings/get-available-time-slots") ?>';
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
                    <div class="col-lg-4 col-md-6 col-sm-12 mb-3 side-nav-item">
                        <?= $this->Html->link(
                            '<div class="card h-100">
                                <div class="card-header new-card-header d-flex justify-content-between align-items-center flex-wrap">
                                    <h4 class="view-card-h4 mb-0 flex-grow-1 text-truncate">New Booking</h4>
                                    <i class="material-icons view-icon ms-2">add</i>
                                </div>
                                <div class="card-body new-card-body"></div>
                                <div class="card-footer new-card-footer">
                                    <span class="mb-0 text-truncate">Add New Booking</span>
                                </div>
                            </div>',
                            ['action' => 'adminbooking'],
                            ['escape' => false, 'class' => 'card-link-wrapper d-block text-decoration-none']
                        ) ?>
                    </div>
                    <?php if ($booking->status !== 'active'): ?>
                        <div class="col-lg-4 col-md-6 col-sm-12 mb-3 side-nav-item">
                            <?= $this->Form->postLink(
                                '<div class="card h-100">
                                    <div class="card-header delete-card-header d-flex justify-content-between align-items-center flex-wrap">
                                        <h4 class="view-card-h4 mb-0 flex-grow-1 text-truncate">Delete Booking</h4>
                                        <i class="material-icons view-icon ms-2">delete</i>
                                    </div>
                                    <div class="card-body delete-card-body"></div>
                                    <div class="card-footer delete-card-footer">
                                        <span class="mb-0 text-truncate">Delete This Booking</span>
                                    </div>
                                </div>',
                                ['action' => 'delete', $booking->id],
                                [
                                    'confirm' => __('Are you sure you want to permanently delete this cancelled booking?'),
                                    'class' => 'card-link-wrapper d-block text-decoration-none',
                                    'escape' => false
                                ]
                            ) ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </aside>
        <div class="column-edit">
            <div class="bookings form content">
                <?= $this->Form->create($booking, ['id' => 'booking-form']) ?>
                <fieldset>
                    <h2 class="text-center"><?= __('Edit Booking') ?></h2>
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
                                               data-cost="<?= $service->service_cost ?>"
                                               <?php
                                               // Check if this service is already selected in the booking
                                               foreach ($booking->bookings_services as $booking_service) {
                                                   if ($booking_service->service_id == $service->id) {
                                                       echo 'checked';
                                                       break;
                                                   }
                                               }
                                               ?>>
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
                                'label' => false,
                                'min' => date('Y-m-d'),
                                'max' => date('Y-m-d', strtotime('+1 year')),
                                'error' => ['class' => 'invalid-feedback'],
                                'value' => $booking->booking_date->format('Y-m-d')
                            ]);
                            ?>
                            <small class="text-muted">Please select at least one service first</small>
                        </div>
                        <div class="col-md-6">
                            <h5>Please Select The Time</h5>
                            <?php
                            // Calculate total duration of selected services
                            $totalDuration = 0;
                            foreach ($booking->bookings_services as $booking_service) {
                                $totalDuration += $booking_service->service->duration_minutes;
                            }
                            
                            echo $this->Form->control('start_time', [
                                'type' => 'select',
                                'options' => array_reduce(
                                    range(9 * 4, 17 * 4),
                                    function($options, $i) use ($booking, $totalDuration) {
                                        $hour = floor($i / 4);
                                        $minute = ($i % 4) * 15;
                                        $startTimeStr = sprintf('%02d:%02d', $hour, $minute);
                                        
                                        // Calculate end time based on service duration
                                        $endTime = strtotime("+" . $totalDuration . " minutes", strtotime($startTimeStr));
                                        // Don't show slots that would end after 5 PM
                                        if (date('H', $endTime) >= 17) {
                                            return $options;
                                        }
                                        $endTimeStr = date('H:i', $endTime);
                                        
                                        // Format for display
                                        $startDisplay = date('g:i A', strtotime($startTimeStr));
                                        $endDisplay = date('g:i A', strtotime($endTimeStr));
                                        
                                        $displayTime = $startDisplay . ' - ' . $endDisplay;
                                        $options[$startTimeStr] = $displayTime;
                                        
                                        return $options;
                                    },
                                    ['' => 'Select a time slot']
                                ),
                                'class' => 'form-control',
                                'id' => 'start-time',
                                'label' => false,
                                'required' => true,
                                'value' => $booking->start_time->format('H:i')
                            ]);
                            ?>
                            <small class="text-muted">Please select at least one service first</small>
                            <div id="time-range-display" class="mt-2" style="display: none;" hidden>
                                <span id="start-time-display"></span> - <span id="end-time-display"></span>
                            </div>
                            <?php echo $this->Form->hidden('end_time', [
                                'id' => 'end-time',
                                'value' => $booking->end_time->format('H:i')
                            ]); ?>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Select Stylists for Each Service</h5>
                            <div id="service-stylist-selections">
                                <?php foreach ($booking->bookings_services as $booking_service): ?>
                                    <div class="stylist-selection mb-3" data-service-id="<?= $booking_service->service_id ?>">
                                        <label>Stylist for <?= $booking_service->service->service_name ?>:</label>
                                        <select name="bookings_services[<?= $booking_service->service_id ?>][stylist_id]" class="form-control" required>
                                            <option value="">Select a stylist...</option>
                                            <?php foreach ($stylists as $id => $name): ?>
                                                <option value="<?= $id ?>" <?= $booking_service->stylist_id == $id ? 'selected' : '' ?>>
                                                    <?= h($name) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                <?php endforeach; ?>
                            </div>
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
                        <?= $this->Form->button(__('Update Booking'), [
                            'class' => 'btn btn-primary',
                            'onclick' => 'return confirm("Are you sure you want to update this booking? This will notify the customer of the changes.");'
                        ]) ?>
                        <?= $this->Html->link(__('Cancel'), ['action' => 'index'], ['class' => 'btn btn-secondary']) ?>
                    </div>
                </div>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>

<script>
// Initialize the service count and total on page load
document.addEventListener('DOMContentLoaded', function() {
    updateServiceSummary();
});

function updateServiceSummary() {
    const checkedServices = document.querySelectorAll('.service-checkbox:checked');
    const serviceCount = checkedServices.length;
    let total = 0;
    const selectedServicesList = document.getElementById('selected-services-list');
    selectedServicesList.innerHTML = '';

    checkedServices.forEach(service => {
        const cost = parseFloat(service.dataset.cost);
        total += cost;
        const serviceName = service.nextElementSibling.textContent.trim();
        selectedServicesList.innerHTML += `<div>${serviceName}</div>`;
    });

    document.getElementById('service-count').textContent = serviceCount;
    document.getElementById('service-total').textContent = total.toFixed(2);
    document.getElementById('total_cost').value = total.toFixed(2);
}

// Add event listeners to service checkboxes
document.querySelectorAll('.service-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', updateServiceSummary);
});
</script>
