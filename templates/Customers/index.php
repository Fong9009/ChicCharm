<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Customer> $customers
 */
$this->Html->css('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css', ['block' => true]);
$this->Html->script('https://code.jquery.com/jquery-3.6.0.min.js', ['block' => true]);
$this->Html->css('custom', ['block' => true]);
$this->Html->script('custom', ['block' => true]);
?>
<div class="custom-list-wrapper">
    <div class="contacts index content">
        <div class="table-responsive">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-2" style="padding: 10px 20px;">
                        <?= $this->Html->link(__('Back to Dashboard'), ['controller' => 'Admins', 'action' => 'dashboard'], ['class' => 'btn btn-primary', 'style' => 'white-space: nowrap;']) ?>
                    </div>
                    <div class="col-8">
                        <h3><?= __('Customer List') ?></h3>
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
                        '' => 'All Customers',
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
                    <?php foreach ($customers as $customer): ?>
                    <tr>
                        <td><?= h($customer->first_name) ?></td>
                        <td><?= h($customer->last_name) ?></td>
                        <td><?= h($customer->email) ?></td>
                        <td><?= h($customer->created) ?></td>
                        <td><?= h($customer->modified) ?></td>
                        <td class="actions">
                            <?= $this->Html->link(__('View'), ['action' => 'view', $customer->id], ['class' => 'button']) ?>
                            <?= $this->Html->link(__('Edit'), ['action' => 'edit', $customer->id], ['class' => 'button']) ?>
                            <?= $this->Form->postLink(
                                __('Delete'),
                                ['action' => 'delete', $customer->id],
                                [
                                    'method' => 'delete',
                                    'confirm' => __('Are you sure you want to delete # {0}?', $customer->id),
                                    'class' => 'button'
                                ]
                            ) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?= $this->Html->link(__('New Customer'), ['action' => 'add'], ['class' => 'btn btn-primary']) ?>
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
