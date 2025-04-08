<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Customer $customer
 */
?> 
<div class="registration-wrapper">
    <div class="registration-form">
        <h2>Registration Form</h2>
        <?= $this->Form->create($customer, ['type' => 'file', 'novalidate' => true]) ?>
        <?= $this->Flash->render() ?>
        <div class="input">
            <?= $this->Form->control('email', [
                'label' => 'Email', 
                'class' => 'form-control',
                'required' => true,
                'placeholder' => 'Enter your email',
                'error' => ['class' => 'invalid-feedback']
            ]) ?>
        </div>
        <div class="input">
            <?= $this->Form->control('first_name', [
                'label' => 'First Name', 
                'class' => 'form-control',
                'required' => true,
                'placeholder' => 'Enter your first name',
                'error' => ['class' => 'invalid-feedback']
            ]) ?>
        </div>
        <div class="input">
            <?= $this->Form->control('last_name', [
                'label' => 'Last Name', 
                'class' => 'form-control',
                'required' => true,
                'placeholder' => 'Enter your last name',
                'error' => ['class' => 'invalid-feedback']
            ]) ?>
        </div>
        <div class="input">
            <?= $this->Form->control('password', [
                'label' => 'Password', 
                'class' => 'form-control',
                'required' => true,
                'placeholder' => 'Enter your password',
                'error' => ['class' => 'invalid-feedback']
            ]) ?>
            <small class="text-muted">Password must be at least 8 characters long.</small>
        </div>
        <div class="input">
            <?= $this->Form->control('password_confirm', [
                'type' => 'password', 
                'label' => 'Confirm Password', 
                'class' => 'form-control',
                'required' => true,
                'placeholder' => 'Confirm your password',
                'error' => ['class' => 'invalid-feedback']
            ]) ?>
        </div>
        <?= $this->Form->button('Register', ['class' => 'btn btn-primary']) ?>
        <div class="text-center mt-3">
            <?= $this->Html->link('Back to Login', ['controller' => 'Auth', 'action' => 'login'], ['class' => 'btn btn-link']) ?>
        </div>
        <?= $this->Form->end() ?>
    </div>
</div>
