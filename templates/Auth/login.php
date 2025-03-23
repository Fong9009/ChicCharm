<?php
/**
 * @var \App\View\AppView $this
 */

use Cake\Core\Configure;

$debug = Configure::read('debug');

$this->layout = 'login';
$this->assign('title', 'Admin Login');
?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="login-form">
                <h2>Admin Login</h2>
                <?= $this->Form->create() ?>
                <?= $this->Flash->render() ?>
                <div class="form-group">
                    <?= $this->Form->control('email', ['label' => 'Email', 'type' => 'email', 'class' => 'form-control', 'required' => true, 'autofocus' => true]) ?>
                </div>
                <div class="form-group">
                    <?= $this->Form->control('password', ['label' => 'Password', 'type' => 'password', 'class' => 'form-control', 'required' => true]) ?>
                </div>
                <div class="d-flex justify-content-between mt-3">
                    <?= $this->Form->button('Login', ['class' => 'btn btn-primary']) ?>
                    <?= $this->Html->link('Forgot password?', ['action' => 'forgetPassword'], ['class' => 'btn btn-link']) ?>
                </div>
                <?= $this->Form->end() ?>
                <hr class="hr-between-buttons">
                <div class="d-flex justify-content-between">
                    <?= $this->Html->link('Register new admin', ['controller' => 'Auth', 'action' => 'register'], ['class' => 'btn btn-secondary']) ?>
                    <?= $this->Html->link('Go to Homepage', '/', ['class' => 'btn btn-secondary']) ?>
                </div>
            </div>
        </div>
    </div>
</div>
