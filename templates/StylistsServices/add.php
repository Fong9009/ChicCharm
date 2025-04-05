<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\StylistsService $stylistsService
 * @var \Cake\Collection\CollectionInterface|string[] $stylists
 * @var \Cake\Collection\CollectionInterface|string[] $services
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Stylists Services'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="stylistsServices form content">
            <?= $this->Form->create($stylistsService) ?>
            <fieldset>
                <legend><?= __('Add Stylists Service') ?></legend>
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
