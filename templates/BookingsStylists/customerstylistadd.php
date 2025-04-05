<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\BookingsStylist $bookingsStylist
 * @var \Cake\Collection\CollectionInterface|string[] $bookings
 * @var \Cake\Collection\CollectionInterface|string[] $stylists
 */

$this->Html->scriptBlock('
    $(document).ready(function () {
        function set30MinuteIntervals() {
            var startTimeField = $("input[name=\'start_time\']");
            var endTimeField = $("input[name=\'end_time\']");

            // Restrict start time to 30-minute intervals
            startTimeField.on("input", function () {
                var value = startTimeField.val();
                if (value) {
                    var minutes = parseInt(value.split(":")[1], 10);
                    if (minutes % 30 !== 0) {
                        var newMinutes = (Math.round(minutes / 30) * 30) % 60;
                        startTimeField.val(value.split(":")[0] + ":" + ("0" + newMinutes).slice(-2));
                    }
                }
            });

            // Restrict end time to 30-minute intervals
            endTimeField.on("input", function () {
                var value = endTimeField.val();
                if (value) {
                    var minutes = parseInt(value.split(":")[1], 10);
                    if (minutes % 30 !== 0) {
                        var newMinutes = (Math.round(minutes / 30) * 30) % 60;
                        endTimeField.val(value.split(":")[0] + ":" + ("0" + newMinutes).slice(-2));
                    }
                }
            });
        }

        set30MinuteIntervals();
    });
');
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

