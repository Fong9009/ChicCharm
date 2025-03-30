<?php
$this->layout = 'default';
?>
<?= $this->Html->css('/dashboard/dash.css') ?>
<?= $this->Html->css('https://fonts.googleapis.com/icon?family=Material+Icons') ?>
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

            <div class="col-xl-6 col-sm-12 mb-xl-0 mb-4">
                <div class="card">
                    <div class="card-header admin-card-header p-3 pt-2 d-flex justify-content-between align-items-center">
                        <div class="text-left">
                            <h4 class="mb-0 admin-card-h4">Admins</h4>
                        </div>
                        <i class="material-icons admin-icon">person</i>
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

            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card">
                    <div class="card-header p-3 pt-2">
                        <div class="text-center py-5">
                            <h4 class="mb-0">Contractors</h4>
                        </div>
                    </div>
                    <hr class="dark horizontal my-0">
                    <div class="card-footer p-3">
                        <?= $this->Html->link(
                            'To Contractor Lists',
                            ['controller' => 'Contractors', 'action' => 'index'],
                            ['class' => 'btn btn-warning w-100', 'escape' => false]
                        ) ?>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card">
                    <div class="card-header p-3 pt-2">
                        <div class="text-center py-5">
                            <h4 class="mb-0">Contacts</h4>
                        </div>
                    </div>
                    <div class="card-body text-center">
                        <h3>Total Contacts</h3>
                        <p><?= h($contactCount)?></p>
                    </div>
                    <hr class="dark horizontal my-0">
                    <div class="card-footer p-3">
                        <?= $this->Html->link(
                            'To Contact Lists',
                            ['controller' => 'Contacts', 'action' => 'index'],
                            ['class' => 'btn btn-warning w-100', 'escape' => false]
                        ) ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card">
                    <div class="card-header p-3 pt-2">
                        <div class="text-center py-5">
                            <h4 class="mb-0">Bookings</h4>
                        </div>
                    </div>
                    <hr class="dark horizontal my-0">
                    <div class="card-footer p-3">
                        <?= $this->Html->link(
                            'To Booking Lists',
                            ['controller' => 'Bookings', 'action' => 'index'],
                            ['class' => 'btn btn-warning w-100', 'escape' => false]
                        ) ?>
                    </div>
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
