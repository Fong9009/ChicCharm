<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Stylist $stylist
 * @var \Cake\Collection\CollectionInterface|string[] $bookings
 * @var \Cake\Collection\CollectionInterface|string[] $services
 */
?>
<div class="container">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Stylists'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('Back To Dashboard'), ['controller' => 'Admins', 'action' => 'dashboard'], ['class' => 'side-nav-item']) ?>
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
                    <?= $this->Form->control('password', [
                        'label' => __('Password'),
                        'class' => 'form-control',
                        'required' => true,
                        'placeholder' => __('Enter password'),
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
