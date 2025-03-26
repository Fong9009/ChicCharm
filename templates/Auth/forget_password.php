<?php
/**
 * @var \App\View\AppView $this
 */

$this->layout = 'login';
$this->assign('title', 'Forgot Password');
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="login-form">
                <h2>Forgot Password</h2>
                
                <?= $this->Flash->render() ?>
                
                <?= $this->Form->create(null, ['class' => 'mt-4']) ?>
                
                <p class="text-muted">Enter your email address below and we'll send you instructions to reset your password.</p>
                
                <div class="mb-4">
                    <?= $this->Form->control('email', [
                        'type' => 'email',
                        'label' => 'Email Address',
                        'class' => 'form-control',
                        'required' => true,
                        'autofocus' => true,
                        'placeholder' => 'name@example.com'
                    ]) ?>
                </div>
                
                <?= $this->Form->button('Send Reset Link', ['class' => 'submit-button mb-3']) ?>
                <?= $this->Form->end() ?>
                
                <div class="text-center mt-3">
                    <p><?= $this->Html->link('Back to login', ['controller' => 'Auth', 'action' => 'login'], ['class' => 'btn btn-link']) ?></p>
                </div>
            </div>
        </div>
    </div>
</div>
