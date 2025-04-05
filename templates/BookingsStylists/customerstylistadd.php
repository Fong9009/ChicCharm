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
    <div class="column column-80">
        <div class="bookingsStylists form content">
            <?= $this->Form->create($bookingsStylist) ?>
            <fieldset>
                <legend><?= __('Add Bookings Stylist') ?></legend>
                <div class="filter-section">
                    <?= $this->Form->create(null, ['type' => 'get', 'url' => ['action' => 'filter']]) ?>

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
                    <?= $this->Form->button(__('Filter Stylists')) ?>

                    <?= $this->Form->end() ?>
                </div>
                <table>
                    <thead>
                    <tr>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Service</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($filterStylists as $stylist): ?>
                        <tr>
                            <td><?= h($stylist->first_name) ?></td>
                            <td><?= h($stylist->last_name) ?></td>
                            <td><?= h($stylist->service_name) ?></td>
                            <td>
                                <?php if ($stylist->first_name !== null):  ?>
                                    <?= $this->Form->postLink(
                                    'Add Contractor',
                                    ['controller' => 'BookingsStylists', 'action' => 'addContractor', $stylist->id],
                                    ['confirm' => 'Are you sure you want to add this contractor?', 'class' => 'btn btn-primary']
                                ) ?>
                                <?php endif ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>

