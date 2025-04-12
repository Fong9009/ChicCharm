<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Admin $admin
 */
$this->assign('title', 'Edit Admin');
?>
<div class="custom-edit-wrapper">
    <div class="row">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $admin->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $admin->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('Admins List'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
        <div class="column-edit">
            <div class="customers form content">
                <?= $this->Form->create($admin, ['type' => 'file']) ?>
                <fieldset>
                    <legend><?= __('Edit Admin') ?></legend>
                    <div class="row">
                        <div class="col-md-6">
                            <?= $this->Form->control('first_name', [
                                'label' => 'First Name', 
                                'class' => 'form-control' . ($this->Form->isFieldError('first_name') ? ' is-invalid' : ''),
                                'error' => ['class' => 'invalid-feedback']
                            ]) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $this->Form->control('last_name', [
                                'label' => 'Last Name', 
                                'class' => 'form-control' . ($this->Form->isFieldError('last_name') ? ' is-invalid' : ''),
                                'error' => ['class' => 'invalid-feedback']
                            ]) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <?= $this->Form->control('email', [
                                'label' => 'Email', 
                                'class' => 'form-control' . ($this->Form->isFieldError('email') ? ' is-invalid' : ''),
                                'error' => ['class' => 'invalid-feedback']
                            ]) ?>
                        </div>
                    </div>
                    <!-- Profile Picture Preview -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="profile-preview text-center">
                                <?php if (!empty($admin->profile_picture)): ?>
                                    <img src="<?= $this->Url->image('profile/' . $admin->profile_picture) ?>"
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
                </fieldset>
                <div class="text-center mt-4">
                    <?= $this->Form->button(__('Update Admin'), ['class' => 'btn btn-primary']) ?>
                    <?= $this->Html->link(__('Cancel'), 
                        ['action' => 'index'], 
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
</div>
