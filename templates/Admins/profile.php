<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Admin $admin
 */
$this->layout = 'default';
?>
<div class="customer-dashboard" style="background-image: url(<?= $this->Url->image('customerbackground.jpg')?>);">
    <div class="container">
        <div class="card">
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-12">
                        <h2>Welcome, <?= h($admin->first_name) ?> <?= h($admin->last_name) ?>!</h2>
                    </div>
                </div>

                <!--Profile Picture and Summary-->
                <div class="row">
                    <div class="col-lg-6 col-md-12 col-sm-12 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h3 class="card-title">Profile Picture</h3>
                            </div>
                            <div class="card-body text-center">
                                <?php if (!empty($admin->profile_picture)) : ?>
                                    <img src="<?= $this->Url->image('profile/' . $admin->profile_picture) ?>"
                                         alt="Profile Picture"
                                         class="profile-picture img-fluid rounded mx-auto d-block">
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-12 mb-4 col-sm-12">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h3 class="card-title">Profile Summary</h3>
                                <?= $this->Html->link(
                                    'Edit Profile',
                                    ['action' => 'edit', $admin->id],
                                    ['class' => 'btn btn-primary']
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
