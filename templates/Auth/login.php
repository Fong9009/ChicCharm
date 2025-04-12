<?php
/**
 * @var \App\View\AppView $this
 */

use Cake\Core\Configure;

$debug = Configure::read('debug');

$this->layout = 'login';
$this->assign('title', 'Login');
?>
<div class="login-wrapper">
    <div class="login-form">
        <h2>Login</h2>
        <?= $this->Form->create() ?>
        <?= $this->Flash->render() ?>
        <div class="input">
            <?= $this->Form->control('email', [
                'label' => 'Email',
                'type' => 'email',
                'class' => 'form-control' . ($this->Form->isFieldError('email') ? ' is-invalid' : ''),
                'required' => true,
                'autofocus' => true,
                'placeholder' => 'Enter your email',
                'error' => ['class' => 'invalid-feedback']
            ]) ?>
        </div>
        <div class="input">
            <?= $this->Form->control('password', [
                'label' => 'Password',
                'type' => 'password',
                'class' => 'form-control' . ($this->Form->isFieldError('password') ? ' is-invalid' : ''),
                'required' => true,
                'placeholder' => 'Enter your password',
                'error' => ['class' => 'invalid-feedback']
            ]) ?>
        </div>
        <?= $this->Form->button('Login', ['class' => 'submit-button']) ?>
        <div class="form-group d-flex">
            <?= $this->Html->link('Don\'t have an account? Sign Up', 
                ['controller' => 'Customers', 'action' => 'registration'], 
                ['class' => 'btn btn-link']) ?>
            <?= $this->Html->link('Forgot password?', 
                ['action' => 'forgetPassword'], 
                ['class' => 'btn btn-link']) ?>
        </div>
        <?= $this->Form->end() ?>
    </div>
</div>