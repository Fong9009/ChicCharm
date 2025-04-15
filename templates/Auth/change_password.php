<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Admin|\App\Model\Entity\Customer $entity
 * @var string $userType
 */

$this->assign('title', 'Change Password');

?>
<div class="row">
    <div class="column">
        <div class="admins form content">
            <?= $this->Form->create($entity) ?>
            <fieldset>
                <h2>
                    <?= __('Change Password') ?>
                    <?php if (isset($entity) && $entity->first_name && $entity->last_name): ?>
                        <?= __(' for ') ?><?= h($entity->first_name) ?> <?= h($entity->last_name) ?>
                    <?php endif; ?>
                </h2>
                <div class="row">
                    <?php
                    echo $this->Form->control('current_password', [
                        'type' => 'password',
                        'label' => 'Current Password',
                        'required' => true,
                        'class' => 'form-control' . ($this->Form->isFieldError('current_password') ? ' is-invalid' : ''),
                        'error' => ['class' => 'invalid-feedback d-block text-danger'],
                        'value' => ''
                    ]);
                    if ($this->Form->isFieldError('current_password')) {
                        echo $this->Form->error('current_password', null, ['class' => 'invalid-feedback d-block text-danger']);
                    }

                    echo $this->Form->control('password', [
                        'type' => 'password',
                        'label' => 'New Password',
                        'required' => true,
                        'class' => 'form-control' . ($this->Form->isFieldError('password') ? ' is-invalid' : ''),
                        'error' => ['class' => 'invalid-feedback d-block text-danger'],
                        'value' => ''
                    ]);
                    if ($this->Form->isFieldError('password')) {
                        echo $this->Form->error('password', null, ['class' => 'invalid-feedback d-block text-danger']);
                    }

                    echo $this->Form->control('confirm_password', [
                        'type' => 'password',
                        'label' => 'Retype New Password',
                        'required' => true,
                        'class' => 'form-control' . ($this->Form->isFieldError('confirm_password') ? ' is-invalid' : ''),
                        'error' => ['class' => 'invalid-feedback d-block text-danger'],
                        'value' => ''
                    ]);
                    if ($this->Form->isFieldError('confirm_password')) {
                        echo $this->Form->error('confirm_password', null, ['class' => 'invalid-feedback d-block text-danger']);
                    }
                    ?>
                </div>
                <div class="password-requirements mb-3">
                    <small class="text-muted">
                        <h6>Password Requirements:</h6>
                        <ul>
                            <?php if (isset($userType) && $userType === 'admin'): ?>
                                <li>At least 8 characters long</li>
                                <li>Must include uppercase and lowercase letters</li>
                                <li>Must include at least one number</li>
                                <li>Must include at least one special character</li>
                            <?php else: ?>
                                <li>At least 8 characters long</li>
                            <?php endif; ?>
                        </ul>
                    </small>
                </div>
            </fieldset>
            <div class="text-center">
                <?= $this->Form->button(__('UPDATE PASSWORD'), ['class' => 'btn btn-primary']) ?>
                <br>
                <br>
                <?php if (isset($userType) && $userType === 'admin'): ?>
                    <?= $this->Html->link(__('Cancel'), 
                        ['controller' => 'Admins', 'action' => 'dashboard'], 
                        ['class' => 'btn btn-secondary ms-2', 
                        'style' => 'background-color: #6c757d; border-color: #6c757d; color: white; transition: all 0.3s;',
                        'onmouseover' => 'this.style.backgroundColor = "#5a6268"; this.style.borderColor = "#545b62";',
                        'onmouseout' => 'this.style.backgroundColor = "#6c757d"; this.style.borderColor = "#6c757d";']) ?>
                <?php else: ?>
                    <?= $this->Html->link(__('Cancel'), 
                        ['controller' => 'Customers', 'action' => 'dashboard'], 
                        ['class' => 'btn btn-secondary ms-2', 
                        'style' => 'background-color: #6c757d; border-color: #6c757d; color: white; transition: all 0.3s;',
                        'onmouseover' => 'this.style.backgroundColor = "#5a6268"; this.style.borderColor = "#545b62";',
                        'onmouseout' => 'this.style.backgroundColor = "#6c757d"; this.style.borderColor = "#6c757d";']) ?>
                <?php endif; ?>
            </div>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
