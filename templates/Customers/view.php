<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Customer $customer
 */
?>
<?= $this->Html->css('/utility/views/views.css') ?>
<?= $this->Html->css('https://fonts.googleapis.com/icon?family=Material+Icons') ?>
<div class="admin-background">
<div class="admin-view-wrapper admin-border">
    <div class="row">
        <aside class="column">
            <div class="side-nav">
                <h4 class="heading"><?= __('Actions') ?></h4>
                <!-- Edit Customers -->
                <div class="row px-2">
                    <div class="col-lg-3 mb-4 side-nav-item">
                        <?= $this->Html->link(
                            '<div class="card h-100">
                            <div class="card-header view-card-header d-flex justify-content-between align-items-center flex-wrap">
                                <h4 class="view-card-h4 mb-0 flex-grow-1 text-truncate">Edit Customer</h4>
                                <i class="material-icons view-icon ms-2">edit</i>
                            </div>
                            <div class="card-body view-card-body"></div>
                            <div class="card-footer view-card-footer">
                                <span class="mb-0 text-truncate">Edit Customers</span>
                            </div>
                        </div>',
                            ['controller' => 'Customers', 'action' => 'edit', $customer->id],
                            ['escape' => false, 'class' => 'card-link-wrapper d-block text-decoration-none']
                        ) ?>
                    </div>
                    <!-- Delete Customer -->
                    <div class="col-lg-3 mb-4 side-nav-item">
                        <?= $this->Form->postLink(
                            '<div class="card h-100">
                            <div class="card-header delete-card-header d-flex justify-content-between align-items-center flex-wrap">
                                <h4 class="view-card-h4 mb-0 flex-grow-1 text-truncate">Delete Customer</h4>
                                <i class="material-icons view-icon ms-2">delete</i>
                            </div>
                            <div class="card-body delete-card-body"></div>
                            <div class="card-footer delete-card-footer">
                                <span class="mb-0 text-truncate">Delete Customer</span>
                            </div>
                        </div>',
                            ['controller' => 'Customers', 'action' => 'delete', $customer->id],
                            ['escape' => false,'class' => 'card-link-wrapper d-block text-decoration-none',
                                'confirm' => __('Are you sure you want to delete # {0}? You will not be able to get them back', ($customer->first_name . ' ' . $customer->last_name)),],
                        ) ?>
                    </div>

                    <!-- List Customer -->
                    <div class="col-lg-3 mb-4 side-nav-item">
                        <?= $this->Html->link(
                            '<div class="card h-100">
                            <div class="card-header list-card-header d-flex justify-content-between align-items-center flex-wrap">
                                <h4 class="view-card-h4 mb-0 flex-grow-1 text-truncate">List Customers</h4>
                                <i class="material-icons view-icon ms-2">menu</i>
                            </div>
                            <div class="card-body list-card-body"></div>
                            <div class="card-footer list-card-footer">
                                <span class="mb-0 text-truncate">List Customers</span>
                            </div>
                        </div>',
                            ['controller' => 'Customers', 'action' => 'index'],
                            ['escape' => false, 'class' => 'card-link-wrapper d-block text-decoration-none']
                        ) ?>
                    </div>

                    <!-- New Customer -->
                    <div class="col-lg-3 mb-4 side-nav-item">
                        <?= $this->Html->link(
                            '<div class="card h-100">
                            <div class="card-header new-card-header d-flex justify-content-between align-items-center flex-wrap">
                                <h4 class="view-card-h4 mb-0 flex-grow-1 text-truncate">New Customer</h4>
                                <i class="material-icons view-icon ms-2">add</i>
                            </div>
                            <div class="card-body new-card-body"></div>
                            <div class="card-footer new-card-footer">
                                <span class="mb-0 text-truncate">Add Customer</span>
                            </div>
                        </div>',
                            ['controller' => 'Customers', 'action' => 'registration'],
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
                <h3><?= h($customer->first_name) ?> <?= h($customer->last_name) ?></h3>
                <table>
                    <tr>
                        <th><?= __('Profile Picture') ?></th>
                        <!-- Profile Picture Preview -->
                        <td>
                            <div class="row">
                                <div class="col-md-1">
                                    <div class="profile-preview text-center">
                                        <?php if (!empty($customer->profile_picture)): ?>
                                            <img src="<?= $this->Url->image('profile/' . $customer->profile_picture) ?>"
                                                 alt="Profile Picture" style="width: 200px; height: 200px;">
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
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
</div>
