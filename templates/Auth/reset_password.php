<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Admin $admin
 */

$this->layout = 'login';
$this->assign('title', 'Reset Admin Password');
?>
<div class="container login">
    <div class="row">
        <div class="column column-50 column-offset-25">

            <div class="admins form content">

                <?= $this->Form->create($admin) ?>

                <fieldset>

                    <legend>Reset Your Password</legend>

                    <?= $this->Flash->render() ?>

                    <?php
                    echo $this->Form->control('password', [
                        'type' => 'password',
                        'label' => 'New Password',
                        'required' => true,
                        'autofocus' => true,
                        'value' => ''
                    ]);
                    echo $this->Form->control('password_confirm', [
                        'type' => 'password',
                        'label' => 'Repeat New Password',
                        'required' => true,
                        'value' => ''
                    ]);
                    ?>

                </fieldset>

                <?= $this->Form->button('Reset Password') ?>
                <?= $this->Form->end() ?>

                <hr class="hr-between-buttons">

                <?= $this->Html->link('Back to login', ['controller' => 'Auth', 'action' => 'login'], ['class' => 'button button-outline']) ?>

            </div>
        </div>
    </div>
</div>
