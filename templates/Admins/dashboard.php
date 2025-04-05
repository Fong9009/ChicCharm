<?php
$this->layout = 'default';
?>
<?= $this->Html->css('/dashboard/dash.css') ?>
<?= $this->Html->css('https://fonts.googleapis.com/icon?family=Material+Icons') ?>
<?= $this->Html->script('/dashboard/time.js') ?>
<head>
    <title>Dashboard</title>
</head>

<body class="g-sidenav-show  bg-gray-200">
<main>
    <div class="p-4 card" style="height: 1600px">
        <div class="row">
            <h1>Welcome back, <?= h($adminName)?></h1>
        </div>
        <div class="row">
            <h1 id="timeValue"></h1>
        </div>
        <div class="row mt-4">
            <!--Customers Card-->
            <div class="col-xl-6 col-sm-12 mb-xl-0 mb-4">
                <div class="card">
                    <div class="card-header customer-card-header p-3 pt-2 d-flex justify-content-between align-items-center">
                        <div class="text-left">
                            <h4 class="mb-0 customer-card-h4">Customers</h4>
                        </div>
                        <i class="material-icons customer-icon">person</i>
                    </div>
                    <div class="card-body customer-card-body text-left">
                        <h3 class="customer-card-h3">Total Customers</h3>
                        <div class="total-value-container">
                            <p class="total-value customer-card-count"><?= h($custCount) ?></p>
                        </div>
                    </div>
                    <hr class="dark horizontal my-0">
                    <div class="card-footer customer-card-footer p-3">
                        <?= $this->Html->link(
                            'View Customer Lists  <i class="material-icons">visibility</i>',
                            ['controller' => 'Customers', 'action' => 'index'],
                            ['class' => 'btn customer-button w-100', 'escape' => false]
                        ) ?>
                    </div>
                </div>
            </div>
            <!--Admins Card-->
            <div class="col-xl-6 col-sm-12 mb-xl-0 mb-4">
                <div class="card">
                    <div class="card-header admin-card-header p-3 pt-2 d-flex justify-content-between align-items-center">
                        <div class="text-left">
                            <h4 class="mb-0 admin-card-h4">Admins</h4>
                        </div>
                        <i class="material-icons admin-icon">engineering</i>
                    </div>
                    <div class="card-body admin-card-body text-left">
                        <h3 class="customer-card-h3">Total Admins</h3>
                        <div class="total-value-container">
                            <p class="total-value admin-card-count"><?= h($adminCount) ?></p>
                        </div>
                    </div>
                    <hr class="dark horizontal my-0">
                    <div class="card-footer admin-card-footer p-3">
                        <?= $this->Html->link(
                            'View Admin Lists  <i class="material-icons">visibility</i>',
                            ['controller' => 'Admins', 'action' => 'index'],
                            ['class' => 'btn admin-button w-100', 'escape' => false]
                        ) ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <!--Contractor Card-->
            <div class="col-xl-6 col-sm-12 mb-xl-0 mb-4">
                <div class="card">
                    <div class="card-header contractor-card-header p-3 pt-2 d-flex justify-content-between align-items-center">
                        <div class="text-left">
                            <h4 class="mb-0 contractor-card-h4">Stylists</h4>
                        </div>
                        <i class="material-icons contractor-icon">group</i>
                    </div>
                    <div class="card-body contractor-card-body text-left">
                        <h3 class="customer-card-h3">Total Contractors</h3>
                        <div class="total-value-container">
                            <p class="total-value contractor-card-count"><?= h($stylistCount) ?></p>
                        </div>
                    </div>
                    <hr class="dark horizontal my-0">
                    <div class="card-footer contractor-card-footer p-3">
                        <?= $this->Html->link(
                            'View Contractor Lists  <i class="material-icons">visibility</i>',
                            ['controller' => 'Stylists', 'action' => 'index'],
                            ['class' => 'btn contractor-button w-100', 'escape' => false]
                        ) ?>
                    </div>
                </div>
            </div>
            <!--Contact Card-->
            <div class="col-xl-6 col-sm-12 mb-xl-0 mb-4">
                <div class="card">
                    <div class="card-header contact-card-header p-3 pt-2 d-flex justify-content-between align-items-center">
                        <div class="text-left">
                            <h4 class="mb-0 contact-card-h4">Contacts</h4>
                        </div>
                        <i class="material-icons contact-icon">chat</i>
                    </div>
                    <div class="card-body contact-card-body text-left">
                        <h3 class="customer-card-h3">Total Contacts</h3>
                        <div class="total-value-container">
                            <p class="total-value contractor-card-count"><?= h($contactCount) ?></p>
                        </div>
                    </div>
                    <hr class="dark horizontal my-0">
                    <div class="card-footer contact-card-footer p-3">
                        <?= $this->Html->link(
                            'View Contact Lists  <i class="material-icons">visibility</i>',
                            ['controller' => 'Admins', 'action' => 'index'],
                            ['class' => 'btn contact-button w-100', 'escape' => false]
                        ) ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <!--Bookings Card-->
            <div class="col-xl-6 col-sm-12 mb-xl-0 mb-4">
                <div class="card">
                    <div class="card-header booking-card-header p-3 pt-2 d-flex justify-content-between align-items-center">
                        <div class="text-left">
                            <h4 class="mb-0 booking-card-h4">Bookings</h4>
                        </div>
                        <i class="material-icons booking-icon">chat</i>
                    </div>
                    <div class="card-body booking-card-body text-left">
                        <h3 class="customer-card-h3">Total Bookings</h3>
                        <div class="total-value-container">
                            <p class="total-value booking-card-count"><?= h($contactCount) ?></p>
                        </div>
                    </div>
                    <hr class="dark horizontal my-0">
                    <div class="card-footer booking-card-footer p-3">
                        <?= $this->Html->link(
                            'View Booking Lists  <i class="material-icons">visibility</i>',
                            ['controller' => 'Admins', 'action' => 'dashboard'],
                            ['class' => 'btn booking-button w-100', 'escape' => false]
                        ) ?>
                    </div>
                </div>
            </div>
            <!--Payments Card-->
            <div class="col-xl-6 col-sm-12 mb-xl-0 mb-4">
                <div class="card">
                    <div class="card-header payment-card-header p-3 pt-2 d-flex justify-content-between align-items-center">
                        <div class="text-left">
                            <h4 class="mb-0 payment-card-h4">Payments</h4>
                        </div>
                        <i class="material-icons payment-icon">payments</i>
                    </div>
                    <div class="card-body payment-card-body text-left">
                        <h3 class="customer-card-h3">Total Payments</h3>
                        <div class="total-value-container">
                            <p class="total-value payment-card-count"><?= h($contactCount) ?></p>
                        </div>
                    </div>
                    <hr class="dark horizontal my-0">
                    <div class="card-footer payment-card-footer p-3">
                        <?= $this->Html->link(
                            'View Payment Lists  <i class="material-icons">visibility</i>',
                            ['controller' => 'Admins', 'action' => 'dashboard'],
                            ['class' => 'btn payment-button w-100', 'escape' => false]
                        ) ?>
                    </div>
                </div>
            </div>
        </div>

    <footer class="footer py-4  ">
        <div class="container-fluid">
            <div class="row align-items-center justify-content-lg-between">
                <div class="col-lg-6 mb-lg-0 mb-4">
                    <div class="copyright text-center text-sm text-muted text-lg-start">
                    </div>
                </div>
            </div>
    </footer>
</main>
</body>
</html>
