<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Admin $admin
 */

$this->layout = 'login';
$this->assign('title', 'Reset Password');
?>
<div class="reset-password-wrapper">
    <div class="reset-password-form">
        <h2>Reset Your Password</h2>
        <?= $this->Flash->render() ?>
        <?= $this->Form->create($user) ?>
        
        <div class="input">
            <?= $this->Form->control('password', [
                'type' => 'password',
                'label' => 'New Password',
                'class' => 'form-control',
                'required' => true,
                'autofocus' => true,
                'value' => '',
                'placeholder' => 'Enter your new password'
            ]); ?>
        </div>
        
        <div class="input">
            <?= $this->Form->control('password_confirm', [
                'type' => 'password',
                'label' => 'Confirm New Password',
                'class' => 'form-control',
                'required' => true,
                'value' => '',
                'placeholder' => 'Confirm your new password'
            ]); ?>
        </div>
        
        <div class="password-requirements">
            <p>Password requirements:</p>
            <ul>
                <li>At least 8 characters long</li>
                <li>Must include uppercase and lowercase letters</li>
                <li>Must include at least one number</li>
            </ul>
        </div>
        
        <?= $this->Form->button('Reset Password', ['class' => 'submit-button']) ?>
        
        <div class="text-center mt-3">
            <?= $this->Html->link('Back to Login', ['controller' => 'Auth', 'action' => 'login'], ['class' => 'btn btn-link']) ?>
        </div>
        <?= $this->Form->end() ?>
    </div>
</div>

<style>
.password-requirements {
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 4px solid #f4623a;
}

.password-requirements ul {
    padding-left: 18px;
    margin-bottom: 0;
}
</style>
