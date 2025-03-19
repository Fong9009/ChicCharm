<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Contact $contact
 */
?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card form-container">
                <div class="card-header text-white text-center">
                    <h2 class="mb-0">Enquiry Form</h2>
                </div>
                <div class="card-body">
                    <?= $this->Form->create($contact, ['class' => 'form-container']) ?>
                    <fieldset class="form-fieldset">
                        <div class="form-group">
                            <?= $this->Form->control('first_name', ['label' => 'First Name', 'class' => 'form-control', 'required' => true]) ?>
                        </div>
                        <div class="form-group">
                            <?= $this->Form->control('last_name', ['label' => 'Last Name', 'class' => 'form-control', 'required' => true]) ?>
                        </div>
                        <div class="form-group">
                            <?= $this->Form->control('email', ['label' => 'Email', 'class' => 'form-control', 'required' => true]) ?>
                        </div>
                        <div class="form-group">
                            <?= $this->Form->control('phone_number', ['label' => 'Phone Number', 'class' => 'form-control', 'required' => true]) ?>
                        </div>
                        <div class="form-group">
                            <?= $this->Form->control('message', ['label' => 'Message', 'class' => 'form-control', 'required' => true]) ?>
                        </div>
                    </fieldset>
                    <div class="form-actions text-center">
                        <?= $this->Form->button(__('Submit'), ['class' => 'btn btn-primary w-100']) ?>
                    </div>
                    <?= $this->Form->end() ?>
                </div>
            </div>
        </div>
    </div>
</div>
