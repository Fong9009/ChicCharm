<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Customer $customer
 */
$this->assign('title', 'Edit Profile');
?>
<div class="form-wrapper">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="customers form content">
                <?= $this->Form->create($customer, ['type' => 'file']) ?>
                <fieldset>
                    <legend><?= __('Edit Your Profile') ?></legend>
                    <div class="row">
                        <div class="col-md-6">
                            <?= $this->Form->control('first_name', ['class' => 'form-control']) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $this->Form->control('last_name', ['class' => 'form-control']) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <?= $this->Form->control('email', ['class' => 'form-control']) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <p class="text-muted">Leave password field empty if you don't want to change it.</p>
                                <?= $this->Form->control('password', ['class' => 'form-control', 'value' => '', 'required' => false]) ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <?= $this->Form->control('confirm_password', [
                                    'type' => 'password',
                                    'class' => 'form-control',
                                    'label' => 'Confirm Password',
                                    'required' => false
                                ]) ?>
                            </div>
                        </div>
                    </div>
                    <!-- Profile Picture Preview -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="profile-preview text-center">
                                <?php if (!empty($customer->profile_picture)): ?>
                                    <img src="<?= $this->Url->image('profile/' . $customer->profile_picture) ?>"
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
                                <?= $this->Form->Control('profile_picture', ['type' => 'file','class' => 'form-control','required' => false]) ?>
                            </div>
                        </div>
                    </div>

                    <?php
                        echo $this->Form->control('nonce', ['type' => 'hidden', 'empty' => true]);
                        echo $this->Form->control('nonce_expiry', ['type' => 'hidden', 'empty' => true]);
                    ?>
                </fieldset>

                <div class="text-center mt-4">
                    <?= $this->Form->button(__('Update Profile'), ['class' => 'btn']) ?>
                </div>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>
