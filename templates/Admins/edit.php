<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Admin $admin
 */
$this->assign('title', 'Edit Admin');
?>
<div class="row">
    <aside class="col-md-3">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $admin->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $admin->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('Admins List'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="col-md-9">
        <div class="customers form content">
            <?= $this->Form->create($admin) ?>
            <fieldset>
                <legend><?= __('Edit Admin') ?></legend>
                <div class="row">
                    <div class="col-md-6">
                        <?= $this->Form->control('first_name', ['label' => 'First Name', 'class' => 'form-control']) ?>
                    </div>
                    <div class="col-md-6">
                        <?= $this->Form->control('last_name', ['label' => 'Last Name', 'class' => 'form-control']) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <?= $this->Form->control('email', ['label' => 'Email', 'class' => 'form-control']) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <p class="text-muted">Leave password field empty if you don't want to change it.</p>
                            <?= $this->Form->control('password', ['label' => 'Password', 'class' => 'form-control', 'value' => '', 'required' => false]) ?>
                        </div>
                    </div>
                </div>
            </fieldset>
            <div class="text-center mt-4">
                <?= $this->Form->button(__('Update Admin'), ['class' => 'btn']) ?>
            </div>
            <?= $this->Form->end() ?>
            
            <div class="text-center mt-4">
                <p>Want to change your password? <a href="<?= $this->Url->build(['controller' => 'Auth', 'action' => 'changePassword']) ?>">Click here</a></p>
            </div>
        </div>
    </div>
</div>
