<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $entity
 */

$this->assign('title', 'Change User Password - Users');

?>
<div class="row">
    <div class="column">
        <div class="customers form content">

            <?= $this->Form->create($entity) ?>

            <fieldset>

                <legend>Change Password for <u><?= h($entity->first_name) ?> <?= h($entity->last_name) ?></u></legend>

                <div class="row">
                    <?php
                    echo $this->Form->control('current_password', [
                        'type' => 'password',
                        'label' => 'Current Password',
                        'required' => true,
                        'templateVars' => ['container_class' => 'column']
                    ]);
                    echo $this->Form->control('password', [
                        'label' => 'New Password',
                        'value' => '', 
                        'templateVars' => ['container_class' => 'column']
                    ]);
                    echo $this->Form->control('password_confirm', [
                        'type' => 'password',
                        'value' => '', 
                        'label' => 'Retype New Password',
                        'templateVars' => ['container_class' => 'column']
                    ]);
                    ?>
                </div>

            </fieldset>

            <?= $this->Form->button('Submit', ['class' => 'btn btn-primary']) ?>
            <?= $this->Form->end() ?>

        </div>
    </div>
</div>
