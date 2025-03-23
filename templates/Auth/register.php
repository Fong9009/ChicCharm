<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Admin $admin
 */

$this->layout = 'login';
$this->assign('title', 'Register new admin');
?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="register-form">
                <h2>Register New Admin</h2>
                <?= $this->Form->create($admin) ?>
                <?= $this->Flash->render() ?>
                <div class="form-group">
                    <?= $this->Form->control('email', ['label' => 'Email', 'class' => 'form-control', 'required' => true]) ?>
                </div>
                <div class="form-group">
                    <?= $this->Form->control('first_name', ['label' => 'First Name', 'class' => 'form-control', 'required' => true]) ?>
                </div>
                <div class="form-group">
                    <?= $this->Form->control('last_name', ['label' => 'Last Name', 'class' => 'form-control', 'required' => true]) ?>
                </div>
                <div class="form-group">
                    <?= $this->Form->control('password', ['label' => 'Password', 'class' => 'form-control', 'required' => true]) ?>
                </div>
                <div class="form-group">
                    <?= $this->Form->control('password_confirm', ['type' => 'password', 'label' => 'Retype Password', 'class' => 'form-control', 'required' => true]) ?>
                </div>
                <div class="form-group">
                    <?= $this->Form->control('avatar', ['type' => 'file', 'label' => 'Avatar', 'class' => 'form-control']) ?>
                </div>
                <div class="d-flex justify-content-between mt-3">
                    <?= $this->Form->button('Register', ['class' => 'btn btn-primary']) ?>
                    <?= $this->Html->link('Back to login', ['controller' => 'Auth', 'action' => 'login'], ['class' => 'btn btn-link']) ?>
                </div>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>
