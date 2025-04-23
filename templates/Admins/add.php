<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Admin $admin
 */
?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="admins form content">
                <h2>Register New Admin</h2>
                <?= $this->Form->create($admin) ?>
                <div class="form-group">
                    <?= $this->Form->control('email', [
                        'type' => 'email',
                        'label' => 'Email',
                        'class' => 'form-control' . ($this->Form->isFieldError('email') ? ' is-invalid' : ''),
                        'required' => true,
                        'placeholder' => 'Enter your email',
                        'error' => ['class' => 'invalid-feedback']
                    ]) ?>
                </div>
                <div class="form-group">
                    <?= $this->Form->control('first_name', [
                        'type' => 'text',
                        'label' => 'First Name',
                        'class' => 'form-control' . ($this->Form->isFieldError('first_name') ? ' is-invalid' : ''),
                        'required' => true,
                        'placeholder' => 'Enter your first name',
                        'error' => ['class' => 'invalid-feedback']
                    ]) ?>
                </div>
                <div class="form-group">
                    <?= $this->Form->control('last_name', [
                        'type' => 'text',
                        'label' => 'Last Name',
                        'class' => 'form-control' . ($this->Form->isFieldError('last_name') ? ' is-invalid' : ''),
                        'required' => true,
                        'placeholder' => 'Enter your last name',
                        'error' => ['class' => 'invalid-feedback']
                    ]) ?>
                </div>
                <div class="form-group">
                    <?= $this->Form->control('password', [
                        'type' => 'password',
                        'label' => 'Password',
                        'class' => 'form-control' . ($this->Form->isFieldError('password') ? ' is-invalid' : ''),
                        'required' => true,
                        'placeholder' => 'Enter your password',
                        'error' => ['class' => 'invalid-feedback']
                    ]) ?>
                </div>
                <div class="form-group">
                    <?= $this->Form->control('password_confirm', [
                        'type' => 'password',
                        'label' => 'Retype Password',
                        'class' => 'form-control' . ($this->Form->isFieldError('password_confirm') ? ' is-invalid' : ''),
                        'required' => true,
                        'placeholder' => 'Retype your password',
                        'error' => ['class' => 'invalid-feedback']
                    ]) ?>
                </div>
                <div class="password-requirements mb-3">
                    <small class="text-muted">
                        <h6>Password Requirements:</h6>
                        <ul>
                            <li>At least 8 characters long</li>
                            <li>At least one uppercase letter, one number and one special character (@$!%*?&)</li>
                        </ul>
                    </small>
                </div>
                <div class="text-center mt-3">
                    <?= $this->Form->button('Register', ['class' => 'btn btn-primary']) ?>
                    <br>
                    <br>
                    <?= $this->Html->link(__('Cancel'),
                        ['action' => 'index'],
                        ['class' => 'btn btn-secondary',
                        'style' => 'background-color: #6c757d; border-color: #6c757d; color: white; transition: all 0.3s;',
                        'onmouseover' => 'this.style.backgroundColor = "#5a6268"; this.style.borderColor = "#545b62";',
                        'onmouseout' => 'this.style.backgroundColor = "#6c757d"; this.style.borderColor = "#6c757d";']) ?>
                </div>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>
