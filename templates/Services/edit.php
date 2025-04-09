<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Service $service
 * @var string[]|\Cake\Collection\CollectionInterface $stylists
 */
?>
<div class="row" style="padding: 100px;">
    <aside class="column column-80" >
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Back to Dashboard'), ['controller'=>'Admins', 'action' => 'dashboard'], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $service->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $service->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Services'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="services form content">
            <?= $this->Form->create($service) ?>
            <fieldset>
                <legend><?= __('Edit Service') ?></legend>
                <?php
                    echo $this->Form->control('service_name');
                    echo $this->Form->control('service_cost');
                    echo $this->Form->control('stylists._ids', ['options' => $stylists]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
