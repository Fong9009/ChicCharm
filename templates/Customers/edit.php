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
                
                <?php
                    echo $this->Form->control('nonce', ['type' => 'hidden']);
                    echo $this->Form->control('nonce_expiry', ['type' => 'hidden', 'empty' => true]);
                ?>
            </fieldset>
            
            <div class="text-center mt-4">
                <?= $this->Form->button(__('Update Profile'), ['class' => 'btn']) ?>
            </div>
            <?= $this->Form->end() ?>
            
            <div class="text-center mt-4">
                <p>Want to change your password? <a href="<?= $this->Url->build(['controller' => 'Auth', 'action' => 'changePassword']) ?>">Click here</a></p>
            </div>
        </div>
    </div>
</div>
