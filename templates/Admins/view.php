<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Admin $admin
 */

$adminId = $this->request->getAttribute('identity')->id
?>
<?= $this->Html->css('/views/views.css') ?>
<?= $this->Html->css('https://fonts.googleapis.com/icon?family=Material+Icons') ?>
<div class="custom-view-wrapper">
    <div class="row">
        <aside class="column">
            <div class="side-nav">
                <h4 class="heading"><?= __('Actions') ?></h4>
                <!-- Edit Admins -->
                <div class="row px-2">
                    <div class="col-lg-3 mb-4 side-nav-item">
                        <?= $this->Html->link(
                            '<div class="card h-100">
                            <div class="card-header view-card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="view-card-h4">Edit Admin</h4>
                                </div>
                                <i class="material-icons view-icon">edit</i>
                            </div>
                            <div class="card-body view-card-body"></div>
                            <div class="card-footer view-card-footer">
                                <span>Edit Admins</span>
                            </div>
                        </div>',
                            ['controller' => 'Admins', 'action' => 'edit', $admin->id],
                            ['escape' => false, 'class' => 'card-link-wrapper d-block text-decoration-none']
                        ) ?>
                    </div>
                    <!-- Delete Admin -->
                    <div class="col-lg-3 mb-4 side-nav-item" <?= ($admin->id == $adminId) ? 'hidden' : '' ?>>
                        <?= $this->Html->link(
                            '<div class="card h-100">
                            <div class="card-header delete-card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="view-card-h4">Delete Admin</h4>
                                </div>
                                <i class="material-icons view-icon">delete</i>
                            </div>
                            <div class="card-body delete-card-body"></div>
                            <div class="card-footer delete-card-footer">
                                <span>Delete Admin</span>
                            </div>
                        </div>',
                            ['controller' => 'Admins', 'action' => 'delete', $admin->id],
                            ['escape' => false,'class' => 'card-link-wrapper d-block text-decoration-none',
                                'confirm' => __('Are you sure you want to delete # {0}?', $admin->id)],
                        ) ?>
                    </div>
                    <!-- List Admin -->
                    <div class="col-lg-3 mb-4 side-nav-item">
                        <?= $this->Html->link(
                            '<div class="card h-100">
                            <div class="card-header list-card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="view-card-h4">List Admins</h4>
                                </div>
                                <i class="material-icons view-icon">menu</i>
                            </div>
                            <div class="card-body list-card-body"></div>
                            <div class="card-footer list-card-footer">
                                <span>List Admins</span>
                            </div>
                        </div>',
                            ['controller' => 'Admins', 'action' => 'index'],
                            ['escape' => false, 'class' => 'card-link-wrapper d-block text-decoration-none']
                        ) ?>
                    </div>

                    <!-- New Admin -->
                    <div class="col-lg-3 mb-4 side-nav-item">
                        <?= $this->Html->link(
                            '<div class="card h-100">
                            <div class="card-header new-card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="view-card-h4">New Admin</h4>
                                </div>
                                <i class="material-icons view-icon">add</i>
                            </div>
                            <div class="card-body new-card-body"></div>
                            <div class="card-footer new-card-footer">
                                <span>Add Admin</span>
                            </div>
                        </div>',
                            ['controller' => 'Admins', 'action' => 'add'],
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
