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
    const apiUrl = '<?= $this->Url->build("/bookings/get-stylists-for-service/") ?>';
    const apiUrl2 = '<?= $this->Url->build("/bookings/get-available-time-slots") ?>';
    const apiUrl3 = '<?= $this->Url->build("/bookings/get-availability-count") ?>';
</script>
<div class="booking-form-wrapper " style="background-image: url(<?= $this->Url->image('gradient.jpg')?>);">
    <div class="row">
        <div class="column-edit">
            <div class="bookings form content admin-border" id="booking-form">
                <?= $this->Form->create($booking) ?>
                <fieldset>
                    <h2 class="text-center"><?= __('Guest Booking') ?></h2><br>
                    <div class="row">
                        <div>
                            <h5>Important Notes</h5>
                            <p>- Guest bookings will not save unless payment has been completed. <br>
                                - You will be emailed an invoice, on the payment of your booking. <br>
                                - If you want us to make a booking on your behalf, <?= $this->Html->link(__('Contact Us'), ['controller' => 'contacts', 'action' => 'enquiry']) ?>
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <div>
                            <h5>Want to Sign in?</h5>
                            <p>Join us for installment payment, booking management and much more.</p>
                            <?= $this->Html->link(__('Sign in'), ['controller' => 'Auth', 'action' => 'guesttransfer'], ['class' => 'btn btn-secondary w-25']) ?>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div>
                            <?= $this->Form->control('customer_name', [
                                'label' => 'Customer Name',
                                'placeholder' => 'Enter your name',
                                'class' => 'form-control',
                                'required' => true,
                                'pattern' => "[A-Za-z ']+",
                                'error' => ['class' => 'invalid-feedback'],
                                'maxlength' => 100,
                            ]) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6" style="margin-bottom: 16px;">
                            <?= $this->Form->control('email', [
                                'label' => 'Email',
                                'class' => 'form-control',
                                'required' => true,
                                'placeholder' => 'Enter your email',
                                'type' => 'email',
                                'error' => ['class' => 'invalid-feedback']
                            ]) ?>
                        </div>
                        <div class="col-md-6" style="margin-bottom: 16px;">
                            <?= $this->Form->control('phone_number', [
                                'label' => 'Phone Number',
                                'class' => 'form-control',
                                'required' => true,
                                'pattern' => '[0-9]{10}',
                                'title' => 'Please enter a valid 10-digit phone number',
                                'placeholder' => 'Enter your 10-digit phone number',
                                'error' => ['class' => 'invalid-feedback'],
                                'maxlength' => 10,
                            ]) ?>
                        </div>
                    </div>
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
                        <div class="col-md-12">
                            <h4>Please Select The Date</h4>
                            <?php
                            echo $this->Form->control('booking_date', [
                                'type' => 'date',
                                'required' => true,
                                'oninvalid' => "this.setCustomValidity('Please select a Date')",
                                'oninput' => "this.setCustomValidity('')",
                                'class' => 'form-control' . ($this->Form->isFieldError('booking_date') ? ' is-invalid' : ''),
                                'id' => 'booking-date',
                                'disabled' => true,
                                'label' => false,
                                'error' => ['class' => 'invalid-feedback']
                            ]);
                            ?>
                            <small class="text-muted">Please select service(s) first</small>
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
                    <div class="col-md-12" style="margin-top: 20px;">
                        <?= $this->Recaptcha->display(['class' => 'mb-3 d-flex justify-content-center'])?>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12" style="margin-top: 20px;">
                        <?= $this->Form->button(__('Create Booking'), ['class' => 'btn btn-primary']) ?>
                        <?= $this->Html->link(__('Cancel'), ['controller' => 'Auth', 'action' => 'guestcancel'], ['class' => 'btn btn-secondary']) ?>
                    </div>
                </div>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>
</div>
