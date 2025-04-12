<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Contact $contact
 */
?>
<?= $this->Html->css('/utility/views/views.css') ?>
<?= $this->Html->css('https://fonts.googleapis.com/icon?family=Material+Icons') ?>
<div class="custom-view-wrapper">
    <div class="row">
        <aside class="column">
            <div class="side-nav">
                <h4 class="heading"><?= __('Actions') ?></h4>
                <!-- Edit Contacts -->
                <div class="row px-2">
                    <div class="col-lg-3 mb-4 side-nav-item">
                        <?= $this->Html->link(
                            '<div class="card h-100">
                            <div class="card-header view-card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="view-card-h4">Edit Contact</h4>
                                </div>
                                <i class="material-icons view-icon">edit</i>
                            </div>
                            <div class="card-body view-card-body"></div>
                            <div class="card-footer view-card-footer">
                                <span>Edit Contacts</span>
                            </div>
                        </div>',
                            ['controller' => 'Contacts', 'action' => 'edit', $contact->id],
                            ['escape' => false, 'class' => 'card-link-wrapper d-block text-decoration-none']
                        ) ?>
                    </div>
                    <!-- Delete Contact -->
                    <div class="col-lg-3 mb-4 side-nav-item">
                        <?= $this->Html->link(
                            '<div class="card h-100">
                            <div class="card-header delete-card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="view-card-h4">Delete Contact</h4>
                                </div>
                                <i class="material-icons view-icon">delete</i>
                            </div>
                            <div class="card-body delete-card-body"></div>
                            <div class="card-footer delete-card-footer">
                                <span>Delete Contact</span>
                            </div>
                        </div>',
                            ['controller' => 'Contacts', 'action' => 'delete', $contact->id],
                            ['escape' => false,'class' => 'card-link-wrapper d-block text-decoration-none',
                                'confirm' => __('Are you sure you want to delete # {0}?', $contact->id)],
                        ) ?>
                    </div>

                    <!-- List Contact -->
                    <div class="col-lg-3 mb-4 side-nav-item">
                        <?= $this->Html->link(
                            '<div class="card h-100">
                            <div class="card-header list-card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="view-card-h4">List Contacts</h4>
                                </div>
                                <i class="material-icons view-icon">menu</i>
                            </div>
                            <div class="card-body list-card-body"></div>
                            <div class="card-footer list-card-footer">
                                <span>List Contacts</span>
                            </div>
                        </div>',
                            ['controller' => 'Contacts', 'action' => 'index'],
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
                <h3><?= h($contact->first_name . ' ' . $contact->last_name) ?></h3>
                <table>
                    <tr>
                        <th><?= __('First Name') ?></th>
                        <td><?= h($contact->first_name) ?></td>
                    </tr>
                    <tr>
                        <th><?= __('Last Name') ?></th>
                        <td><?= h($contact->last_name) ?></td>
                    </tr>
                    <tr>
                        <th><?= __('Email') ?></th>
                        <td><?= h($contact->email) ?></td>
                    </tr>
                    <tr>
                        <th><?= __('Phone Number') ?></th>
                        <td><?= h($contact->phone_number) ?></td>
                    </tr>
                    <tr>
                        <th><?= __('Message') ?></th>
                        <td><?= h($contact->message) ?></td>
                    </tr>
                    <tr>
                        <th><?= __('Replied') ?></th>
                        <td><?= $contact->replied ? __('Yes') : __('No') ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
