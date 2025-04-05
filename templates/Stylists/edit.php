<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Stylist $stylist
 * @var string[]|\Cake\Collection\CollectionInterface $bookings
 * @var string[]|\Cake\Collection\CollectionInterface $services
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $stylist->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $stylist->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Stylists'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="stylists form content">
            <?= $this->Form->create($stylist) ?>
            <fieldset>
                <legend><?= __('Edit Stylist') ?></legend>
                <?php
                    echo $this->Form->control('first_name');
                    echo $this->Form->control('last_name');
                    echo $this->Form->control('email');
                    echo $this->Form->control('password');
                    echo $this->Form->control('nonce', ['empty' => true]);
                    echo $this->Form->control('nonce_expiry', ['empty' => true]);
                    echo $this->Form->control('type');
                    echo $this->Form->control('profile_picture');
                    echo $this->Form->control('bookings._ids', ['options' => $bookings]);
                    echo $this->Form->control('services._ids', ['options' => $services]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
