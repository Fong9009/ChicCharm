<?php
/**
 * Login layout
 *
 * This layout comes with no navigation bar and Flash renderer placeholder. Usually used for login page or similar.
 *
 * @var \App\View\AppView $this
 */

use Cake\Core\Configure;

$appLocale = Configure::read('App.defaultLocale');
?>
<!DOCTYPE html>
<html lang="<?= $appLocale ?>">
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        <?= $this->fetch('title') ?> - Cake CMS/Auth Sample
    </title>
    <?= $this->Html->meta('icon') ?>

    <link href="https://fonts.googleapis.com/css?family=Merriweather+Sans:400,700" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css?family=Merriweather:400,300,300italic,400italic,700,700italic" rel="stylesheet" type="text/css" />
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <?= $this->Html->css('/landing-detail/css/styles.css') ?>
    <?= $this->Html->css(['fonts', 'cake', 'custom']) ?>
    <?= $this->Html->script('custom') ?>
    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>
</head>
<body>
<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-light fixed-top py-3" id="mainNav" style="background-color: #121211;">
    <div class="container px-4 px-lg-5">
        <!-- Mobile Navigation -->
        <div class="mobile-icons-nav">
            <!-- Hamburger Menu -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Brand/Logo -->
            <a class="navbar-brand" href="<?= $this->Url->build('/') ?>">ChicCharm</a>

            <!-- Profile Icon -->
            <div class="mobile-profile-icon">
                <button class="btn btn-link" type="button" id="mobileProfileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-user fa-lg"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="mobileProfileDropdown">
                    <?php $identity = $this->request->getAttribute('identity');
                    if ($identity) : ?>
                        <?php if ($identity->get('type') === 'admin') : ?>
                            <li><a class="dropdown-item" href="<?= $this->Url->build(['plugin' => false, 'controller' => 'Admins', 'action' => 'dashboard']) ?>">
                                <i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
                            <li><a class="dropdown-item" href="<?= $this->Url->build(['plugin' => false, 'controller' => 'Admins', 'action' => 'profile', $identity->get('id')]) ?>">
                                <i class="fas fa-user-shield"></i><span>My Profile</span></a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= $this->Url->build(['plugin' => false, 'controller' => 'Auth', 'action' => 'logout']) ?>" onclick="return confirmLogout()">
                                <i class="fas fa-sign-out-alt"></i><span>Logout</span></a></li>
                        <?php elseif ($identity->get('type') === 'customer') : ?>
                            <li><a class="dropdown-item" href="<?= $this->Url->build(['plugin' => false, 'controller' => 'Customers', 'action' => 'dashboard']) ?>">
                                <i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
                            <li><a class="dropdown-item" href="<?= $this->Url->build(['plugin' => false, 'controller' => 'Customers', 'action' => 'edit', $identity->get('id')]) ?>">
                                <i class="fas fa-user"></i><span>My Profile</span></a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= $this->Url->build(['plugin' => false, 'controller' => 'Auth', 'action' => 'logout']) ?>" onclick="return confirmLogout()">
                                <i class="fas fa-sign-out-alt"></i><span>Logout</span></a></li>
                        <?php endif; ?>
                    <?php else : ?>
                        <li><a class="dropdown-item" href="<?= $this->Url->build(['plugin' => false, 'controller' => 'Auth', 'action' => 'login']) ?>">
                            <i class="fas fa-sign-in-alt"></i><span>Login</span></a></li>
                        <li><a class="dropdown-item" href="<?= $this->Url->build(['plugin' => false, 'controller' => 'Customers', 'action' => 'registration']) ?>">
                            <i class="fas fa-user-plus"></i><span>Sign Up</span></a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <!-- Regular Navigation for Desktop -->
        <a class="navbar-brand d-none d-lg-block" href="<?= $this->Url->build('/') ?>">ChicCharm</a>
        <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav ms-auto my-2 my-lg-0">
                <?php
                if ($identity) {
                    // User is logged in
                    if ($identity->get('type') === 'admin') {
                        // Admin Navigation ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $this->Url->build('/#about') ?>">About</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $this->Url->build(['plugin' => false, 'controller' => 'Services', 'action' => 'servicePage']) ?>">Services</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $this->Url->build('/#portfolio') ?>">Portfolio</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="enquiriesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-envelope"></i>
                                <span>Enquiries</span>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="enquiriesDropdown">
                                <li>
                                    <a class="dropdown-item" href="<?= $this->Url->build([  'plugin' => false,'controller' => 'Contacts', 'action' => 'index']) ?>">
                                        <i class="fas fa-inbox"></i>
                                        <span>Active Messages</span>
                                    </a>
                                </li>
                                <li><div class="dropdown-divider"></div></li>
                                <li>
                                    <a class="dropdown-item" href="<?= $this->Url->build([ 'plugin' => false, 'controller' => 'Contacts', 'action' => 'archiveIndex']) ?>">
                                        <i class="fas fa-archive"></i>
                                        <span>Archived Messages</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="customersDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-users"></i>
                                <span>Customers</span>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="customersDropdown">
                                <li>
                                    <a class="dropdown-item" href="<?= $this->Url->build([ 'plugin' => false, 'controller' => 'Bookings', 'action' => 'index']) ?>">
                                        <i class="fas fa-list"></i>
                                        <span>Bookings</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="<?= $this->Url->build([ 'plugin' => false, 'controller' => 'Customers', 'action' => 'index']) ?>">
                                        <i class="fas fa-list"></i>
                                        <span>List Customers</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="<?= $this->Url->build([ 'plugin' => false, 'controller' => 'Customers', 'action' => 'registration']) ?>">
                                        <i class="fas fa-user-plus"></i>
                                        <span>Add Customer</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="stylistsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-cut"></i>
                                <span>Stylists</span>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="stylistsDropdown">
                                <li>
                                    <a class="dropdown-item" href="<?= $this->Url->build([ 'plugin' => false, 'controller' => 'Stylists', 'action' => 'index']) ?>">
                                        <i class="fas fa-list"></i>
                                        <span>List Stylists</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="<?= $this->Url->build([ 'plugin' => false, 'controller' => 'Stylists', 'action' => 'add']) ?>">
                                        <i class="fas fa-user-plus"></i>
                                        <span>Add Stylist</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-shield"></i>
                                <span>Admin</span>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="adminDropdown">
                                <li>
                                    <a class="dropdown-item" href="<?= $this->Url->build(['plugin' => false,'controller' => 'Admins', 'action' => 'dashboard'])?>">
                                        <i class="fas fa-tachometer-alt"></i>
                                        <span>Admin Dashboard</span>
                                    </a>
                                </li>
                                <li><div class="dropdown-divider"></div></li>
                                <li>
                                    <a class="dropdown-item" href="<?= $this->Url->build([ 'plugin' => false,'controller' => 'Admins', 'action' => 'index']) ?>">
                                        <i class="fas fa-users-cog"></i>
                                        <span>Admins List</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="<?= $this->Url->build(['plugin' => false,'controller' => 'Admins', 'action' => 'add']) ?>">
                                        <i class="fas fa-user-plus"></i>
                                        <span>Add New Admin</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="<?= $this->Url->build(['plugin' => false,'controller' => 'Admins', 'action' => 'profile', $identity->get('id')]) ?>">
                                        <i class="fas fa-user-shield"></i>
                                        <span>Admin Profile</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php } elseif ($identity->get('type') === 'customer') {
                        // Customer Navigation ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $this->Url->build('/#about') ?>">About</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $this->Url->build(['plugin' => false, 'controller' => 'Services', 'action' => 'servicePage']) ?>">Services</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $this->Url->build('/#portfolio') ?>">Portfolio</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link"
                               href="<?= $this->Url->build(['controller' => 'Contacts', 'action' => 'enquiry']) ?>"
                            >Contact Us
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link"
                               href="<?= $this->Url->build([
                                   'controller' => 'Customers',
                                   'action' => 'dashboard']) ?>">Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $this->Url->build(['plugin' => false,
                                'controller' => 'Customers', 'action' => 'edit', $identity->get('id')]) ?>">My Profile
                            </a>
                        </li>
                    <?php }
                    // Logout button for both admin and customer ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $this->Url->build(['plugin' => false,
                            'controller' => 'Auth', 'action' => 'logout']) ?>" onclick="return confirmLogout()">Logout
                        </a>
                    </li>
                <?php } else {
                    // Public Navigation ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $this->Url->build('/#about') ?>">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $this->Url->build(['plugin' => false, 'controller' => 'Services', 'action' => 'servicePage']) ?>">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $this->Url->build('/#portfolio') ?>">Portfolio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $this->Url->build('/contacts/enquiry') ?>">Contact Us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $this->Url->build('/auth/login') ?>">Login</a>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </div>
</nav>

<main class="main">
    <div class="container mt-4">
        <?= $this->Flash->render() ?>
    </div>
    <?= $this->fetch('content') ?>
</main>

<!-- Footer -->
<footer id="footer" class="footer bg-black text-white py-5">
    <div class="container text-center">
        <div class="row justify-content-center">
            <!-- Newsletter Signup -->
            <div class="col-md-3">
                <h5 class="text-light fw-bold mb-3">Sign Up for Our Newsletter</h5>
                <p class="text-light mb-3">Be the first to get notified about upcoming products and deals</p>
                <form method="post" action="<?= $this->Url->build(['plugin' => false, 'controller' => 'Newsletter', 'action' => 'subscribe']); ?>" id="ContactFooter" class="needs-confirmation newsletter-signup">
                    <?= $this->Form->hidden('_csrfToken', ['value' => $this->request->getAttribute('csrfToken')]) ?>
                    <div class="input-group">
                        <input type="email" class="form-control" name="email" placeholder="Enter your email" required>
                        <button type="submit" class="btn btn-primary">Subscribe</button>
                    </div>
                    <?php if ($this->request->getSession()->read('newsletter_success')) : ?>
                        <div class="newsletter-success text-light mt-2">
                            <small>Thank you for subscribing!</small>
                            <?php $this->request->getSession()->delete('newsletter_success'); ?>
                        </div>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Support Links -->
            <div class="col-md-3">
                <h5 class="text-light fw-bold">Support</h5>
                <ul class="list-unstyled">
                    <li><a href="<?= $this->Url->build('/contacts/enquiry') ?>" class="text-secondary">Contact us</a></li>
                </ul>
            </div>

            <!-- Our Company -->
            <div class="col-md-2">
                <h5 class="text-light fw-bold">Our Company</h5>
                <ul class="list-unstyled">
                    <li><a href="<?= $this->Url->build('/#about') ?>" class="text-secondary">About Us</a></li>
                </ul>
                <ul class="list-unstyled">
                    <li><a href="<?= $this->Url->build(['plugin' => false, 'controller' => 'Services', 'action' => 'servicePage']) ?>" class="text-secondary">Our Services</a></li>
                </ul>
            </div>
        </div>

        <!-- Divider -->
        <hr class="mt-4 mb-3 w-50 mx-auto" style="border-top: 1px solid rgba(255, 255, 255, 0.3);">

        <!-- Copyright -->
        <div class="text-center mt-3">
            <p class="mb-0 text-secondary">&copy; <?= date('Y'); ?> ChicCharm. All Rights Reserved.</p>
        </div>
    </div>
</footer>
<?= $this->Html->script('https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js') ?>
<?= $this->Html->script('https://cdnjs.cloudflare.com/ajax/libs/SimpleLightbox/2.1.0/simpleLightbox.min.js') ?>
<?= $this->Html->script('/landing-detail/js/scripts.js') ?>
</body>
</html>
