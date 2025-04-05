<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\StylistsService $stylistsService
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Stylists Service'), ['action' => 'edit', $stylistsService->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Stylists Service'), ['action' => 'delete', $stylistsService->id], ['confirm' => __('Are you sure you want to delete # {0}?', $stylistsService->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Stylists Services'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Stylists Service'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="stylistsServices view content">
            <h3><?= h($stylistsService->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Stylist') ?></th>
                    <td><?= $stylistsService->hasValue('stylist') ? $this->Html->link($stylistsService->stylist->first_name, ['controller' => 'Stylists', 'action' => 'view', $stylistsService->stylist->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Service') ?></th>
                    <td><?= $stylistsService->hasValue('service') ? $this->Html->link($stylistsService->service->service_name, ['controller' => 'Services', 'action' => 'view', $stylistsService->service->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($stylistsService->id) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>