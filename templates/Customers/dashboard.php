<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Customer $customer
 */
?>
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2>Welcome, <?= h($customer->first_name) ?> <?= h($customer->last_name) ?>!</h2>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Profile Summary</h3>
                    <?= $this->Html->link(
                        'Edit Profile',
                        ['action' => 'edit', $customer->id],
                        ['class' => 'btn btn-primary']
                    ) ?>
                </div>
                <div class="card-body">
                <p><strong>Name:</strong> <?= h($customer->first_name) ?> <?= h($customer->last_name) ?></p>
                    <p><strong>Email:</strong> <?= h($customer->email) ?></p>
                    <p><strong>Member Since:</strong> <?= $customer->created->format('F Y') ?></p>
                </div>
            </div>
        </div>
    </div>
</div>
