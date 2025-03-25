<?php
/**
 * @var \App\View\AppView $this
 */

use Cake\Core\Configure;

$debug = Configure::read('debug');

$this->layout = 'login';
$this->assign('title', 'Login');
?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="login-form">
                <h2>Login</h2>
                <?= $this->Form->create() ?>
                <?= $this->Flash->render() ?>
                <div class="form-group">
                    <?= $this->Form->control('email', ['label' => 'Email', 'type' => 'email', 'class' => 'form-control', 'required' => true, 'autofocus' => true]) ?>
                </div>
                <div class="form-group">
                    <?= $this->Form->control('password', ['label' => 'Password', 'type' => 'password', 'class' => 'form-control', 'required' => true]) ?>
                </div>
                <?= $this->Form->button('Login', ['class' => 'submit-button']) ?>
                <div class="form-group d-flex justify-content-between mt-3">
                    <div class="align-left">
                        <?= $this->Html->link('Don\'t have an account? Sign Up', ['controller' => 'Customers', 'action' => 'registration'], ['class' => 'btn btn-link']) ?>
                    </div>
                    <div class="align-right">
                        <?= $this->Html->link('Forgot password?', ['action' => 'forgetPassword'], ['class' => 'btn btn-link']) ?>
                    </div>
                </div>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>