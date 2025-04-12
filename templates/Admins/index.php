<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Admin> $admins
 */
$this->Html->css('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css', ['block' => true]);
$this->Html->script('https://code.jquery.com/jquery-3.6.0.min.js', ['block' => true]);
$this->Html->css('custom', ['block' => true]);
$this->Html->script('custom', ['block' => true]);
?>
<div class="custom-list-wrapper">
    <div style="padding-top: 60px;">
        <?= $this->Flash->render('admin_notify') ?>
    </div>
    <div class="row">
        <aside class="column">
            <div class="side-nav">
                <h4 class="heading"><?= __('Actions') ?></h4>
                <?= $this->Html->link(__('New Admin'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
                <?= $this->Html->link(__('Back to Dashboard'), ['controller' => 'Admins', 'action' => 'dashboard'], ['class' => 'side-nav-item']) ?>
            </div>
        </aside>
        <div class="contacts index content">
            <div class="table-responsive">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h3><?= __('Admin List') ?></h3>
                        </div>
                    </div>
                </div>
                <div class="search-filter-container">
                    <div class="search-box">
                        <?= $this->Form->create(null, ['type' => 'get', 'class' => 'search-form']) ?>
                        <div class="input-group">
                            <?= $this->Form->control('search', [
                                'label' => false,
                                'class' => 'form-control',
                                'placeholder' => 'Search...',
                                'value' => $this->request->getQuery('search')
                            ]) ?>
                        </div>
                        <?= $this->Form->end() ?>
                    </div>

                    <div class="filter-box">
                        <?= $this->Form->create(null, ['type' => 'get', 'class' => 'filter-form']) ?>
                        <?= $this->Form->select('filter', [
                            '' => 'All Users',
                        ], [
                            'class' => 'form-control',
                            'value' => $this->request->getQuery('filter')
                        ]) ?>
                        <?= $this->Form->end() ?>
                    </div>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th><?= $this->Paginator->sort('first_name') ?></th>
                            <th><?= $this->Paginator->sort('last_name') ?></th>
                            <th><?= $this->Paginator->sort('email') ?></th>
                            <th><?= $this->Paginator->sort('created') ?></th>
                            <th><?= $this->Paginator->sort('modified') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($admins as $admin): ?>
                        <tr>
                            <td><?= h($admin->first_name) ?></td>
                            <td><?= h($admin->last_name) ?></td>
                            <td><?= h($admin->email) ?></td>
                            <td><?= h($admin->created) ?></td>
                            <td><?= h($admin->modified) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['action' => 'view', $admin->id], ['class' => 'button']) ?>
                                <?= $this->Html->link(__('Edit'), ['action' => 'edit', $admin->id], ['class' => 'button']) ?>
                                <?= $this->Form->postLink(
                                    __('Delete'),
                                    ['action' => 'delete', $admin->id],
                                    [
                                        'method' => 'delete',
                                        'confirm' => __('Are you sure you want to delete # {0}?', $admin->id),
                                        'class' => 'button'
                                    ]
                                ) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?= $this->Html->link(__('New Admin'), ['action' => 'add'], ['class' => 'btn btn-primary']) ?>
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
    </div>
</div>
