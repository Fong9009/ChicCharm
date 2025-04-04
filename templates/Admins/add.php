<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Admin $admin
 */
?> 
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="enquiry-form">
                <h2>Register New Admin</h2>
                <?= $this->Form->create($admin) ?>
                <?= $this->Flash->render() ?>
                <div class="form-group">
                    <?= $this->Form->control('email', ['label' => 'Email', 'class' => 'form-control', 'required' => true, 'placeholder' => 'Enter your email']) ?>
                </div>
                <div class="form-group">
                    <?= $this->Form->control('first_name', ['label' => 'First Name', 'class' => 'form-control', 'required' => true, 'placeholder' => 'Enter your first name']) ?>
                </div>
                <div class="form-group">
                    <?= $this->Form->control('last_name', ['label' => 'Last Name', 'class' => 'form-control', 'required' => true, 'placeholder' => 'Enter your last name']) ?>
                </div>
                <div class="form-group">
                    <?= $this->Form->control('password', ['label' => 'Password', 'class' => 'form-control', 'required' => true, 'placeholder' => 'Enter your password']) ?>
                </div>
                <div class="form-group">
                    <?= $this->Form->control('password_confirm', ['type' => 'password', 'label' => 'Retype Password', 'class' => 'form-control', 'required' => true, 'placeholder' => 'Retype your password']) ?>
                </div>
                <div class="d-flex justify-content-between mt-3">
                    <?= $this->Form->button('Register', ['class' => 'btn btn-primary']) ?>
                </div>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>
