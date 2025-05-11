<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Admin> $admins
 */
$this->Html->css('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css', ['block' => true]);
$this->Html->script('https://code.jquery.com/jquery-3.6.0.min.js', ['block' => true]);
$this->Html->css('custom', ['block' => true]);
$this->Html->script('custom', ['block' => true]);
$adminId = $this->request->getAttribute('identity')->id
?>
<?= $this->Html->css('/utility/indexes/indexes.css') ?>
<?= $this->Html->css('https://fonts.googleapis.com/icon?family=Material+Icons') ?>

<div class="admin-background">
    <div class="contacts index content admin-border">
        <?= $this->Flash->render('admin_notify') ?>
            <div class="column">
                <div class="side-nav">
                    <h4 class="heading"><?= __('Actions') ?></h4>
                    <div class="row gx-2">
                        <div class="col-lg-4 col-md-6 col-sm-12 mb-3 side-nav-item">
                            <?= $this->Html->link(
                                '<div class="card h-100">
                            <div class="card-header dashboard-card-header d-flex justify-content-between align-items-center flex-wrap">
                                <h4 class="view-card-h4 mb-0 flex-grow-1 text-truncate">Dashboard</h4>
                                <i class="material-icons view-icon ms-2">person</i>
                            </div>
                            <div class="card-body dashboard-card-body"></div>
                            <div class="card-footer dashboard-card-footer">
                                <span class="mb-0 text-truncate">Back To Dashboard</span>
                            </div>
                        </div>',
                                ['controller' => 'Admins', 'action' => 'dashboard'],
                                ['escape' => false, 'class' => 'card-link-wrapper d-block text-decoration-none']
                            ) ?>
                        </div>
                        <div class="col-lg-4 col-md-6 col-sm-12 mb-3 side-nav-item">
                            <?= $this->Html->link(
                                '<div class="card h-100">
                            <div class="card-header new-card-header d-flex justify-content-between align-items-center flex-wrap">
                                <h4 class="view-card-h4 mb-0 flex-grow-1 text-truncate">New Admin</h4>
                                <i class="material-icons view-icon ms-2">add</i>
                            </div>
                            <div class="card-body new-card-body"></div>
                            <div class="card-footer new-card-footer">
                                <span  class="mb-0 text-truncate">Add Admin</span>
                            </div>
                        </div>',
                                ['controller' => 'Admins', 'action' => 'add'],
                                ['escape' => false, 'class' => 'card-link-wrapper d-block text-decoration-none']
                            ) ?>
                        </div>
                    </div>
                </div>
            </div>
                <div class="table-responsive mt-3">
                    <div class="container">
                        <div class="row align-items-center">
                            <h3><?= __('Admin List') ?></h3>
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
                    <table class="table">
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
                                    <?php if ($admin->id == $adminId): ?>
                                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $admin->id], ['class' => 'button btn-primary']) ?>
                                        <br><span class="text-muted small">Cannot delete your own account</span>
                                    <?php endif; ?>
                                    <?php if ($admin->id != $adminId): ?>
                                        <?= $this->Form->postLink(
                                            __('Delete'),
                                            ['action' => 'delete', $admin->id],
                                            [
                                                'method' => 'post',
                                                'confirm' => __('Are you sure you want to delete # {0}?', ($admin->first_name . ' '. $admin->last_name)),
                                                'class' => 'button',
                                            ]
                                        ) ?>
                                    <?php endif; ?>
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
</div>
