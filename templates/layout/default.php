<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         0.10.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 * @var \App\View\AppView $this
 */

$cakeDescription = 'CakePHP: the rapid development php framework';
$isPublicPage = $this->request->getParam('controller') === 'Contacts' && $this->request->getParam('action') === 'add';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        <?php if ($this->request->getAttribute('identity')): ?>
            <?= $this->request->getAttribute('identity')->type === 'Admin' ? 'Admin - ' : 'Customer - ' ?>
            <?= $this->fetch('title') ?>
        <?php else: ?>
            ChicCharm
        <?php endif; ?>
    </title>
    <!-- Google fonts-->
    <link href="https://fonts.googleapis.com/css?family=Merriweather+Sans:400,700" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css?family=Merriweather:400,300,300italic,400italic,700,700italic" rel="stylesheet" type="text/css" />
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <?= $this->Html->meta('icon') ?>
    <?= $this->Html->css('/landing-detail/css/styles.css') ?>
    <?= $this->Html->css(['fonts', 'cake', 'custom']) ?>
    <?= $this->Html->script('custom') ?>

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>
</head>

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
                        <?php elseif ($identity->get('type') === 'stylist') : ?>
                            <li><a class="dropdown-item" href="<?= $this->Url->build(['plugin' => false, 'controller' => 'Stylists', 'action' => 'dashboard']) ?>">
                                    <i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
                            <li><a class="dropdown-item" href="<?= $this->Url->build(['plugin' => false, 'controller' => 'Auth', 'action' => 'logout']) ?>" onclick="return confirmLogout()">
                                    <i class="fas fa-sign-out-alt"></i><span>Logout</span></a></li>
                        <?php endif; ?>
                    <?php else : ?>
                        <li><a class="dropdown-item" href="<?= $this->Url->build(['plugin' => false, 'controller' => 'Auth', 'action' => 'login']) ?>">
                            <i class="fas fa-sign-in-alt"></i><span>Login</span></a></li>
                        <li><a class="dropdown-item" href="<?= $this->Url->build(['plugin' => false, 'controller' => 'Customers', 'action' => 'registration']) ?>">
                            <i class="fas fa-user-plus"></i><span>Sign Up</span></a></li>
                        <?php if (!empty($pendingGuestBookingToken)): ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= $this->Url->build(['controller' => 'Bookings', 'action' => 'viewPendingGuestBooking', $pendingGuestBookingToken]) ?>">
                                <i class="fas fa-shopping-cart"></i><span>View Pending Booking</span></a></li>
                        <?php endif; ?>
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
                            <a class="nav-link" href="<?= $this->Url->build(['plugin' => false, 'controller' => 'Stylists', 'action' => 'stylistOverview']) ?>">Stylists</a>
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
                            <a class="nav-link" href="<?= $this->Url->build(['plugin' => false, 'controller' => 'Stylists', 'action' => 'stylistOverview']) ?>">Stylists</a>
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
                    <?php } elseif ($identity->get('type') === 'stylist') {
                        // Customer Navigation ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $this->Url->build('/#about') ?>">About</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $this->Url->build(['plugin' => false, 'controller' => 'Services', 'action' => 'servicePage']) ?>">Services</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $this->Url->build(['plugin' => false, 'controller' => 'Stylists', 'action' => 'stylistOverview']) ?>">Stylists</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link"
                               href="<?= $this->Url->build([
                                   'controller' => 'Stylists',
                                   'action' => 'dashboard']) ?>">Dashboard
                            </a>
                        </li>
                    <?php } else {
                    // Customer Navigation ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $this->Url->build('/#about') ?>">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $this->Url->build(['plugin' => false, 'controller' => 'Services', 'action' => 'servicePage']) ?>">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $this->Url->build(['plugin' => false, 'controller' => 'Stylists', 'action' => 'stylistOverview']) ?>">Stylists</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link"
                           href="<?= $this->Url->build([
                               'controller' => 'Bookings',
                               'action' => 'guestbooking']) ?>">Make a Booking
                        </a>
                    </li>
                    <?php if ($this->request->getSession()->check('GuestBooking.pending_details')): ?>
                        <li class="nav-item">
                            <a class="nav-link"
                               href="<?= $this->Url->build([
                                   'controller' => 'Bookings',
                                   'action' => 'viewPendingGuestBooking']) ?>">View Pending Booking
                            </a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $this->Url->build(['plugin' => false,
                            'controller' => 'Auth', 'action' => 'login']) ?>">Login
                        </a>
                    </li>
                    <?php }
                    // Logout button for both admin and customer ?>
                    <?php
                    $identityType = $identity->get('type');
                    if (isset($identityType) && in_array($identityType, ['admin', 'customer', 'stylist'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $this->Url->build(['plugin' => false,
                                'controller' => 'Auth', 'action' => 'logout']) ?>" onclick="return confirmLogout()">Logout
                            </a>
                        </li>
                    <?php endif; ?>
                <?php } else {
                    // Public Navigation ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $this->Url->build('/#about') ?>">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $this->Url->build(['plugin' => false, 'controller' => 'Services', 'action' => 'servicePage']) ?>">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $this->Url->build(['plugin' => false, 'controller' => 'Stylists', 'action' => 'stylistOverview']) ?>">Stylists</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $this->Url->build('/contacts/enquiry') ?>">Contact Us</a>
                    </li>
                    <?php if (!empty($pendingGuestBookingToken)): ?>
                        <li class="nav-item">
                            <a class="nav-link text-warning" href="<?= $this->Url->build(['controller' => 'Bookings', 'action' => 'viewPendingGuestBooking', $pendingGuestBookingToken]) ?>">
                                <i class="fas fa-shopping-cart"></i> View Pending Booking
                            </a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $this->Url->build('/auth/login') ?>">Login</a>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </div>
</nav>
<body>

<!-- Main Content Area -->
<main class="main">
    <?= $this->Flash->render() ?>
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
                <form method="post" action="<?= $this->Url->build(['plugin' => false, 'controller' => 'Newsletter', 'action' => 'subscribe']); ?>" id="newsletterSubscribeForm" class="needs-confirmation newsletter-signup">
                    <?= $this->Form->hidden('_csrfToken', ['value' => $this->request->getAttribute('csrfToken'), 'id' => 'newsletter-csrf-token']) ?>
                    <div class="input-group">
                        <input type="email" class="form-control" name="email" id="newsletter-email-input" placeholder="Enter your email" required>
                        <button type="submit" class="btn btn-primary">Subscribe</button>
                    </div>
                    <div id="newsletter-response-message" class="mt-2"></div>
                </form>
            </div>

            <!-- Support Links -->
            <div class="col-md-3">
                <h5 class="text-light fw-bold">Support</h5>
                <ul class="list-unstyled">
                    <li><i class="bi bi-telephone-fill"></i> <?= $this->ContentBlock->text('contact-phone'); ?></li>
                </ul>
                <ul class="list-unstyled">
                    <li><a href="<?= $this->Url->build('/contacts/enquiry') ?>" class="text-secondary">Contact us</a></li>
                </ul>
                <ul class="list-unstyled">
                    <li><a href="<?= $this->Url->build('/faq/questions') ?>" class="text-secondary">FAQ</a></li>
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
                <ul class="list-unstyled">
                    <li><a href="<?= $this->Url->build(['plugin' => false, 'controller' => 'Stylists', 'action' => 'stylistOverview']) ?>" class="text-secondary">Our Stylists</a></li>
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

<!-- Bootstrap core JS-->
<?= $this->Html->script('https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js') ?>
<?= $this->Html->script('https://cdnjs.cloudflare.com/ajax/libs/SimpleLightbox/2.1.0/simpleLightbox.min.js') ?>
<?= $this->Html->script('landing-detail/js/scripts.js', ['block' => true]) ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const newsletterForm = document.getElementById('newsletterSubscribeForm');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(event) {
            event.preventDefault();

            const emailInput = document.getElementById('newsletter-email-input');
            const csrfTokenInput = document.getElementById('newsletter-csrf-token');
            const messageArea = document.getElementById('newsletter-response-message');

            const email = emailInput.value;
            const csrfToken = csrfTokenInput.value;
            const actionUrl = newsletterForm.getAttribute('action');

            messageArea.innerHTML = '';
            messageArea.className = 'mt-2';

            // Basic client-side validation (optional, server validates too)
            if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                messageArea.innerHTML = '<small style="color: #f4623a;">Please enter a valid email address.</small>';
                messageArea.classList.add('text-danger'); // Example error styling
                return;
            }

            const formData = new URLSearchParams();
            formData.append('email', email);
            // Note: CakePHP's CsrfProtectionMiddleware typically expects the token in the X-CSRF-Token header for AJAX,
            // or as a form field named '_csrfToken'. Sending it as a form field is simpler here.
            // If header is preferred, add it to fetch options and remove from formData.
            formData.append('_csrfToken', csrfToken);


            fetch(actionUrl, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: formData.toString()
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    messageArea.innerHTML = '<small style="color: #d4edda; background-color: rgba(21, 87, 36, 0.3); border-left: 3px solid #155724; padding: 5px 10px; border-radius: 4px; display: inline-block;">' + data.message + '</small>';
                    emailInput.value = '';
                } else {
                    messageArea.innerHTML = '<small style="color: #f4623a;">' + (data.message || 'An error occurred.') + '</small>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                messageArea.innerHTML = '<small style="color: #f4623a;">Subscription request failed. Please try again.</small>';
                // Optionally, add a class for error styling
            });
        });
    }
});
</script>

<script>
function confirmLogout() {
    return confirm('Are you sure you want to logout?');
}
</script>
</body>
</html>


