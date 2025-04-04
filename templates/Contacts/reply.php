<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Contact $contact
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Contacts List'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="contacts form content">
            <h3><?= __('Reply to Contact') ?></h3>
            <div class="contact-details">
                <p><strong>From:</strong> <?= h($contact->first_name . ' ' . $contact->last_name) ?> (<?= h($contact->email) ?>)</p>
                <p><strong>Original Message:</strong></p>
                <div class="message-box">
                    <?= nl2br(h($contact->message)) ?>
                </div>
            </div>
            <?= $this->Form->create(null, ['url' => ['action' => 'sendReply', $contact->id]]) ?>
            <fieldset>
                <legend><?= __('Reply Message') ?></legend>
                <?php
                    echo $this->Form->control('subject', [
                        'label' => 'Subject',
                        'value' => 'RE: Thank you for contacting ChicCharm - ' . h($contact->first_name) . ' ' . h($contact->last_name),
                        'required' => true
                    ]);
                    echo $this->Form->control('message', [
                        'type' => 'textarea',
                        'label' => 'Message',
                        'required' => true,
                        'rows' => 10
                    ]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Send Reply'), ['class' => 'button primary']) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div> 