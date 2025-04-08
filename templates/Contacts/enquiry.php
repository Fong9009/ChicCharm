<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Contact $contact
 */
?>
<div class="form-wrapper">
    <div class="contacts form content">
        <h2>Contact Us</h2>
        <p class="text-center mb-4">We'd love to hear from you. Please fill out the form below.</p>
        <?= $this->Form->create($contact) ?>
        <div class="row">
            <div class="col-md-6">
                <?= $this->Form->control('first_name', [
                    'label' => 'First Name',
                    'class' => 'form-control',
                    'required' => true,
                    'placeholder' => 'Enter your first name',
                ]) ?>
            </div>
            <div class="col-md-6">
                <?= $this->Form->control('last_name', [
                    'label' => 'Last Name',
                    'class' => 'form-control',
                    'required' => true,
                    'placeholder' => 'Enter your last name',
                ]) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <?= $this->Form->control('email', [
                    'label' => 'Email',
                    'class' => 'form-control',
                    'required' => true,
                    'placeholder' => 'Enter your email',
                    'type' => 'email'
                ]) ?>
            </div>
            <div class="col-md-6">
                <?= $this->Form->control('phone_number', [
                    'label' => 'Phone Number',
                    'class' => 'form-control',
                    'required' => true,
                    'pattern' => '[0-9]{10}',
                    'title' => 'Please enter a valid 10-digit phone number',
                    'placeholder' => 'Enter your 10-digit phone number',
                ]) ?>
            </div>
        </div>
        <?= $this->Form->control('message', [
            'label' => 'Message',
            'class' => 'form-control',
            'required' => true,
            'rows' => 5,
            'placeholder' => 'Enter your message',
        ]) ?>
        <div class="text-center mt-4">
            <?= $this->Recaptcha->display(['class' => 'mb-3 d-flex justify-content-center'])?>
            <?= $this->Form->button(__('Submit Enquiry'), [
                'class' => 'submit-button mt-3'
            ]) ?>
        </div>
        <?= $this->Form->end() ?>
    </div>
</div>


