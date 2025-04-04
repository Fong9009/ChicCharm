<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Customer $customer
 */
$this->assign('title', 'Edit Profile');
?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="customers form content">
            <?= $this->Form->create($customer) ?>
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
                <!-- Profile Picture Preview -->
                <div class="row">
                    <div class="col-md-12 text-center">
                        <?php if (!empty($customer->profile_picture)): ?>
                            <img src="<?= $this->Url->image('uploads/' . $customer->profile_picture) ?>"
                                 alt="Profile Picture"
                                 style="width: 150px; height: 150px; border-radius: 50%;">
                        <?php endif; ?>
                    </div>
                </div>
                <!-- File Upload Location -->
                <div class="row">
                    <div class="col-md-12">
                        <label for="profile_picture">Upload New Profile Picture</label>
                        <?= $this->Form->file('profile_picture', ['class' => 'form-control']) ?>
                    </div>
                </div>


                <?php
                    echo $this->Form->control('nonce', ['type' => 'hidden']);
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
