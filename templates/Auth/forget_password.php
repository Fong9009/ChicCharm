<?php
/**
 * @var \App\View\AppView $this
 */

$this->layout = 'login';
$this->assign('title', 'Forgot Password');
?>

<div class="forget-password-wrapper">
    <div class="forget-password-form">
        <h2>Forgot Password</h2>
        <?= $this->Flash->render() ?>
        <?= $this->Form->create(null) ?>
        
        <p class="text-muted">Enter your email address below and we'll send you instructions to reset your password.</p>
        
        <div class="input">
            <?= $this->Form->control('email', [
                'type' => 'email',
                'label' => 'Email Address',
                'class' => 'form-control',
                'required' => true,
                'autofocus' => true,
                'placeholder' => 'Enter your email address'
            ]) ?>
        </div>
        
        <?= $this->Form->button('Send Reset Link', ['class' => 'submit-button']) ?>
        
        <div class="text-center mt-3">
            <?= $this->Html->link('Back to Login', ['controller' => 'Auth', 'action' => 'login'], ['class' => 'btn btn-link']) ?>
        </div>
        <?= $this->Form->end() ?>
    </div>
</div>
