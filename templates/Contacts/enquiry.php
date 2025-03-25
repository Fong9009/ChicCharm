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
                    'required' => true
                ]) ?>
                <?= $this->Form->control('last_name', [
                    'label' => 'Last Name',
                    'class' => 'form-control',
                    'required' => true
                ]) ?>
                <?= $this->Form->control('email', [
                    'label' => 'Email',
                    'class' => 'form-control',
                    'required' => true
                ]) ?>
                <?= $this->Form->control('phone_number', [
                    'label' => 'Phone Number',
                    'class' => 'form-control',
                    'required' => true
                ]) ?>
                <?= $this->Form->control('message', [
                    'label' => 'Message',
                    'class' => 'form-control',
                    'required' => true,
                    'rows' => 5
                ]) ?>
                <?= $this->Recaptcha->display(['class' => 'mb-3'])?>
                <?= $this->Form->button(__('Submit'), ['class' => 'submit-button']) ?>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>


