<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\BookingsStylist $bookingsStylist
 * @var \Cake\Collection\CollectionInterface|string[] $bookings
 * @var \Cake\Collection\CollectionInterface|string[] $stylists
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Bookings Stylists'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column">
        <div class="enquiry-form">
            <?= $this->Form->create($bookingsStylist) ?>
            <fieldset>
                <legend><?= __('Add Stylist To Booking') ?></legend>
                <div class="filter-section">
                    <?= $this->Form->create(null, ['type' => 'get', 'url' => ['action' => 'filter']]) ?>

                    <?= $this->Form->hidden('filter', ['value' => '1']) ?>
                    <?= $this->Form->hidden('booking_id', ['value' => $booking_id]) ?>
                    <!-- Service Dropdown -->
                    <?= $this->Form->control('service_id', [
                        'type' => 'select',
                        'options' => $services, // Make sure $services is passed from the controller
                        'label' => 'Select Service',
                        'empty' => 'Choose a service...'
                    ]) ?>

                    <?= $this->Form->control('start_time', [
                        'type' => 'time',
                        'label' => 'Start Time',
                        'required' => true
                    ]) ?>

                    <?= $this->Form->control('end_time', [
                        'type' => 'time',
                        'label' => 'End Time',
                        'required' => true
                    ]) ?>

                    <!-- Filter Button -->
                    <?= $this->Form->button(__('Filter Stylists'), ['class' => 'btn btn-primary']) ?>

                    <?= $this->Form->end() ?>
                </div>
                <div class="contacts index content">
                    <table>
                        <thead>
                        <tr>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Service</th>
                            <th>Total Price</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($filterStylists as $stylist): ?>
                            <tr>
                                <td><?= h($stylist->first_name) ?></td>
                                <td><?= h($stylist->last_name) ?></td>
                                <td><?= h($nameOfService) ?></td>
                                <td><?= h($totalServicePrice) ?></td>
                                <td class="actions">
                                    <?php if ($stylist->first_name !== null):  ?>
                                        <?= $this->Html->link(
                                            'Add Stylist',
                                            [
                                                'controller' => 'BookingsStylists',
                                                'action' => 'addStylist',
                                                $stylist->id,
                                                $booking_id,
                                                $this->request->getData('start_time'),
                                                $this->request->getData('end_time'),
                                                $totalServicePrice,
                                            ],
                                            ['class' => 'btn btn-primary', 'confirm' => 'Add this Stylist To Booking?']
                                        ) ?>
                                    <?php endif ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </fieldset>
        </div>
    </div>
</div>

