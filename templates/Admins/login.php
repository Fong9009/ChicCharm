<?php
/**
 * @var \App\View\AppView $this
 */
?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="login-form">
                <h2>Admin Login</h2>
                <?= $this->Form->create() ?>
                <?= $this->Form->control('email', [
                    'label' => 'Email',
                    'class' => 'form-control',
                    'required' => true,
                    'placeholder' => 'Enter your email'
                ]) ?>
                <?= $this->Form->control('password', [
                    'label' => 'Password',
                    'class' => 'form-control',
                    'required' => true,
                    'placeholder' => 'Enter your password'
                ]) ?>
                <?= $this->Form->button(__('Login'), ['class' => 'submit-button']) ?>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>
