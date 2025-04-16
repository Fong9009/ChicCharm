<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Service $service
 * @var \Cake\Collection\CollectionInterface|string[] $stylists
 */
?>
<?= $this->Html->css('/utility/adds/adds.css') ?>
<?= $this->Html->css('https://fonts.googleapis.com/icon?family=Material+Icons') ?>
<div class="contacts index content">
    <!-- Action Menu -->
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
                                <h4 class="view-card-h4 mb-0 flex-grow-1 text-truncate">List Services</h4>
                                <i class="material-icons view-icon ms-2">list</i>
                            </div>
                            <div class="card-body list-card-body"></div>
                            <div class="card-footer list-card-footer">
                                <span  class="mb-0 text-truncate">List Services</span>
                            </div>
                        </div>',
                        ['controller' => 'Services', 'action' => 'index'],
                        ['escape' => false, 'class' => 'card-link-wrapper d-block text-decoration-none']
                    ) ?>
                </div>
            </div>
        </div>
    </aside>
    <div class="column">
        <div class="enquiry-form">
            <?= $this->Form->create($service) ?>
            <fieldset>
                <legend><?= __('Add Service') ?></legend>
                <div class="form-group mb-3">
                    <?= $this->Form->control('service_name', [
                            'label' => __('Service Name'),
                            'type' => 'text',
                            'class' => 'form-control',
                            'required' => true,
                            'placeholder' => __('Enter Service Name'),
                            'error' => ['Please enter Service Name'],
                        ]); ?>
                </div>
                <div class="form-group mb-3">
                    <?= $this->Form->control('service_cost', [
                        'label' => __('Service Cost'),
                        'type' => 'text',
                        'class' => 'form-control',
                        'required' => true,
                        'placeholder' => __('Enter Service Cost'),
                        'error' => ['Please enter Service Cost'],
                    ]); ?>
                </div>
                <div class="form-group mb-3">
                    <?= $this->Form->control('duration_minutes', [
                        'label' => __('Duration (minutes)'),
                        'type' => 'number',
                        'class' => 'form-control',
                        'required' => true,
                        'placeholder' => __('Enter duration in minutes'),
                        'error' => ['Please enter duration in minutes'],
                        'min' => 15,
                        'max' => 480,
                        'step' => 15
                    ]); ?>
                    <small class="form-text text-muted">Duration must be in 15-minute increments, between 15 minutes and 8 hours.</small>
                </div>
            </fieldset>
            <?= $this->Form->button(__('Submit'), ['class' => 'btn btn-primary']) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
