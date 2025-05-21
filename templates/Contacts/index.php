<?php
/**
 * @var \App\View\AppView $this
 * @var \Cake\Collection\CollectionInterface|array<\App\Model\Entity\Contact> $contacts
 */

$this->Html->css('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css', ['block' => true]);
$this->Html->css('https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css', ['block' => true]);
$this->Html->css('custom', ['block' => true]);
?>
<div class="admin-background">
<div class="custom-list-wrapper">
    <div class="row">
        <aside class="column">
            <div class="side-nav">
                <h4 class="heading"><?= __('Actions') ?></h4>
                <?= $this->Html->link(__('Archived Messages'), ['action' => 'archiveIndex'], ['class' => 'side-nav-item']) ?>
                <?= $this->Html->link(__('Back to Dashboard'), ['controller' => 'Admins', 'action' => 'dashboard'], ['class' => 'side-nav-item']) ?>
            </div>
        </aside>
        <div class="contacts index content admin-border">
            <div class="table-responsive">
                <div class="container">
                    <div class="row align-items-center">
                        <h3><?= __('Active Messages') ?></h3>
                        <?= $this->Flash->render('custom_location') ?>
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
                            '' => 'All Messages',
                            'replied' => 'Replied',
                            'not_replied' => 'Not Replied'
                        ], [
                            'class' => 'form-control',
                            'value' => $this->request->getQuery('filter'),
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
                            <th><?= $this->Paginator->sort('phone_number') ?></th>
                            <th><?= $this->Paginator->sort('preferred_contact_method') ?></th>
                            <th><?= $this->Paginator->sort('message') ?></th>
                            <th><?= $this->Paginator->sort('created', 'Sent At') ?></th>
                            <th><?= $this->Paginator->sort('replied') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($contacts as $contact): ?>
                        <tr>
                            <td><?= h($contact->first_name) ?></td>
                            <td><?= h($contact->last_name) ?></td>
                            <td><?= h($contact->email) ?></td>
                            <td><?= h($contact->phone_number) ?></td>
                            <?php if($contact->preferred_contact_method != null): ?>
                                <td><?= h($contact->preferred_contact_method) ?></td>
                            <?php else : ?>
                                <td>No Preferred Contact</td>
                            <?php endif; ?>
                            <td><?= h($contact->message) ?></td>
                            <td><?= h($contact->created) ?></td>
                            <td><?= $contact->replied ? __('Yes') : __('No') ?></td>
                            <td class="actions">
                                <div class="dropdown">
                                    <button type="button" class="btn btn-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><?= $this->Html->link(__('View'), ['action' => 'view', $contact->id], ['class' => 'dropdown-item view']) ?></li>
                                        <li><?= $this->Html->link(__('Edit'), ['action' => 'edit', $contact->id], ['class' => 'dropdown-item edit']) ?></li>
                                        <li><?= $this->Html->link(__('Email'), ['action' => 'reply', $contact->id], ['class' => 'dropdown-item reply']) ?></li>
                                        <li><?= $this->Form->postLink(__('Archive'), ['action' => 'archive', $contact->id], ['class' => 'dropdown-item archive']) ?></li>
                                        <li><?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $contact->id], [
                                            'confirm' => __('Are you sure you want to delete this message?'),
                                            'class' => 'dropdown-item delete'
                                        ]) ?></li>
                                    </ul>
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
</div>
</div>
<?php
$this->Html->script('https://code.jquery.com/jquery-3.6.0.min.js', ['block' => true]);
$this->Html->script('https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js', ['block' => true]);
$this->Html->script('custom', ['block' => true]);
?>

