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
        
        <div class="password-requirements mb-3">
            <small class="text-muted">
                <h6>Password Requirements:</h6>
                <ul>
                    <?php if (isset($userType) && $userType === 'admin'): ?>
                        <li>At least 8 characters long</li>
                        <li>Must include uppercase and lowercase letters</li>
                        <li>Must include at least one number</li>
                    <?php else: ?>
                        <li>At least 8 characters long</li>
                    <?php endif; ?>
                </ul>
            </small>
        </div>
        
        <?= $this->Form->button('Reset Password', ['class' => 'submit-button']) ?>
        
        <div class="text-center mt-3">
            <?= $this->Html->link('Back to Login', ['controller' => 'Auth', 'action' => 'login'], ['class' => 'btn btn-link']) ?>
        </div>
        <?= $this->Form->end() ?>
    </div>
</div>
