<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Contact $contact
 */
?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="enquiry-form">
                <h2>Enquiry Form</h2>
                <?= $this->Form->create($contact) ?>
                <?= $this->Form->control('first_name', [
                    'label' => 'First Name',
                    'class' => 'form-control',
                    'required' => true,
                    'placeholder' => 'Enter your first name',
                ]) ?>
                <?= $this->Form->control('last_name', [
                    'label' => 'Last Name',
                    'class' => 'form-control',
                    'required' => true,
                    'placeholder' => 'Enter your last name',
                ]) ?>
                <?= $this->Form->control('email', [
                    'label' => 'Email',
                    'class' => 'form-control',
                    'required' => true,
                    'placeholder' => 'Enter your email',
                ]) ?>
                <?= $this->Form->control('phone_number', [
                    'label' => 'Phone Number',
                    'class' => 'form-control',
                    'required' => true,
                    'pattern' => '[0-9]{10}',
                    'title' => 'Please enter a valid 10-digit phone number',
                    'placeholder' => 'Enter your 10-digit phone number',
                ]) ?>
                <?= $this->Form->control('message', [
                    'label' => 'Message',
                    'class' => 'form-control',
                    'required' => true,
                    'rows' => 5,
                    'placeholder' => 'Enter your message',
                ]) ?>
                <?= $this->Recaptcha->display(['class' => 'mb-3'])?>
                <?= $this->Form->button(__('Submit'), ['class' => 'submit-button']) ?>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>


