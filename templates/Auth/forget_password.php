<?php
/**
 * @var \App\View\AppView $this
 */

$this->layout = 'login';
$this->assign('title', 'Forget Password');
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="forget-password-form">
                <h2>Forget Password</h2>
                <?= $this->Form->create() ?>
                <?= $this->Flash->render() ?>
                <p>Enter your email address registered with our system below to reset your password:</p>
                <div class="form-group">
                    <?= $this->Form->control('email', ['label' => 'Email', 'type' => 'email', 'class' => 'form-control', 'required' => true, 'autofocus' => true]) ?>
                </div>
                <div class="d-flex justify-content-between mt-3">
                    <?= $this->Form->button('Send verification email', ['class' => 'btn btn-primary']) ?>
                    <?= $this->Html->link('Back to login', ['controller' => 'Auth', 'action' => 'login'], ['class' => 'btn btn-link']) ?>
                </div>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>
