<?php
$titleDescription = "Landing Page";
?>

<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        <?= $titleDescription ?>:
        <?= $this->fetch('title') ?>
    </title>
    <?= $this->Html->meta('icon') ?>
    <?= $this->Html->css('/landing-detail/css/styles.css') ?>
    <?= $this->Html->css(['fonts', 'cake']) ?>

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>
</head>
<body>
<!-- Navigation-->
<nav class="navbar navbar-expand-lg navbar-light fixed-top py-3" id="mainNav" style="background-color: #121211;">
    <div class="container px-4 px-lg-5">
        <a class="navbar-brand" href="#page-top">ChicCharm</a>
        <button class="navbar-toggler navbar-toggler-right" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav ms-auto my-2 my-lg-0">
                <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
                <li class="nav-item"><a class="nav-link" href="#services">Services</a></li>
                <li class="nav-item"><a class="nav-link" href="#portfolio">Portfolio</a></li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $this->Url->build('/contact-us') ?>">Contact Us</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $this->Url->build('/login') ?>">Login</a>
                </li>

            </ul>
        </div>
    </div>
</nav>

<main>
        <?= $this->Flash->render() ?>
        <?= $this->fetch('content') ?>
</main>

<!-- Footer-->
<footer class="footer bg-black text-white py-5">
    <div class="container text-center">
        <div class="row justify-content-center">
            <!-- Newsletter Signup -->
            <div class="col-md-3">
                <h5 class="text-light fw-bold">Sign Up for Our Newsletter</h5>
                <form method="post" action="/contact#ContactFooter" id="ContactFooter">
                    <div class="input-group">
                        <input type="email" class="form-control" name="contact[email]" placeholder="Enter your email" required>
                        <button type="submit" class="btn btn-primary">Subscribe</button>
                    </div>
                </form>
            </div>

            <!-- Support Links -->
            <div class="col-md-3">
                <h5 class="text-light fw-bold">Support</h5>
                <ul class="list-unstyled">
                    <li><a href="/pages/faq" class="text-secondary">FAQ</a></li>
                    <li><a href="/pages/contact-us" class="text-secondary">Contact us</a></li>
                    <li><a href="/policies/shipping-policy" class="text-secondary">Shipping Policy</a></li>
                    <li><a href="/policies/refund-policy" class="text-secondary">Refund Policy</a></li>
                </ul>
            </div>

            <!-- Our Company -->
            <div class="col-md-2">
                <h5 class="text-light fw-bold">Our Company</h5>
                <ul class="list-unstyled">
                    <li><a href="/pages/about" class="text-secondary">About Us</a></li>
                    <li><a href="/policies/terms-of-service" class="text-secondary">Terms Of Service</a></li>
                    <li><a href="/policies/privacy-policy" class="text-secondary">Privacy Policy</a></li>
                    <li><a href="/pages/accessibility-statement" class="text-secondary">Accessibility Statement</a></li>
                </ul>
            </div>

            <!-- Social Media -->
            <div class="col-md-2">
                <h5 class="text-light fw-bold">Follow Us</h5>
                <div class="d-flex justify-content-center">
                    <a href="https://facebook.com" class="text-white me-3 fs-4"><i class="bi bi-facebook"></i></a>
                    <a href="https://instagram.com" class="text-white me-3 fs-4"><i class="bi bi-instagram"></i></a>
                    <a href="https://youtube.com" class="text-white fs-4"><i class="bi bi-youtube"></i></a>
                </div>
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

</body>

