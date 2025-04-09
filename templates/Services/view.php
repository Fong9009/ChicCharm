<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Service $service
 */
?>
<div class="row" style="padding: 100px;">
    <aside class="column column-80">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Back to Dashboard'), ['controller'=>'Admins', 'action' => 'dashboard'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('Edit Service'), ['action' => 'edit', $service->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Service'), ['action' => 'delete', $service->id], ['confirm' => __('Are you sure you want to delete # {0}?', $service->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Services'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Service'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="related contacts index content">
            <div class="services view content">
                <div>
                <h3><?= h($service->service_name) ?></h3>
                <table>
                    <tr>
                        <th><?= __('Service Name') ?></th>
                        <td><?= h($service->service_name) ?></td>
                    </tr>
                    <tr>
                        <th><?= __('Id') ?></th>
                        <td><?= $this->Number->format($service->id) ?></td>
                    </tr>
                    <tr>
                        <th><?= __('Service Cost') ?></th>
                        <td><?= $this->Number->format($service->service_cost) ?></td>
                    </tr>
                </table>
                    <h4 class="px-4"><?= __('Related Stylists') ?></h4>
                    <?php if (!empty($service->stylists)) : ?>
                    <div class="table-responsive">
                        <table>
                            <tr>
                                <th><?= __('First Name') ?></th>
                                <th><?= __('Last Name') ?></th>
                                <th><?= __('Email') ?></th>
                                <th><?= __('Created') ?></th>
                                <th><?= __('Modified') ?></th>
                                <th><?= __('Type') ?></th>
                                <th class="actions"><?= __('Actions') ?></th>
                            </tr>
                            <?php foreach ($service->stylists as $stylist) : ?>
                            <tr>
                                <td><?= h($stylist->first_name) ?></td>
                                <td><?= h($stylist->last_name) ?></td>
                                <td><?= h($stylist->email) ?></td>
                                <td><?= h($stylist->created) ?></td>
                                <td><?= h($stylist->modified) ?></td>
                                <td><?= h($stylist->type) ?></td>
                                <td class="actions">
                                    <?= $this->Html->link(__('View'), ['controller' => 'Stylists', 'action' => 'view', $stylist->id]) ?>
                                    <?= $this->Html->link(__('Edit'), ['controller' => 'Stylists', 'action' => 'edit', $stylist->id]) ?>
                                    <?= $this->Form->postLink(
                                        __('Delete'),
                                        ['controller' => 'Stylists', 'action' => 'delete', $stylist->id],
                                        [
                                            'method' => 'delete',
                                            'confirm' => __('Are you sure you want to delete # {0}?', $stylist->id),
                                        ]
                                    ) ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
