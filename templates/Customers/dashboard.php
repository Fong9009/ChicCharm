<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Customer $customer
 */
$this->layout = 'default';
?>
<body style="background-image: url(<?= $this->Url->image('customerbackground.jpg')?>); background-size: cover; background-position: center center; background-repeat: no-repeat; background-attachment: fixed">
<div class="container py-4">
    <div class="p-4 card" style="height: 1600px">
        <div class="row mb-4">
            <div class="col-12">
                <h2>Welcome, <?= h($customer->first_name) ?> <?= h($customer->last_name) ?>!</h2>
            </div>
        </div>
        <!--Profile Picture-->
        <div class="row">
            <div class="col-md-12 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">Profile Picture</h3>
                        <?= $this->Html->link(
                            'Edit Profile Picture',
                            ['action' => 'edit', $customer->id],
                            ['class' => 'btn btn-primary']
                        ) ?>
                    </div>
                    <div class="card-body">
                    </div>
                </div>
            </div>
        </div>
        <!--Account Details-->
        <div class="row">
            <div class="col-md-12 mb-4">
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
        <!--View Current Bookings-->
        <div class="row">
            <div class="col-md-12 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">Current Bookings</h3>
                        <?= $this->Html->link(
                            'Create Booking',
                            ['action' => 'edit', $customer->id],
                            ['class' => 'btn btn-primary']
                        ) ?>
                    </div>
                    <div class="card-body">
                    </div>
                </div>
            </div>
        </div>
        <!--View Past Bookings-->
        <div class="row">
            <div class="col-md-12 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">Past Bookings</h3>
                    </div>
                    <div class="card-body">
                    </div>
                </div>
            </div>
        </div>
        <!--View Past Payments-->
        <div class="row">
            <div class="col-md-12 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">Past Invoices</h3>
                    </div>
                    <div class="card-body">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
