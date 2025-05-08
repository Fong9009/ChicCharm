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
            <?= $this->Form->create($service, ['type' => 'file']) ?>
            <fieldset>
                <legend><?= __('Add Service') ?></legend>
                <div class="form-group mb-3">
                    <?= $this->Form->control('service_name', [
                            'label' => __('Service Name*'),
                            'type' => 'text',
                            'class' => 'form-control',
                            'required' => true,
                            'placeholder' => __('Enter Service Name'),
                            'error' => ['Please enter Service Name'],
                        ]); ?>
                </div>
                <div class="form-group mb-3">
                    <?= $this->Form->control('service_desc', [
                        'label' => __('Service Description'),
                        'type' => 'text',
                        'class' => 'form-control',
                        'required' => false,
                        'placeholder' => __('Enter Service Description'),
                        'error' => ['Please enter Service Description'],
                    ]); ?>
                </div>
                <div class="form-group mb-3">
                    <?= $this->Form->control('service_cost', [
                        'label' => __('Service Cost*'),
                        'type' => 'text',
                        'class' => 'form-control',
                        'required' => true,
                        'placeholder' => __('Enter Service Cost'),
                        'error' => ['Please enter Service Cost'],
                    ]); ?>
                </div>
                <div class="form-group mb-3">
                    <?= $this->Form->control('duration_minutes', [
                        'label' => __('Duration (minutes)*'),
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
                <!-- Service Image Preview -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="profile-preview text-start">
                            <?php if (!empty($service->service_image)): ?>
                                <img src="<?= $this->Url->image('service/' . $service->profile_picture) ?>"
                                     alt="Service Picture"
                                     class="img-fluid"
                                     style="width: 400px; height: 400px; object-fit: cover; border-radius: 8px;">
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <!-- File Upload Location -->
                <div class="row">
                    <div class="col-12 col-md-12 col-sm-12">
                        <div class="input">
                            <label for="Service Image">Upload for Service Image</label>
                            <?= $this->Form->Control('service_image', [
                                'type' => 'file',
                                'class' => 'form-control' . ($this->Form->isFieldError('service_image') ? ' is-invalid' : ''),
                                'required' => false,
                                'error' => ['class' => 'invalid-feedback']
                            ]) ?>
                        </div>
                    </div>
                </div>
            </fieldset>
            <?= $this->Form->button(__('Create Service'), ['class' => 'btn btn-primary']) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
