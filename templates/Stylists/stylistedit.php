<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Stylist $stylist
 */
$this->assign('title', 'Edit My Profile');
?>
<div class="form-wrapper">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="customers form content">
                <?= $this->Form->create($stylist, ['type' => 'file']) ?>
                <fieldset>
                    <legend><?= __('Edit Your Profile') ?></legend>
                    <div class="row">
                        <div class="col-md-6">
                            <?= $this->Form->control('first_name', [
                                'class' => 'form-control' . ($this->Form->isFieldError('first_name') ? ' is-invalid' : ''),
                                'error' => ['class' => 'invalid-feedback']
                            ]) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $this->Form->control('last_name', [
                                'class' => 'form-control' . ($this->Form->isFieldError('last_name') ? ' is-invalid' : ''),
                                'error' => ['class' => 'invalid-feedback']
                            ]) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <?= $this->Form->control('email', [
                                'class' => 'form-control' . ($this->Form->isFieldError('email') ? ' is-invalid' : ''),
                                'error' => ['class' => 'invalid-feedback']
                            ]) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <?= $this->Form->control('stylist_motto', [
                                'label' => __('Stylist Motto'),
                                'class' => 'form-control',
                                'required' => false,
                                'placeholder' => __('Enter Stylist Motto'),
                                'error' => ['class' => 'invalid-feedback'],
                            ]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <?= $this->Form->control('stylist_bio', [
                                'label' => __('Stylist Bio'),
                                'type' => 'textarea',
                                'id' => 'content-value-input',
                                'class' => 'form-control',
                                'required' => false,
                                'placeholder' => __('Enter Stylist Bio A Short Description'),
                                'maxlength' => 1000,
                                'error' => ['class' => 'invalid-feedback'],
                            ]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div id="message-char-count" class="char-count-display text-muted text-start small mb-2"></div>
                    </div>
                    <!-- Profile Picture Preview -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="profile-preview text-center">
                                <?php if (!empty($stylist->profile_picture)): ?>
                                    <img src="<?= $this->Url->image('profile/' . $stylist->profile_picture) ?>"
                                         alt="Profile Picture">
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <!-- File Upload Location -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="input">
                                <label for="profile_picture">Upload New Profile Picture</label>
                                <?= $this->Form->Control('profile_picture', [
                                    'type' => 'file',
                                    'class' => 'form-control' . ($this->Form->isFieldError('profile_picture') ? ' is-invalid' : ''),
                                    'required' => false,
                                    'error' => ['class' => 'invalid-feedback']
                                ]) ?>
                            </div>
                        </div>
                    </div>

                    <?php
                    echo $this->Form->control('nonce', ['type' => 'hidden', 'empty' => true]);
                    echo $this->Form->control('nonce_expiry', ['type' => 'hidden', 'empty' => true]);
                    ?>
                </fieldset>

                <div class="text-center mt-4">
                    <?= $this->Form->button(__('Update Profile'), ['class' => 'btn btn-primary']) ?>
                    <?= $this->Html->link(__('Cancel'),
                        $userType === 'admin' ? ['action' => 'index'] : ['action' => 'dashboard'],
                        ['class' => 'btn btn-secondary ms-2',
                            'style' => 'background-color: #6c757d; border-color: #6c757d; color: white; transition: all 0.3s;',
                            'onmouseover' => 'this.style.backgroundColor = "#5a6268"; this.style.borderColor = "#545b62";',
                            'onmouseout' => 'this.style.backgroundColor = "#6c757d"; this.style.borderColor = "#6c757d";']) ?>
                </div>
                <?= $this->Form->end() ?>

                <div class="text-center mt-4">
                    <p>Want to change your password? <a href="<?= $this->Url->build(['controller' => 'Auth', 'action' => 'changePassword']) ?>">Click here</a></p>
                </div>
            </div>
        </div>
    </div>
    <?php $this->append('script'); ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const messageTextarea = document.getElementById('content-value-input');
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
