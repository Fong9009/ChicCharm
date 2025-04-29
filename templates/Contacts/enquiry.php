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
            <div class="col-md-6" style="margin-bottom: 16px;">
                <?= $this->Form->control('first_name', [
                    'label' => 'First Name',
                    'class' => 'form-control',
                    'required' => true,
                    'placeholder' => 'Enter your first name',
                    'error' => ['class' => 'invalid-feedback'],
                    'maxlength' => 100
                ]) ?>
            </div>
            <div class="col-md-6" style="margin-bottom: 16px;">
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
            <div class="col-md-6" style="margin-bottom: 16px;">
                <?= $this->Form->control('email', [
                    'label' => 'Email',
                    'class' => 'form-control',
                    'required' => true,
                    'placeholder' => 'Enter your email',
                    'type' => 'email',
                    'error' => ['class' => 'invalid-feedback']
                ]) ?>
            </div>
            <div class="col-md-6" style="margin-bottom: 16px;">
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
        <div class="text-center" style="margin-top: 20px;">
            <?= $this->Recaptcha->display(['class' => 'mb-3 d-flex justify-content-center'])?>
            <?= $this->Form->button(__('Submit Enquiry'), [
                'class' => 'submit-button mt-3'
            ]) ?>
        </div>
        <?= $this->Form->end() ?>
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


