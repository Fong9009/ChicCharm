<?php
/**
 * @var \App\View\AppView $this
 */

use Cake\Core\Configure;

$debug = Configure::read('debug');

$this->layout = 'login';
$this->assign('title', 'Login');
?>
<?= $this->Html->css('/login/login.css') ?>
<div class="login-background d-flex align-items-stretch">
    <div class="row flex-fill w-100 m-0">
        <div class="col-md-6 d-none d-md-block p-0 h-100">
            <?= $this->Html->image('haircut.jpg', [
                'alt' => 'Login Image',
                'class' => 'img-fluid login-image',
            ]) ?>
        </div>

        <div class="col-12 col-md-6 d-flex p-0">
            <div class="login-wrapper-background w-100 d-flex flex-column justify-content-center">
                <div class="login-form d-flex flex-column justify-content-center">
                    <h2>Login</h2>
                    <h4 class="text-center">ChicCharm, For Styling Needs</h4>
                    <?= $this->Form->create() ?>
                    <?= $this->Flash->render() ?>
                    <div class="input">
                        <?= $this->Form->control('email', [
                            'label' => ['text' => 'Email', 'class' => 'text-left-align'],
                            'type' => 'email',
                            'class' => 'form-control' . ($this->Form->isFieldError('email') ? ' is-invalid' : ''),
                            'required' => true,
                            'autofocus' => true,
                            'placeholder' => 'Enter your email',
                            'error' => ['class' => 'invalid-feedback'],
                        ]) ?>
                    </div>
                    <div class="input">
                        <?= $this->Form->control('password', [
                            'label' => ['text' => 'Password', 'class' => 'text-left-align'],
                            'type' => 'password',
                            'class' => 'form-control' . ($this->Form->isFieldError('password') ? ' is-invalid' : ''),
                            'required' => true,
                            'placeholder' => 'Enter your password',
                            'error' => ['class' => 'invalid-feedback'],
                        ]) ?>
                    </div>
                    <?= $this->Form->button('Login', ['class' => 'submit-button btn btn-primary w-100 mt-3']) ?>
                    <div class="form-group d-flex flex-column mt-3">
                        <?= $this->Html->link('Don\'t have an account? Sign Up',
                            ['controller' => 'Customers', 'action' => 'registration'],
                            ['class' => 'btn btn-link p-0']) ?>
                        <?= $this->Html->link('Forgot password?',
                            ['action' => 'forgetPassword'],
                            ['class' => 'btn btn-link p-0']) ?>
                    </div>
                    <?= $this->Form->end() ?>
                </div>
            </div>
        </div>
    </div>
</div>
