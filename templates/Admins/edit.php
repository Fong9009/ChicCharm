<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Admin $admin
 */
?>
<div class="row">
    <aside class="column">
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
    <div class="column column-80">
        <div class="contacts form content">
            <?= $this->Form->create($admin) ?>
            <fieldset>
                <legend><?= __('Edit Admin') ?></legend>
                <div class="form-group">
                    <?= $this->Form->control('first_name', ['label' => 'First Name', 'class' => 'form-control']) ?>
                </div>
                <div class="form-group">
                    <?= $this->Form->control('last_name', ['label' => 'Last Name', 'class' => 'form-control']) ?>
                </div>
                <div class="form-group">
                    <?= $this->Form->control('email', ['label' => 'Email', 'class' => 'form-control']) ?>
                </div>
                <div class="form-group">
                    <?= $this->Form->control('password', ['label' => 'Password', 'class' => 'form-control']) ?>
                </div>
            </fieldset>
            <?= $this->Form->button(__('Submit'), ['class' => 'btn btn-primary']) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
