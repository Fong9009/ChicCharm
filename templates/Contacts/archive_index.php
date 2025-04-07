<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Contact> $contacts
 */

// Dropdown functionalityscript
$this->Html->css('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css', ['block' => true]);
$this->Html->script('https://code.jquery.com/jquery-3.6.0.min.js', ['block' => true]);
$this->Html->css('custom', ['block' => true]);
$this->Html->script('custom', ['block' => true]);

?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Back to Active Messages'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="contacts index content">
        <h3><?= __('Archived Messages') ?></h3>
        <div class="table-responsive">
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
                        'active' => 'Active',
                        'inactive' => 'Inactive'
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
                        <th><?= $this->Paginator->sort('phone_number') ?></th>
                        <th><?= $this->Paginator->sort('message') ?></th>
                        <th><?= $this->Paginator->sort('created') ?></th>
                        <th><?= $this->Paginator->sort('modified') ?></th>
                        <th><?= $this->Paginator->sort('replied') ?></th>
                        <th><?= __('Actions') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($contacts as $contact): ?>
                    <tr>
                        <td><?= h($contact->first_name) ?></td>
                        <td><?= h($contact->last_name) ?></td>
                        <td><?= h($contact->email) ?></td>
                        <td><?= h($contact->phone_number) ?></td>
                        <td><?= h($contact->message) ?></td>
                        <td><?= h($contact->created) ?></td>
                        <td><?= h($contact->modified) ?></td>
                        <td><?= $contact->replied ? __('Yes') : __('No') ?></td>
                        <td class="actions">
                            <div class="dropdown">
                                <button class="dropdown-toggle"><i class="fas fa-chevron-right"></i></button>
                                <div class="dropdown-content">
                                    <?= $this->Html->link(__('View'), ['action' => 'view', $contact->id]) ?>
                                    <?= $this->Form->postLink(__('Restore'), ['action' => 'restore', $contact->id], ['confirm' => __('Are you sure you want to restore this message?')]) ?>
                                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $contact->id], ['confirm' => __('Are you sure you want to delete # {0}?', $contact->id), 'class' => 'text-danger']) ?>
                                </div>
                            </div>
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