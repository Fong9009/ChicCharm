<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\StylistsService $stylistsService
 * @var string[]|\Cake\Collection\CollectionInterface $stylists
 * @var string[]|\Cake\Collection\CollectionInterface $services
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $stylistsService->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $stylistsService->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Stylists Services'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="stylistsServices form content">
            <?= $this->Form->create($stylistsService) ?>
            <fieldset>
                <legend><?= __('Edit Stylists Service') ?></legend>
                <?php
                    echo $this->Form->control('stylist_id', ['options' => $stylists]);
                    echo $this->Form->control('service_id', ['options' => $services]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
