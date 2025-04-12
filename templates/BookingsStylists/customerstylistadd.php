<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\BookingsStylist $bookingsStylist
 * @var \Cake\Collection\CollectionInterface|string[] $bookings
 * @var \Cake\Collection\CollectionInterface|string[] $stylists
 */

$this->Html->css('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css', ['block' => true]);
$this->Html->css('https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css', ['block' => true]);
$this->Html->css('custom', ['block' => true]);
?>
<?php debug($booking_id); ?>
<div class="custom-list-wrapper">
    <div class="row">
        <div class="contacts index content">
            <div class="table-responsive">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-2" style="padding: 10px 20px;">
                            <?= $this->Html->link(__('Back to Dashboard'), ['controller' => 'Admins', 'action' => 'dashboard'], ['class' => 'btn btn-primary', 'style' => 'white-space: nowrap;']) ?>
                        </div>
                        <div class="col-8">
                            <h3><?= __('Add Stylist To Booking') ?></h3>
                        </div>
                    </div>
                </div>
                <div class="search-filter-container">
                    <?= $this->Form->create(null, ['type' => 'post', 'url' => ['action' => 'customerstylistadd', $booking_id], 'class' => 'filter-form']) ?>
                    <?= $this->Form->hidden('filter', ['value' => '1']) ?>
                    <div class="row">
                        <div class="col-md-4">
                            <?= $this->Form->control('service_id', [
                                'type' => 'select',
                                'options' => $services, // Make sure $services is passed from the controller
                                'label' => 'Select Service',
                                'empty' => 'Choose a service...',
                                'class' => 'form-control'
                            ]) ?>
                        </div>
                        <div class="col-md-3">
                            <?= $this->Form->control('start_time', [
                                'type' => 'time',
                                'label' => 'Start Time',
                                'required' => true,
                                'class' => 'form-control'
                            ]) ?>
                        </div>
                        <div class="col-md-3">
                            <?= $this->Form->control('end_time', [
                                'type' => 'time',
                                'label' => 'End Time',
                                'required' => true,
                                'class' => 'form-control'
                            ]) ?>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <?= $this->Form->button(__('Filter Stylists'), ['class' => 'btn btn-primary']) ?>
                        </div>
                    </div>
                    <?= $this->Form->end() ?>
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th><?= $this->Paginator->sort('first_name') ?></th>
                            <th><?= $this->Paginator->sort('last_name') ?></th>
                            <th><?= $this->Paginator->sort('service') ?></th>
                            <th><?= $this->Paginator->sort('total_price') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
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
                                <?php if ($stylist->first_name !== null): ?>
                                <div class="dropdown">
                                    <button type="button" class="btn btn-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <?= $this->Form->postLink(
                                                __('Add Stylist'),
                                                ['controller' => 'BookingsStylists', 'action' => 'addStylist', $booking_id],
                                                [
                                                    'data' => [
                                                        'stylist_id' => $stylist->id,
                                                        'start_time' => $this->request->getQuery('start_time'),
                                                        'end_time' => $this->request->getQuery('end_time'),
                                                        'service_id' => $this->request->getQuery('service_id')
                                                    ],
                                                    'class' => 'dropdown-item',
                                                    'confirm' => 'Add this Stylist To Booking?'
                                                ]
                                            ) ?>
                                        </li>
                                    </ul>
                                </div>
                                <?php endif ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="paginator">
                <ul class="pagination">
                    <?= $this->Paginator->first('<< ' . __('first')) ?>
                    <?= $this->Paginator->prev('< ' . __('previous')) ?>
                    <?= $this->Paginator->numbers() ?>
                    <?= $this->Paginator->next(__('next') . ' >') ?>
                    <?= $this->Paginator->last(__('last') . ' >>') ?>
                </ul>
                <p><?= $this->Paginator->counter(__('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')) ?></p>
            </div>
        </div>
    </div>
</div>

<?php
$this->Html->script('https://code.jquery.com/jquery-3.6.0.min.js', ['block' => true]);
$this->Html->script('https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js', ['block' => true]);
$this->Html->script('custom', ['block' => true]);
?>

