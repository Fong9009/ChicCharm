<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Admin $admin
 */
?>
<div class="custom-view-wrapper">
    <div class="row">
        <aside class="column">
            <div class="side-nav">
                <h4 class="heading"><?= __('Actions') ?></h4>
                <?= $this->Html->link(__('Edit Admin'), ['action' => 'edit', $admin->id], ['class' => 'side-nav-item']) ?>
                <?= $this->Form->postLink(__('Delete Admin'), ['action' => 'delete', $admin->id], ['confirm' => __('Are you sure you want to delete # {0}?', $admin->id), 'class' => 'side-nav-item']) ?>
                <?= $this->Html->link(__('Admins List'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            </div>
        </aside>
        <div class="column">
            <div class="contacts view content">
                <h3><?= h($admin->first_name) ?></h3>
                <table>
                    <tr>
                        <th><?= __('First Name') ?></th>
                        <td><?= h($admin->first_name) ?></td>
                    </tr>
                    <tr>
                        <th><?= __('Last Name') ?></th>
                        <td><?= h($admin->last_name) ?></td>
                    </tr>
                    <tr>
                        <th><?= __('Email') ?></th>
                        <td><?= h($admin->email) ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>