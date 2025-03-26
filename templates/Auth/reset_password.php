<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Admin $admin
 */

$this->layout = 'login';
$this->assign('title', 'Reset Password');
?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="login-form">
                <h2>Reset Your Password</h2>
                
                <?= $this->Flash->render() ?>
                
                <?= $this->Form->create($admin, ['class' => 'mt-4']) ?>
                
                <div class="mb-4">
                    <?= $this->Form->control('password', [
                        'type' => 'password',
                        'label' => 'New Password',
                        'class' => 'form-control',
                        'required' => true,
                        'autofocus' => true,
                        'value' => ''
                    ]); ?>
                </div>
                
                <div class="mb-4">
                    <?= $this->Form->control('password_confirm', [
                        'type' => 'password',
                        'label' => 'Confirm New Password',
                        'class' => 'form-control',
                        'required' => true,
                        'value' => ''
                    ]); ?>
                </div>
                
                <div class="password-requirements mb-4">
                    <p class="text-muted small">Password requirements:</p>
                    <ul class="text-muted small">
                        <li>At least 8 characters long</li>
                        <li>Must include uppercase and lowercase letters</li>
                        <li>Must include at least one number</li>
                    </ul>
                </div>
                
                <?= $this->Form->button('Reset Password', ['class' => 'submit-button mb-3']) ?>
                <?= $this->Form->end() ?>
                
                <div class="text-center mt-3">
                    <p><?= $this->Html->link('Back to login', ['controller' => 'Auth', 'action' => 'login'], ['class' => 'btn btn-link']) ?></p>
                </div>
            </div>
        </div>
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
