<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Stylist $stylist
 * @var string[]|\Cake\Collection\CollectionInterface $bookings
 * @var string[]|\Cake\Collection\CollectionInterface $services
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
                                 <h4 class="view-card-h4 mb-0 flex-grow-1 text-truncate">Delete Stylist</h4>
                                 <i class="material-icons view-icon ms-2">delete</i>
                            </div>
                            <div class="card-body delete-card-body"></div>
                            <div class="card-footer delete-card-footer">
                                <span class="mb-0 text-truncate">Delete Stylist</span>
                            </div>
                        </div>',
                        ['controller' => 'Services', 'action' => 'delete', $stylist->id],
                        ['escape' => false,'class' => 'card-link-wrapper d-block text-decoration-none',
                            'confirm' => __('Are you sure you want to delete # {0}? You will not be able to get them back!', ($stylist->first_name . ' ' . $stylist->last_name))],
                    ) ?>
                </div>

                <!-- List Service -->
                <div class="col-lg-3 col-md-6 col-sm-12 mb-3 side-nav-item">
                    <?= $this->Html->link(
                        '<div class="card h-100">
                            <div class="card-header list-card-header d-flex justify-content-between align-items-center flex-wrap">
                                <h4 class="view-card-h4 mb-0 flex-grow-1 text-truncate">List Stylists</h4>
                                <i class="material-icons view-icon ms-2">menu</i>
                            </div>
                            <div class="card-body list-card-body"></div>
                            <div class="card-footer list-card-footer">
                                <span class="mb-0 text-truncate">List Stylists</span>
                            </div>
                        </div>',
                        ['controller' => 'Stylists', 'action' => 'index'],
                        ['escape' => false, 'class' => 'card-link-wrapper d-block text-decoration-none']
                    ) ?>
                </div>

                <!-- New Service -->
                <div class="col-lg-3 col-md-6 col-sm-12 mb-3 side-nav-item">
                    <?= $this->Html->link(
                        '<div class="card h-100">
                            <div class="card-header new-card-header d-flex justify-content-between align-items-center flex-wrap">
                                 <h4 class="view-card-h4 mb-0 flex-grow-1 text-truncate">New Stylist</h4>
                                 <i class="material-icons view-icon ms-2">add</i>
                            </div>
                            <div class="card-body new-card-body"></div>
                            <div class="card-footer new-card-footer">
                                <span class="mb-0 text-truncate">Add Stylist</span>
                            </div>
                        </div>',
                        ['controller' => 'Stylists', 'action' => 'add'],
                        ['escape' => false, 'class' => 'card-link-wrapper d-block text-decoration-none']
                    ) ?>
                </div>
            </div>
        </div>
    </aside>
    <div class="column">
        <div class="stylists form content">
            <?= $this->Form->create($stylist) ?>
            <fieldset>
                <legend><?= __('Add Stylist') ?></legend>
                <div class="form-group mb-3">
                    <?= $this->Form->control('first_name', [
                        'label' => __('First Name'),
                        'class' => 'form-control',
                        'required' => true,
                        'placeholder' => __('Enter first name'),
                        'error' => ['class' => 'invalid-feedback'],
                    ]); ?>
                </div>
                <div class="form-group mb-3">
                    <?= $this->Form->control('last_name', [
                        'label' => __('Last Name'),
                        'class' => 'form-control',
                        'required' => true,
                        'placeholder' => __('Enter last name'),
                        'error' => ['class' => 'invalid-feedback'],
                    ]); ?>
                </div>
                <div class="form-group mb-3">
                    <?= $this->Form->control('email', [
                        'label' => __('Email'),
                        'class' => 'form-control',
                        'required' => true,
                        'placeholder' => __('Enter email'),
                        'error' => ['class' => 'invalid-feedback'],
                    ]);?>
                </div>
                <div class="form-group mb-3">
                    <p class="text-muted">Leave password field empty if you don't want to change it.</p>
                    <?= $this->Form->control('password', [
                        'label' => __('Password'),
                        'class' => 'form-control',
                        'required' => false,
                        'value' => '',
                        'error' => ['class' => 'invalid-feedback'],
                    ]); ?>
                </div>
                <?php
                echo $this->Form->control('nonce', ['type' => 'hidden', 'empty' => true]);
                echo $this->Form->control('nonce_expiry', ['type' => 'hidden', 'empty' => true]);
                ?>
                <?php if(empty($services)): ?>
                    <p>No Services available at this time.</p>
                <?php else: ?>
                    <?= $this->Form->control('services._ids',[
                        'label' => __('Services'),
                        'type' => 'select',
                        'multiple' => 'checkbox',
                        'options' => $services,
                    ]); ?>
                <?php endif; ?>
            </fieldset>
            <?= $this->Form->button(__('Submit'), ['class' => 'btn btn-primary']) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
