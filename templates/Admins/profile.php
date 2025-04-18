<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Admin $admin
 */
$this->layout = 'default';
?>
<?= $this->Html->css('/dashboard/profiledash.css') ?>
<?= $this->Html->css(['fonts', 'cake', 'custom']) ?>
<?= $this->Html->script('custom') ?>
<?= $this->Html->css('https://fonts.googleapis.com/icon?family=Material+Icons') ?>

<!-- Admin Profile Card -->
<div class="customer-dashboard" style="background-image: url(<?= $this->Url->image('customerbackground.jpg')?>);">
    <div class="container">
        <div class="card">
            <div class="card-body background-colour">
                <div class="card h-100 mb-4">
                    <div class="card-header d-flex justify-content-between customer-card-header" style="background-color:#D7CCC8">
                        <h2 class="fw-bold">Welcome, <?= h($admin->first_name) ?> <?= h($admin->last_name) ?>!</h2>
                    </div>
                    <?= $this->Flash->render() ?>
                    <div class="card-body">
                        <div class="row mb-4 p-1">
                            <div class="w-100" style="height: 2px; background-color: #6c757d;"></div>
                        </div>
                        <!--Profile Picture and Summary-->
                        <div class="row">
                            <div class="col-lg-4 col-md-12 col-sm-12 mb-4">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <?php if (!empty($admin->profile_picture)) : ?>
                                            <img src="<?= $this->Url->image('profile/' . $admin->profile_picture) ?>"
                                                 alt="Profile Picture"
                                                 class="profile-picture img-fluid rounded mx-auto d-block">
                                        <?php else : ?>
                                            <img src="<?= $this->Url->image('profile/blank-profile.png') ?>"
                                                 alt="Profile Picture"
                                                 class="profile-picture img-fluid rounded mx-auto d-block">
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-8 col-md-12 mb-4 col-sm-12">
                                <div class="card h-100">
                                    <div class="card-header d-flex justify-content-between align-items-center customer-card-header" style="background-color:#D7CCC8">
                                        <h3 class="card-title">Profile Summary</h3>
                                        <?= $this->Html->link(
                                            'Edit Profile <i class="material-icons view-icon small-icon">edit</i>',
                                            ['action' => 'edit', $admin->id],
                                            ['class' => 'btn btn-primary', 'escape' => false]
                                        ) ?>
                                    </div>
                                    <div class="card-body">
                                        <p><strong>Name:</strong> <?= h($admin->first_name) ?> <?= h($admin->last_name) ?></p>
                                        <p><strong>Email:</strong> <?= h($admin->email) ?></p>
                                        <p><strong>Admin Since:</strong> <?= $admin->created->format('F Y') ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
