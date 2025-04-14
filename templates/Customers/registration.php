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
                'class' => 'form-control' . ($this->Form->isFieldError('email') ? ' is-invalid' : ''),
                'required' => true,
                'placeholder' => 'Enter your email',
                'error' => ['class' => 'invalid-feedback']
            ]) ?>
        </div>
        <div class="input">
            <?= $this->Form->control('first_name', [
                'label' => 'First Name',
                'class' => 'form-control' . ($this->Form->isFieldError('first_name') ? ' is-invalid' : ''),
                'required' => true,
                'placeholder' => 'Enter your first name',
                'error' => ['class' => 'invalid-feedback']
            ]) ?>
        </div>
        <div class="input">
            <?= $this->Form->control('last_name', [
                'label' => 'Last Name',
                'class' => 'form-control' . ($this->Form->isFieldError('last_name') ? ' is-invalid' : ''),
                'required' => true,
                'placeholder' => 'Enter your last name',
                'error' => ['class' => 'invalid-feedback']
            ]) ?>
        </div>
        <div class="input">
            <?= $this->Form->control('password', [
                'label' => 'Password',
                'class' => 'form-control' . ($this->Form->isFieldError('password') ? ' is-invalid' : ''),
                'required' => true,
                'placeholder' => 'Enter your password',
                'error' => ['class' => 'invalid-feedback']
            ]) ?>
        </div>
        <div class="input">
            <?= $this->Form->control('password_confirm', [
                'type' => 'password',
                'label' => 'Confirm Password',
                'class' => 'form-control' . ($this->Form->isFieldError('password_confirm') ? ' is-invalid' : ''),
                'required' => true,
                'placeholder' => 'Confirm your password',
                'error' => ['class' => 'invalid-feedback']
            ]) ?>
        </div>
        <div class="password-requirements mb-3">
                <small class="text-muted">
                    <h6>Password Requirement:</h6>
                    <ul>
                        <li>At least 8 characters long</li>
                    </ul>
                </small>
            </div>
        <div class="text-center mt-3">
            <?= $this->Form->button('Register', ['class' => 'btn btn-primary']) ?>
            <br>
            <?= $this->Html->link(__('Cancel'),
                ['controller' => 'Auth', 'action' => 'login'],
                ['class' => 'btn btn-secondary',
                'style' => 'background-color: #6c757d; border-color: #6c757d; color: white; transition: all 0.3s;',
                'onmouseover' => 'this.style.backgroundColor = "#5a6268"; this.style.borderColor = "#545b62";',
                'onmouseout' => 'this.style.backgroundColor = "#6c757d"; this.style.borderColor = "#6c757d";']) ?>
        </div>
        <?= $this->Form->end() ?>
    </div>
</div>
