<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Customer $customer
 */
?>
<div class="custom-view-wrapper">
    <div class="row">
        <aside class="column">
            <div class="side-nav">
                <h4 class="heading"><?= __('Actions') ?></h4>
                <?= $this->Html->link(__('Edit Customer'), ['action' => 'edit', $customer->id], ['class' => 'side-nav-item']) ?>
                <?= $this->Form->postLink(__('Delete Customer'), ['action' => 'delete', $customer->id], ['confirm' => __('Are you sure you want to delete # {0}?', $customer->id), 'class' => 'side-nav-item']) ?>
                <?= $this->Html->link(__('List Customers'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
                <?= $this->Html->link(__('New Customer'), ['action' => 'registration'], ['class' => 'side-nav-item']) ?>
            </div>
        </aside>
        <div class="column">
            <div class="contacts view content">
                <h3><?= h($customer->first_name) ?></h3>
                <table>
                    <tr>
                        <th><?= __('First Name') ?></th>
                        <td><?= h($customer->first_name) ?></td>
                    </tr>
                    <tr>
                        <th><?= __('Last Name') ?></th>
                        <td><?= h($customer->last_name) ?></td>
                    </tr>
                    <tr>
                        <th><?= __('Email') ?></th>
                        <td><?= h($customer->email) ?></td>
                    </tr>
                    <tr>
                        <th><?= __('Nonce') ?></th>
                        <td><?= h($customer->nonce) ?></td>
                    </tr>
                    <tr>
                        <th><?= __('Nonce Expiry') ?></th>
                        <td><?= h($customer->nonce_expiry) ?></td>
                    </tr>
                    <tr>
                        <th><?= __('Created') ?></th>
                        <td><?= h($customer->created) ?></td>
                    </tr>
                    <tr>
                        <th><?= __('Modified') ?></th>
                        <td><?= h($customer->modified) ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>