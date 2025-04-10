<?php
$this->layout = 'default';
$identity = $this->request->getAttribute('identity');
?>
<?= $this->Html->css('/dashboard/dash.css') ?>
<?= $this->Html->css('https://fonts.googleapis.com/icon?family=Material+Icons') ?>
<?= $this->Html->script('/dashboard/time.js') ?>

<main class="admin-dashboard">
    <div class="dashboard-container">
        <!-- Welcome, Section -->
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="welcome-text">Welcome back, <?= h($adminName)?></h1>
                <h2 id="timeValue" class="mt-2"></h2>
            </div>
        </div>
        <!-- Admin Personal Utility-->
        <div>
            <h2 class="welcome-text"> Admin Shortcuts</h2>
        </div>
        <hr class="flex-grow-1 mx-auto" style="border: none; height: 3px; background-color: #050505;"/>
        <div class="row">
            <div class="col-lg-3  mb-4">
                <div class="card h-100">
                    <div class="card-header admin-card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="admin-card-h4">Profile</h4>
                        </div>
                        <i class="material-icons admin-icon">engineering</i>
                    </div>
                    <div class="card-body admin-card-body"></div>
                    <div class="card-footer admin-card-footer">
                        <?= $this->Html->link(
                            '<span class="fs-5">View Admin Profile</span><i class="material-icons">visibility</i>',
                            ['controller' => 'Admins', 'action' => 'profile', $identity->get('id')],
                            ['class' => 'admin-button', 'escape' => false]
                        ) ?>
                    </div>
                </div>
            </div>
        </div>

        <div>
            <h2 class="welcome-text"> Quick Access</h2>
        </div>
        <hr class="flex-grow-1 mx-auto" style="border: none; height: 3px; background-color: #050505;"/>
        <!-- First Row: Customers and Admins -->
        <div class="row">
            <!-- Customers Card -->
            <div class="col-lg-6 col-md-6 col-sm-12 mb-4">
                <div class="card h-100">
                    <div class="card-header customer-card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="customer-card-h4">Customers</h4>
                        </div>
                        <i class="material-icons customer-icon">person</i>
                    </div>
                    <div class="card-body customer-card-body">
                        <h3 class="customer-card-h3">Total Customers</h3>
                        <div class="total-value-container">
                            <p class="customer-card-count"><?= h($custCount) ?></p>
                        </div>
                    </div>
                    <div class="card-footer customer-card-footer">
                        <?= $this->Html->link(
                            '<span class="fs-5">View Customer Lists</span><i class="material-icons">visibility</i>',
                            ['controller' => 'Customers', 'action' => 'index'],
                            ['class' => 'customer-button', 'escape' => false]
                        ) ?>
                    </div>
                </div>
            </div>

            <!-- Admins Card -->
            <div class="col-lg-6 col-md-6 col-sm-12 mb-4">
                <div class="card h-100">
                    <div class="card-header admin-card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="admin-card-h4">Admins</h4>
                        </div>
                        <i class="material-icons admin-icon">engineering</i>
                    </div>
                    <div class="card-body admin-card-body">
                        <h3 class="admin-card-h3">Total Admins</h3>
                        <div class="total-value-container">
                            <p class="admin-card-count"><?= h($adminCount) ?></p>
                        </div>
                    </div>
                    <div class="card-footer admin-card-footer">
                        <?= $this->Html->link(
                            '<span class="fs-5">View Admin Lists</span><i class="material-icons">visibility</i>',
                            ['controller' => 'Admins', 'action' => 'index'],
                            ['class' => 'admin-button', 'escape' => false]
                        ) ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Second Row: Stylists and Contacts -->
        <div class="row">
            <!-- Stylists Card -->
            <div class="col-lg-6 col-md-6 col-sm-12 mb-4">
                <div class="card h-100">
                    <div class="card-header contractor-card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="contractor-card-h4">Stylists</h4>
                        </div>
                        <i class="material-icons contractor-icon">group</i>
                    </div>
                    <div class="card-body contractor-card-body">
                        <h3 class="contractor-card-h3">Total Contractors</h3>
                        <div class="total-value-container">
                            <p class="contractor-card-count"><?= h($stylistCount) ?></p>
                        </div>
                    </div>
                    <div class="card-footer contractor-card-footer">
                        <?= $this->Html->link(
                            '<span class="fs-5">View Stylist Lists</span><i class="material-icons">visibility</i>',
                            ['controller' => 'Stylists', 'action' => 'index'],
                            ['class' => 'contractor-button', 'escape' => false]
                        ) ?>
                    </div>
                </div>
            </div>

            <!-- Contacts Card -->
            <div class="col-lg-6 col-md-6 col-sm-12 mb-4">
                <div class="card h-100">
                    <div class="card-header contact-card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="contact-card-h4">Contacts</h4>
                        </div>
                        <i class="material-icons contact-icon">chat</i>
                    </div>
                    <div class="card-body contact-card-body">
                        <h3 class="contact-card-h3">Total Contacts</h3>
                        <div class="total-value-container">
                            <p class="contact-card-count"><?= h($contactCount) ?></p>
                        </div>
                    </div>
                    <div class="card-footer contact-card-footer">
                        <?= $this->Html->link(
                            '<span class="fs-5">View Contact Lists</span><i class="material-icons">visibility</i>',
                            ['controller' => 'Contacts', 'action' => 'index'],
                            ['class' => 'contact-button', 'escape' => false]
                        ) ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Third Row: Bookings and Payments -->
        <div class="row">
            <!-- Bookings Card -->
            <div class="col-lg-6 col-md-6 col-sm-12 mb-4">
                <div class="card h-100">
                    <div class="card-header booking-card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="booking-card-h4">Bookings</h4>
                        </div>
                        <i class="material-icons booking-icon">event</i>
                    </div>
                    <div class="card-body booking-card-body">
                        <h3 class="booking-card-h3">Total Bookings</h3>
                        <div class="total-value-container">
                            <p class="booking-card-count"><?= h($bookingCount) ?></p>
                        </div>
                    </div>
                    <div class="card-footer booking-card-footer">
                        <?= $this->Html->link(
                            '<span class="fs-5">View Booking Lists</span><i class="material-icons">visibility</i>',
                            ['controller' => 'Bookings', 'action' => 'index'],
                            ['class' => 'booking-button', 'escape' => false]
                        ) ?>
                    </div>
                </div>
            </div>

            <!-- Payments Card -->
            <div class="col-lg-6 col-md-6 col-sm-12 mb-4">
                <div class="card h-100">
                    <div class="card-header payment-card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="payment-card-h4">Payments</h4>
                        </div>
                        <i class="material-icons payment-icon">payments</i>
                    </div>
                    <div class="card-body payment-card-body">
                        <h3 class="payment-card-h3">Total Payments</h3>
                        <div class="total-value-container">
                            <p class="payment-card-count"><?= h($contactCount) ?></p>
                        </div>
                    </div>
                    <div class="card-footer payment-card-footer">
                        <?= $this->Html->link(
                            '<span class="fs-5">View Payment Lists</span><i class="material-icons">visibility</i>',
                            ['controller' => 'Payments', 'action' => 'index'],
                            ['class' => 'payment-button', 'escape' => false]
                        ) ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- 4th Row: Services -->
        <div class="row">
            <!-- Services Card -->
            <div class="col-lg-6 col-md-6 col-sm-12 mb-4">
                <div class="card h-100">
                    <div class="card-header service-card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="service-card-h4">Services</h4>
                        </div>
                        <i class="material-icons service-icon">settings</i>
                    </div>
                    <div class="card-body service-card-body">
                        <h3 class="service-card-h3">Total Services</h3>
                        <div class="total-value-container">
                            <p class="service-card-count"><?= h($serviceCount) ?></p>
                        </div>
                    </div>
                    <div class="card-footer service-card-footer">
                        <?= $this->Html->link(
                            '<span class="fs-5">View Stylist Lists</span><i class="material-icons">visibility</i>',
                            ['controller' => 'Services', 'action' => 'index'],
                            ['class' => 'service-button', 'escape' => false]
                        ) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<footer class="footer py-4  ">
    <div class="container-fluid">
        <div class="row align-items-center justify-content-lg-between">
            <div class="col-lg-6 mb-lg-0 mb-4">
                <div class="copyright text-center text-sm text-muted text-lg-start">
                </div>
            </div>
        </div>
</footer>
