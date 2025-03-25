<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Customer $customer
 */
?> 
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="enquiry-form">
                <h2>Registration Form</h2>
                <?= $this->Form->create($customer, ['type' => 'file', 'novalidate' => true]) ?>
                <?= $this->Flash->render() ?>
                <div class="form-group mb-3">
                    <?= $this->Form->control('email', [
                        'label' => 'Email', 
                        'class' => 'form-control',
                        'required' => true,
                        'error' => ['class' => 'invalid-feedback']
                    ]) ?>
                </div>
                <div class="form-group mb-3">
                    <?= $this->Form->control('first_name', [
                        'label' => 'First Name', 
                        'class' => 'form-control',
                        'required' => true,
                        'error' => ['class' => 'invalid-feedback']
                    ]) ?>
                </div>
                <div class="form-group mb-3">
                    <?= $this->Form->control('last_name', [
                        'label' => 'Last Name', 
                        'class' => 'form-control',
                        'required' => true,
                        'error' => ['class' => 'invalid-feedback']
                    ]) ?>
                </div>
                <div class="form-group mb-3">
                    <?= $this->Form->control('password', [
                        'label' => 'Password', 
                        'class' => 'form-control',
                        'required' => true,
                        'error' => ['class' => 'invalid-feedback']
                    ]) ?>
                    <small class="form-text text-muted">Password must be at least 8 characters long.</small>
                </div>
                <div class="form-group mb-3">
                    <?= $this->Form->control('password_confirm', [
                        'type' => 'password', 
                        'label' => 'Confirm Password', 
                        'class' => 'form-control',
                        'required' => true,
                        'error' => ['class' => 'invalid-feedback']
                    ]) ?>
                </div>
                <div class="d-flex justify-content-between mt-4">
                    <?= $this->Form->button('Register', ['class' => 'btn btn-primary']) ?>
                    <?= $this->Html->link('Back to Login', ['controller' => 'Auth', 'action' => 'login'], ['class' => 'btn btn-link']) ?>
                </div>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>
