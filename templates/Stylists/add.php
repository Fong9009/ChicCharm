<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Stylist $stylist
 * @var \Cake\Collection\CollectionInterface|string[] $bookings
 * @var \Cake\Collection\CollectionInterface|string[] $services
 */
?>
<?= $this->Html->css('/utility/adds/adds.css') ?>
<?= $this->Html->css('https://fonts.googleapis.com/icon?family=Material+Icons') ?>
<div class="admin-background">
    <div class="contacts index content admin-border">
        <!-- Action Menu -->
        <aside class="column">
            <div class="side-nav">
                <h4 class="heading"><?= __('Actions') ?></h4>
                <div class="row gx-2">
                    <div class="col-lg-4 col-md-6 col-sm-12 mb-3 side-nav-item">
                        <?= $this->Html->link(
                            '<div class="card h-100">
                                <div class="card-header dashboard-card-header d-flex justify-content-between align-items-center flex-wrap">
                                    <h4 class="view-card-h4 mb-0 flex-grow-1 text-truncate">Dashboard</h4>
                                    <i class="material-icons view-icon ms-2">person</i>
                                </div>
                                <div class="card-body dashboard-card-body"></div>
                                <div class="card-footer dashboard-card-footer">
                                    <span class="mb-0 text-truncate">Back To Dashboard</span>
                                </div>
                            </div>',
                            ['controller' => 'Admins', 'action' => 'dashboard'],
                            ['escape' => false, 'class' => 'card-link-wrapper d-block text-decoration-none']
                        ) ?>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-12 mb-3 side-nav-item">
                        <?= $this->Html->link(
                            '<div class="card h-100">
                                <div class="card-header list-card-header d-flex justify-content-between align-items-center flex-wrap">
                                    <h4 class="view-card-h4 mb-0 flex-grow-1 text-truncate">List Stylists</h4>
                                    <i class="material-icons view-icon ms-2">list</i>
                                </div>
                                <div class="card-body list-card-body"></div>
                                <div class="card-footer list-card-footer">
                                    <span  class="mb-0 text-truncate">List Stylists</span>
                                </div>
                            </div>',
                            ['controller' => 'Stylists', 'action' => 'index'],
                            ['escape' => false, 'class' => 'card-link-wrapper d-block text-decoration-none']
                        ) ?>
                    </div>
                </div>
            </div>
        </aside>
        <div class="column">
            <div class="stylists form content">
                <?= $this->Form->create($stylist, ['type' => 'file']) ?>
                <fieldset>
                    <legend><?= __('Add Stylist') ?></legend>
                    <div class="form-group mb-3">
                        <?= $this->Form->control('first_name', [
                            'label' => __('First Name*'),
                            'class' => 'form-control',
                            'required' => true,
                            'placeholder' => __('Enter first name'),
                            'error' => ['class' => 'invalid-feedback'],
                        ]); ?>
                    </div>
                    <div class="form-group mb-3">
                        <?= $this->Form->control('last_name', [
                            'label' => __('Last Name*'),
                            'class' => 'form-control',
                            'required' => true,
                            'placeholder' => __('Enter last name'),
                            'error' => ['class' => 'invalid-feedback'],
                        ]); ?>
                    </div>
                    <div class="form-group mb-3">
                        <?= $this->Form->control('email', [
                            'label' => __('Email*'),
                            'class' => 'form-control',
                            'required' => true,
                            'placeholder' => __('Enter email'),
                            'error' => ['class' => 'invalid-feedback'],
                        ]);?>
                    </div>
                    <div class="form-group mb-3">
                        <?= $this->Form->control('password', [
                            'label' => __('Password*'),
                            'class' => 'form-control',
                            'required' => true,
                            'placeholder' => __('Enter password'),
                            'error' => ['class' => 'invalid-feedback'],
                        ]); ?>
                    </div>
                    <div class="form-group mb-3">
                        <?= $this->Form->control('password_confirm', [
                            'type' => 'password',
                            'label' => 'Retype Password*',
                            'class' => 'form-control' . ($this->Form->isFieldError('password_confirm') ? ' is-invalid' : ''),
                            'required' => true,
                            'placeholder' => 'Retype your password',
                            'error' => ['class' => 'invalid-feedback']
                        ]) ?>
                    </div>
                    <div class="form-group mb-3">
                        <?= $this->Form->control('stylist_motto', [
                            'label' => __('Stylist Motto'),
                            'class' => 'form-control',
                            'required' => false,
                            'placeholder' => __('Enter Stylist Motto'),
                            'error' => ['class' => 'invalid-feedback'],
                        ]); ?>
                    </div>
                    <div class="form-group mb-3">
                        <?= $this->Form->control('stylist_bio', [
                            'label' => __('Stylist Bio'),
                            'type' => 'textarea',
                            'id' => 'content-value-input',
                            'class' => 'form-control',
                            'required' => false,
                            'placeholder' => __('Enter Stylist Bio A Short Description'),
                            'maxlength' => 1000,
                            'error' => ['class' => 'invalid-feedback'],
                        ]); ?>
                    </div>
                    <div class="row">
                        <div id="message-char-count" class="char-count-display text-muted text-start small mb-2"></div>
                    </div>
                    <?php
                        echo $this->Form->control('nonce', ['type' => 'hidden', 'empty' => true]);
                        echo $this->Form->control('nonce_expiry', ['type' => 'hidden', 'empty' => true]);
                        ?>
                    <!-- Service Image Preview -->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="profile-preview text-start">
                                <?php if (!empty($stylist->profile_picture)): ?>
                                    <img src="<?= $this->Url->image('profile/' . $stylist->profile_picture) ?>"
                                         alt="Profile Picture"
                                         class="img-fluid"
                                         style="width: 400px; height: 400px; object-fit: cover; border-radius: 8px;">
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <!-- File Upload Location -->
                    <div class="row">
                        <div class="col-12 col-md-12 col-sm-12">
                            <div class="input">
                                <label for="Service Image">Upload for Profile Image</label>
                                <?= $this->Form->Control('profile_picture', [
                                    'type' => 'file',
                                    'class' => 'form-control' . ($this->Form->isFieldError('profile_picture') ? ' is-invalid' : ''),
                                    'required' => false,
                                    'error' => ['class' => 'invalid-feedback']
                                ]) ?>
                            </div>
                        </div>
                    </div>
                    <?php if(empty($services)): ?>
                        <p>No Services available at this time.</p>
                    <?php else: ?>
                        <?= $this->Form->control('services._ids',[
                            'label' => __('Services'),
                            'type' => 'select',
                            'multiple' => 'checkbox',
                            'options' => $services,
                            ]); ?>
                    <?php endif; ?>
                </fieldset>
                <?= $this->Form->button(__('Submit'), ['class' => 'btn btn-primary']) ?>
                <?= $this->Form->end() ?>
            </div>
        </div>
        <?php $this->append('script'); ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const messageTextarea = document.getElementById('content-value-input');
                const charCountDisplay = document.getElementById('message-char-count');
                const maxLength = messageTextarea ? parseInt(messageTextarea.getAttribute('maxlength'), 10) : 0;

                function updateCharCount() {
                    if (!messageTextarea || !charCountDisplay || !maxLength) return;

                    const currentLength = messageTextarea.value.length;
                    const remaining = maxLength - currentLength;

                    charCountDisplay.textContent = `${currentLength}/${maxLength}`;

                    if (remaining < 0) {
                        charCountDisplay.style.color = 'red';
                    } else if (remaining < 50) {
                        charCountDisplay.style.color = 'orange';
                    } else {
                        charCountDisplay.style.color = '';
                    }
                }

                if (messageTextarea) {
                    messageTextarea.addEventListener('input', updateCharCount);
                    updateCharCount();
                }
            });
        </script>
        <?php $this->end(); ?>
    </div>
</div>
