<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Stylist> $stylists
 */
?>
<div class="stylists index content">
    <?= $this->Html->link(__('New Stylist'), ['action' => 'add'], ['class' => 'button float-right']) ?>
    <h3><?= __('Stylists') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('first_name') ?></th>
                    <th><?= $this->Paginator->sort('last_name') ?></th>
                    <th><?= $this->Paginator->sort('email') ?></th>
                    <th><?= $this->Paginator->sort('nonce') ?></th>
                    <th><?= $this->Paginator->sort('nonce_expiry') ?></th>
                    <th><?= $this->Paginator->sort('created') ?></th>
                    <th><?= $this->Paginator->sort('modified') ?></th>
                    <th><?= $this->Paginator->sort('type') ?></th>
                    <th><?= $this->Paginator->sort('profile_picture') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($stylists as $stylist): ?>
                <tr>
                    <td><?= $this->Number->format($stylist->id) ?></td>
                    <td><?= h($stylist->first_name) ?></td>
                    <td><?= h($stylist->last_name) ?></td>
                    <td><?= h($stylist->email) ?></td>
                    <td><?= h($stylist->nonce) ?></td>
                    <td><?= h($stylist->nonce_expiry) ?></td>
                    <td><?= h($stylist->created) ?></td>
                    <td><?= h($stylist->modified) ?></td>
                    <td><?= h($stylist->type) ?></td>
                    <td><?= h($stylist->profile_picture) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $stylist->id]) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $stylist->id]) ?>
                        <?= $this->Form->postLink(
                            __('Delete'),
                            ['action' => 'delete', $stylist->id],
                            [
                                'method' => 'delete',
                                'confirm' => __('Are you sure you want to delete # {0}?', $stylist->id),
                            ]
                        ) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('first')) ?>
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
            <?= $this->Paginator->last(__('last') . ' >>') ?>
        </ul>
        <p><?= $this->Paginator->counter(__('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')) ?></p>
    </div>
</div>