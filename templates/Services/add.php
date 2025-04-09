<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Service $service
 * @var \Cake\Collection\CollectionInterface|string[] $stylists
 */
?>
<div class="row" style="padding: 100px;">
    <aside class="column column-80">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Back To Dashboard'), ['controller' => 'Admins','action' => 'dashboard'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Services'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="enquiry-form">
            <?= $this->Form->create($service) ?>
            <fieldset>
                <legend><?= __('Add Service') ?></legend>
                <div class="form-group mb-3">
                    <?= $this->Form->control('service_name', [
                            'label' => __('Service Name'),
                            'type' => 'text',
                            'class' => 'form-control',
                            'required' => true,
                            'placeholder' => __('Enter Service Name'),
                            'error' => ['Please enter Service Name'],
                        ]); ?>
                </div>
                <div class="form-group mb-3">
                    <?= $this->Form->control('service_cost', [
                        'label' => __('Service Cost'),
                        'type' => 'text',
                        'class' => 'form-control',
                        'required' => true,
                        'placeholder' => __('Enter Service Cost'),
                        'error' => ['Please enter Service Cost'],
                    ]); ?>
                </div>
            </fieldset>
            <?= $this->Form->button(__('Submit'), ['class' => 'btn btn-primary']) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
