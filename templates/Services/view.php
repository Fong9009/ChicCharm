<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Service $service
 */
?>
<?= $this->Html->css('/utility/views/views.css') ?>
<?= $this->Html->css('https://fonts.googleapis.com/icon?family=Material+Icons') ?>
<div class="contacts index content">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <!-- Edit Services -->
            <div class="row gx-2">
                <div class="col-lg-3 col-md-6 col-sm-12 mb-3 side-nav-item">
                    <?= $this->Html->link(
                        '<div class="card h-100">
                            <div class="card-header view-card-header d-flex justify-content-between align-items-center flex-wrap">
                                <h4 class="view-card-h4 mb-0 flex-grow-1 text-truncate">Edit Service</h4>
                                <i class="material-icons view-icon ms-2">edit</i>
                            </div>
                            <div class="card-body view-card-body"></div>
                            <div class="card-footer view-card-footer">
                                <span class="mb-0 text-truncate">Edit Services</span>
                            </div>
                        </div>',
                        ['controller' => 'Services', 'action' => 'edit', $service->id],
                        ['escape' => false, 'class' => 'card-link-wrapper d-block text-decoration-none']
                    ) ?>
                </div>
                <!-- Delete Service -->
                <div class="col-lg-3 col-md-6 col-sm-12 mb-3 side-nav-item">
                    <?= $this->Html->link(
                        '<div class="card h-100">
                            <div class="card-header delete-card-header d-flex justify-content-between align-items-center flex-wrap">
                                 <h4 class="view-card-h4 mb-0 flex-grow-1 text-truncate">Delete Service</h4>
                                 <i class="material-icons view-icon ms-2">delete</i>
                            </div>
                            <div class="card-body delete-card-body"></div>
                            <div class="card-footer delete-card-footer">
                                <span class="mb-0 text-truncate">Delete Service</span>
                            </div>
                        </div>',
                        ['controller' => 'Services', 'action' => 'delete', $service->id],
                        ['escape' => false,'class' => 'card-link-wrapper d-block text-decoration-none',
                            'confirm' => __('Are you sure you want to delete # {0}?', $service->id)],
                    ) ?>
                </div>

                <!-- List Service -->
                <div class="col-lg-3 col-md-6 col-sm-12 mb-3 side-nav-item">
                    <?= $this->Html->link(
                        '<div class="card h-100">
                            <div class="card-header list-card-header d-flex justify-content-between align-items-center flex-wrap">
                                <h4 class="view-card-h4 mb-0 flex-grow-1 text-truncate">List Services</h4>
                                <i class="material-icons view-icon ms-2">menu</i>
                            </div>
                            <div class="card-body list-card-body"></div>
                            <div class="card-footer list-card-footer">
                                <span class="mb-0 text-truncate">List Services</span>
                            </div>
                        </div>',
                        ['controller' => 'Services', 'action' => 'index'],
                        ['escape' => false, 'class' => 'card-link-wrapper d-block text-decoration-none']
                    ) ?>
                </div>

                <!-- New Service -->
                <div class="col-lg-3 col-md-6 col-sm-12 mb-3 side-nav-item">
                    <?= $this->Html->link(
                        '<div class="card h-100">
                            <div class="card-header new-card-header d-flex justify-content-between align-items-center flex-wrap">
                                 <h4 class="view-card-h4 mb-0 flex-grow-1 text-truncate">Create New Service</h4>
                                 <i class="material-icons view-icon ms-2">add</i>
                            </div>
                            <div class="card-body new-card-body"></div>
                            <div class="card-footer new-card-footer">
                                <span class="mb-0 text-truncate">Add Service</span>
                            </div>
                        </div>',
                        ['controller' => 'Services', 'action' => 'add'],
                        ['escape' => false, 'class' => 'card-link-wrapper d-block text-decoration-none']
                    ) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-2" style="padding: 10px 20px;">
                    <?= $this->Html->link(__('Back to Dashboard'), ['controller' => 'Admins', 'action' => 'dashboard'], ['class' => 'btn btn-primary text-white', 'style' => 'white-space: nowrap;']) ?>
                </div>
            </div>
        </div>
    </aside>
    <div class="table-responsive mt-3">
        <div class="row">
            <div class="container">
                <div class="row align-items-center">
                    <h3><?= h($service->service_name) ?></h3>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="table-responsive">
                <table class="table w-auto">
                    <tbody>
                        <tr>
                            <th><?= __('Service Name') ?></th>
                            <td><?= h($service->service_name) ?></td>
                        </tr>
                        <tr>
                            <th><?= __('Service Cost') ?></th>
                            <td><?= $this->Number->currency($service->service_cost) ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="container">
                <div class="row align-items-center">
                    <h4 class="px-4"><?= __('Related Stylists') ?></h4>
                </div>
            </div>
        </div>
        <div class="row">
            <?php if (!empty($service->stylists)) : ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th><?= __('First Name') ?></th>
                                <th><?= __('Last Name') ?></th>
                                <th><?= __('Email') ?></th>
                                <th><?= __('Created') ?></th>
                                <th><?= __('Modified') ?></th>
                                <th><?= __('Type') ?></th>
                                <th class="actions"><?= __('Actions') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($service->stylists as $stylist) : ?>
                            <tr>
                                <td><?= h($stylist->first_name) ?></td>
                                <td><?= h($stylist->last_name) ?></td>
                                <td><?= h($stylist->email) ?></td>
                                <td><?= h($stylist->created) ?></td>
                                <td><?= h($stylist->modified) ?></td>
                                <td><?= h($stylist->type) ?></td>
                                <td class="actions">
                                    <?= $this->Html->link(__('View'), ['controller' => 'Stylists', 'action' => 'view', $stylist->id], ['class' => 'button']) ?>
                                    <?= $this->Html->link(__('Edit'), ['controller' => 'Stylists', 'action' => 'edit', $stylist->id], ['class' => 'button']) ?>
                                    <?= $this->Form->postLink(
                                        __('Delete'),
                                        ['controller' => 'Stylists', 'action' => 'delete', $stylist->id],
                                        [
                                            'method' => 'delete',
                                            'confirm' => __('Are you sure you want to delete # {0}?', $stylist->id),
                                            'class' => 'button',
                                        ]
                                    ) ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
