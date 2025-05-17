<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Contact $contact
 */
?>
<?= $this->Html->css('/dashboard/profiledash.css') ?>
<?= $this->Html->script('custom') ?>
<?= $this->Html->css('https://fonts.googleapis.com/icon?family=Material+Icons') ?>
<div class="customer-dashboard" style="background-image: url(<?= $this->Url->image('gradient.jpg')?>); padding-top: 68px; padding-bottom: 65px;" >
    <div class="contacts form content mx-auto" style="max-width: 900px; background-color: rgba(255,255,255,0.9); border-radius: 8px; padding: 40px; box-shadow: 0 0 15px rgba(0,0,0,0.1);">
        <h2 class="text-center">Contact Us</h2>
        <p class="text-center mb-4">We'd love to hear from you. Please fill out the form below or reach out to us using the details provided.</p>
        <div class="row">
            <div class="col-md-5">
                <h4>Our Details</h4>
                <hr class="my-3">
                <p>
                    <strong><i class="material-icons align-middle me-2">location_on</i>Address:</strong><br>
                    123 Beauty Lane, Styleville<br>
                </p>
                <p>
                    <strong><i class="material-icons align-middle me-2">phone</i>Phone:</strong><br>
                    (03) 9000 0000
                </p>
                <p>
                    <strong><i class="material-icons align-middle me-2">email</i>Email:</strong><br>
                    <a href="mailto:enquiries@example.com">contact@chiccharm.com</a>
                </p>
                <p>
                    <strong><i class="material-icons align-middle me-2">schedule</i>Business Hours:</strong><br>
                    Monday - Sunday: 9:00 AM - 5:00 PM<br>
                </p>
            </div>
            <div class="col-md-1 d-none d-md-flex justify-content-center">
                <div class="vr" style="height: 100%;"></div>
            </div>
            <div class="col-md-6">
                <h4>Send us a Message</h4>
                <hr class="my-3">
                <?= $this->Form->create($contact) ?>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <?= $this->Form->control('first_name', [
                            'label' => 'First Name',
                            'class' => 'form-control',
                            'required' => true,
                            'placeholder' => 'Enter your first name',
                            'error' => ['class' => 'invalid-feedback'],
                            'maxlength' => 100
                        ]) ?>
                    </div>
                    <div class="col-md-6 mb-3">
                        <?= $this->Form->control('last_name', [
                            'label' => 'Last Name',
                            'class' => 'form-control',
                            'required' => true,
                            'placeholder' => 'Enter your last name',
                            'error' => ['class' => 'invalid-feedback'],
                            'maxlength' => 100
                        ]) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <?= $this->Form->control('email', [
                            'label' => 'Email',
                            'class' => 'form-control',
                            'required' => true,
                            'placeholder' => 'Enter your email',
                            'type' => 'email',
                            'error' => ['class' => 'invalid-feedback']
                        ]) ?>
                    </div>
                    <div class="col-md-6 mb-3">
                        <?= $this->Form->control('phone_number', [
                            'label' => 'Phone Number',
                            'class' => 'form-control',
                            'required' => true,
                            'pattern' => '[0-9]{10}',
                            'title' => 'Please enter a valid 10-digit phone number',
                            'placeholder' => 'Enter your 10-digit phone number',
                            'error' => ['class' => 'invalid-feedback']
                        ]) ?>
                    </div>
                </div>
                <?= $this->Form->control('message', [
                    'label' => 'Message',
                    'class' => 'form-control',
                    'required' => true,
                    'rows' => 5,
                    'placeholder' => 'Enter your message',
                    'error' => ['class' => 'invalid-feedback'],
                    'maxlength' => 3000,
                ]) ?>
                <div id="message-char-count" class="char-count-display text-muted text-start small mb-2"></div>
                <div class="text-center mt-3">
                    <?= $this->Recaptcha->display(['class' => 'mb-3 d-flex justify-content-center'])?>
                    <?= $this->Form->button(__('Submit Enquiry'), [
                        'class' => 'submit-button mt-3'
                    ]) ?>
                </div>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>

<?php $this->append('script'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const messageTextarea = document.getElementById('message');
    const charCountDisplay = document.getElementById('message-char-count');
    const maxLength = messageTextarea ? parseInt(messageTextarea.getAttribute('maxlength'), 10) : 0;

    function updateCharCount() {
        if (!messageTextarea || !charCountDisplay || !maxLength) return;

        const currentLength = messageTextarea.value.length;
        const remaining = maxLength - currentLength;

        charCountDisplay.textContent = `${currentLength}/${maxLength}`;

        if (remaining < 0) {
            charCountDisplay.style.color = 'red';
        } else if (remaining < 50) {
             charCountDisplay.style.color = 'orange';
        } else {
            charCountDisplay.style.color = '';
        }
    }

    if (messageTextarea) {
        messageTextarea.addEventListener('input', updateCharCount);
        updateCharCount();
    }
});
</script>
<?php $this->end(); ?>
</div>
