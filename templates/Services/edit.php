<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Service $service
 * @var string[]|\Cake\Collection\CollectionInterface $stylists
 */
?>
<?= $this->Html->css('/utility/edits/edits.css') ?>
<?= $this->Html->css('https://fonts.googleapis.com/icon?family=Material+Icons') ?>
<div class="contacts index content">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <!-- Edit Services -->
            <div class="row gx-2">
                <div class="col-lg-3 col-md-6 col-sm-12 mb-3 side-nav-item">
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
                <!-- Delete Service -->
                <div class="col-lg-3 col-md-6 col-sm-12 mb-3 side-nav-item">
                    <?= $this->Form->postLink(
                        '<div class="card h-100">
                            <div class="card-header delete-card-header d-flex justify-content-between align-items-center flex-wrap">
                                 <h4 class="view-card-h4 mb-0 flex-grow-1 text-truncate">Delete Service</h4>
                                 <i class="material-icons view-icon ms-2">delete</i>
                            </div>
                            <div class="card-body delete-card-body"></div>
                            <div class="card-footer delete-card-footer">
                                <span class="mb-0 text-truncate">Delete Service</span>
                            </div>
                        </div>',
                        ['controller' => 'Services', 'action' => 'delete', $service->id],
                        ['escape' => false,'class' => 'card-link-wrapper d-block text-decoration-none',
                            'confirm' => __('Are you sure you want to delete # {0}? You will not be able to get it back', $service->service_name)],
                    ) ?>
                </div>

                <!-- List Service -->
                <div class="col-lg-3 col-md-6 col-sm-12 mb-3 side-nav-item">
                    <?= $this->Html->link(
                        '<div class="card h-100">
                            <div class="card-header list-card-header d-flex justify-content-between align-items-center flex-wrap">
                                <h4 class="view-card-h4 mb-0 flex-grow-1 text-truncate">List Services</h4>
                                <i class="material-icons view-icon ms-2">menu</i>
                            </div>
                            <div class="card-body list-card-body"></div>
                            <div class="card-footer list-card-footer">
                                <span class="mb-0 text-truncate">List Services</span>
                            </div>
                        </div>',
                        ['controller' => 'Services', 'action' => 'index'],
                        ['escape' => false, 'class' => 'card-link-wrapper d-block text-decoration-none']
                    ) ?>
                </div>

                <!-- New Service -->
                <div class="col-lg-3 col-md-6 col-sm-12 mb-3 side-nav-item">
                    <?= $this->Html->link(
                        '<div class="card h-100">
                            <div class="card-header new-card-header d-flex justify-content-between align-items-center flex-wrap">
                                 <h4 class="view-card-h4 mb-0 flex-grow-1 text-truncate">New Service</h4>
                                 <i class="material-icons view-icon ms-2">add</i>
                            </div>
                            <div class="card-body new-card-body"></div>
                            <div class="card-footer new-card-footer">
                                <span class="mb-0 text-truncate">Add Service</span>
                            </div>
                        </div>',
                        ['controller' => 'Services', 'action' => 'add'],
                        ['escape' => false, 'class' => 'card-link-wrapper d-block text-decoration-none']
                    ) ?>
                </div>
            </div>
        </div>
    </aside>
    <div class="column">
        <div class="services form content">
            <?= $this->Form->create($service) ?>
            <fieldset>
                <legend><?= __('Edit Service') ?></legend>
                <div class="form-group mb-3">
                    <?=$this->Form->control('service_name',[
                        'label' => __('Service Name'),
                        'class' => 'form-control',
                        'required' => true,
                        'placeholder' => __('Service Name'),
                        'error' => ['class' => 'invalid-feedback'],
                    ]); ?>
                </div>
                <div class="form-group mb-3">
                    <?= $this->Form->control('service_cost', [
                        'label' => __('Service Cost'),
                        'class' => 'form-control',
                        'required' => true,
                        'placeholder' => __('Service Cost'),
                        'error' => ['class' => 'invalid-feedback'],
                    ]); ?>
                </div>
                <div class="form-group mb-3">
                    <?= $this->Form->control('duration_minutes', [
                        'label' => __('Duration (minutes)'),
                        'class' => 'form-control',
                        'required' => true,
                        'placeholder' => __('Enter duration in minutes'),
                        'error' => ['class' => 'invalid-feedback'],
                        'min' => 15,
                        'max' => 480,
                        'step' => 15,
                        'type' => 'number'
                    ]); ?>
                    <small class="form-text text-muted">Duration must be in 15-minute increments, between 15 minutes and 8 hours.</small>
                </div>
            </fieldset>
            <?= $this->Form->button(__('Submit'), ['class' => 'btn btn-primary']) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
