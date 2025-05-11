<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Service> $services
 */
?>
<?= $this->Html->css('/utility/indexes/indexes.css') ?>
<?= $this->Html->css('https://fonts.googleapis.com/icon?family=Material+Icons') ?>
<div class="admin-background">
    <div class="contacts index content admin-border">
        <!-- Action Menu -->
        <aside class="column">
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
                                    <h4 class="view-card-h4 mb-0 flex-grow-1 text-truncate">New Service</h4>
                                    <i class="material-icons view-icon ms-2">add</i>
                                </div>
                                <div class="card-body new-card-body"></div>
                                <div class="card-footer new-card-footer">
                                    <span  class="mb-0 text-truncate">Add Service</span>
                                </div>
                            </div>',
                            ['controller' => 'Services', 'action' => 'add'],
                            ['escape' => false, 'class' => 'card-link-wrapper d-block text-decoration-none']
                        ) ?>
                    </div>
                </div>
            </div>
        </aside>

        <!--Services Table-->
        <div class="table-responsive mt-3">
            <div class="container">
                <div class="row align-items-center">
                    <h3><?= __('Services') ?></h3>
                </div>
            </div>
            <table class="table">
                <thead>
                <tr>
                    <th><?= $this->Paginator->sort('service_name') ?></th>
                    <th><?= $this->Paginator->sort('service_cost') ?></th>
                    <th><?= $this->Paginator->sort('duration_minutes', 'Duration') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($services as $service): ?>
                    <tr>
                        <td><?= h($service->service_name) ?></td>
                        <td><?= $this->Number->currency($service->service_cost) ?></td>
                        <td><?= h($service->duration_minutes) ?> mins</td>
                        <td class="actions">
                            <?= $this->Html->link(__('View'), ['action' => 'view', $service->id], ['class' => 'button']) ?>
                            <?= $this->Html->link(__('Edit'), ['action' => 'edit', $service->id], ['class' => 'button']) ?>
                            <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $service->id], [
                                'method' => 'delete',
                                'confirm' => __('Are you sure you want to delete # {0}? You will not be able to get it back', $service->service_name),
                                'class' => 'button',
                            ]) ?>
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
