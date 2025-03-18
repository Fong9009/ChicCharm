<?php
$this->layout = 'publiclayout';
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Creative - Start Bootstrap Theme</title>
        <!-- Favicon-->
        <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
        <!-- Google fonts-->
        <link href="https://fonts.googleapis.com/css?family=Merriweather+Sans:400,700" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css?family=Merriweather:400,300,300italic,400italic,700,700italic" rel="stylesheet" type="text/css" />


        <?= $this->Html->css('https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css', ['rel' => 'stylesheet']); ?>
        <!-- SimpleLightbox plugin CSS-->
        <?= $this->Html->css('https://cdnjs.cloudflare.com/ajax/libs/SimpleLightbox/2.1.0/simpleLightbox.min.css', ['rel' => 'stylesheet']); ?>
        <?= $this->Html->charset() ?>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>
            <?= $this->fetch('title') ?>
        </title>
        <?= $this->Html->meta('icon') ?>
        <!-- Core theme CSS (includes Bootstrap)-->
        <?= $this->Html->css('/landing-detail/css/styles.css') ?>
        <?= $this->Html->css(['fonts', 'cake']) ?>

        <?= $this->fetch('meta') ?>
        <?= $this->fetch('css') ?>
        <?= $this->fetch('script') ?>
    </head>
    <body id="page-top">
        <!-- Masthead-->
        <header class="masthead" style="background-image: url('<?= $this->Url->image('bg-mastheadv2.jpg')?>'); background-size: cover; background-position: center;">
            <div class="container px-4 px-lg-5 h-100">
                <div class="row gx-3 gx-lg-5 h-100 align-items-center  text-start">
                    <div class="col-lg-8 align-self-end">
                        <h1 class="text-white font-weight-bold">Australia's Go To For Fashion & Entertainment Services</h1>
                        <hr class="w-100 mx-auto" style="border: none; height: 3px; background-color: #c99863;"/>
                    </div>
                    <div class="col-lg-8 align-self-baseline">
                        <p class="text-white-75 mb-5">Out with the old and in with the new, ChicCharm's diverse range of services are sure to assist your needs in makeup artistry, wig styling, fashion design and hairstyling </p>
                        <a class="btn btn-primary btn-xl" href="#about">Discover the World of ChicCharm</a>
                    </div>
                </div>
            </div>
        </header>
        <!-- About-->
        <section class="page-section bg-primary" id="about">
            <div class="container mb-5 px-4 px-lg-5">
                <div class="row gx-4 gx-lg-5 justify-content-center">
                    <div class="col-lg-8 text-center">
                        <h2 class="text-white mt-0 display-1">ChicCharm</h2>
                        <h4 class="text-white mt-0">A New Era</h4>
                        <hr class="divider divider-light" />
                        <p class="text-white-75 mb-4">ChicCharm is commited to providing a fresh experience to returning customers all while attracting new clients far and wide.
                            We are committed to providing the highest quality and expertise for your fashion shoots and theatre entertainments
                            Even so we still provide the same loved services that our customers adore With a new Business direction our services are only going to get wider!</p>
                        <hr class="divider divider-light" />
                    </div>
                    <div class="col-lg-8 d-flex justify-content-center">
                    <img src="<?= $this->Url->image('model.png')?>" alt="Image Description" style="width: 1000px; height: auto;">
                    </div>
                </div>
            </div>
            <div class="w-100 mb-5" style="height: 2px; background-color: #6c757d;"></div>
            <div class="container mb-4 px-4 px-lg-5">
                <div class="row gx-5 gx-lg-5 h-50 align-items-center text-start">
                    <div class="col-lg-9 text-start align-self-start order-lg-1">
                        <h2 class="text-white mt-0 display-1">Meet Michonne</h2>
                        <h4 class="text-white mt-0">The Owner and Visionary of ChicCharm's Future Creativity</h4>
                        <hr class="flex-grow-1 mx-auto" style="border: none; height: 3px; background-color: #c99863;"/>
                        <h5 class="text-white mt-0">"Creativity is what drives ChicCharm and that is what I am about"</h5>
                        <p class="text-white-75 mb-4">
                            Michonne had envisioned a future business that would take the fashion and entertainment industry by storm. <br>
                            Seeing that the fashion and entertainment industry had yet to take it's shape in Australia, Michonne had saw an opportunity. <br>
                            An opportunity to provide fashion and makeup services to cater for modelling and the entertainment industry <br>
                            Michonne believes that with enough dedication and a new make over of ChicCharm she can bring it onto the not just the local stage<br>
                            But the whole of Australia.
                        </p>
                        <hr class="flex-grow-1 mx-auto" style="border: none; height: 3px; background-color: #c99863;"/>
                    </div>
                    <div class="col-lg-3 mb-2 order-lg-2">
                        <img src="<?= $this->Url->image('Michonne.jpg')?>" alt="Image Description" style="width: auto; height: 500px;">
                    </div>
                </div>
            </div>
        </section>
        <!-- Services-->
        <section class="page-section" id="services">
            <div class="container px-4 px-lg-5">
                <h2 class="text-center mt-0">ChicCharm At Your Service</h2>
                <hr class="divider" />
                <div class="row gx-4 gx-lg-5">
                    <div class="col-lg-3 col-md-6 text-center">
                        <div class="mt-5">
                            <div class="mb-2"><i class="bi-gem fs-1 text-primary"></i></div>
                            <h3 class="h4 mb-2">Sturdy Themes</h3>
                            <p class="text-muted mb-0">Our themes are updated regularly to keep them bug free!</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 text-center">
                        <div class="mt-5">
                            <div class="mb-2"><i class="bi-laptop fs-1 text-primary"></i></div>
                            <h3 class="h4 mb-2">Up to Date</h3>
                            <p class="text-muted mb-0">All dependencies are kept current to keep things fresh.</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 text-center">
                        <div class="mt-5">
                            <div class="mb-2"><i class="bi-globe fs-1 text-primary"></i></div>
                            <h3 class="h4 mb-2">Ready to Publish</h3>
                            <p class="text-muted mb-0">You can use this design as is, or you can make changes!</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 text-center">
                        <div class="mt-5">
                            <div class="mb-2"><i class="bi-heart fs-1 text-primary"></i></div>
                            <h3 class="h4 mb-2">Made with Love</h3>
                            <p class="text-muted mb-0">Is it really open source if it's not made with love?</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Portfolio-->
        <div id="portfolio">
            <div class="container-fluid p-0">
                <div class="row g-0">
                    <div class="col-lg-4 col-sm-6">
                        <a class="portfolio-box" href="<?= $this->Url->image('portfolio/fullsize/1.jpg')?>" title="Project Name">
                            <img class="img-fluid" src="<?= $this->Url->image('portfolio/thumbnails/1.jpg')?>" alt="..." />
                            <div class="portfolio-box-caption">
                                <div class="project-category text-white-50">Category</div>
                                <div class="project-name">Project Name</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <a class="portfolio-box" href="<?= $this->Url->image('portfolio/fullsize/2.jpg')?>" title="Project Name">
                            <img class="img-fluid" src="<?= $this->Url->image('portfolio/thumbnails/2.jpg')?>" alt="..." />
                            <div class="portfolio-box-caption">
                                <div class="project-category text-white-50">Category</div>
                                <div class="project-name">Project Name</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <a class="portfolio-box" href="<?= $this->Url->image('portfolio/fullsize/3.jpg')?>" title="Project Name">
                            <img class="img-fluid" src="<?= $this->Url->image('portfolio/thumbnails/3.jpg')?>" alt="..." />
                            <div class="portfolio-box-caption">
                                <div class="project-category text-white-50">Category</div>
                                <div class="project-name">Project Name</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <a class="portfolio-box" href="<?= $this->Url->image('portfolio/fullsize/4.jpg')?>" title="Project Name">
                            <img class="img-fluid" src="<?= $this->Url->image('portfolio/thumbnails/4.jpg')?>" alt="..." />
                            <div class="portfolio-box-caption">
                                <div class="project-category text-white-50">Category</div>
                                <div class="project-name">Project Name</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <a class="portfolio-box" href="<?= $this->Url->image('portfolio/fullsize/5.jpg')?>" title="Project Name">
                            <img class="img-fluid" src="<?= $this->Url->image('portfolio/thumbnails/5.jpg')?>" alt="..." />
                            <div class="portfolio-box-caption">
                                <div class="project-category text-white-50">Category</div>
                                <div class="project-name">Project Name</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <a class="portfolio-box" href="<?= $this->Url->image('portfolio/fullsize/6.jpg')?>" title="Project Name">
                            <img class="img-fluid" src="<?= $this->Url->image('portfolio/thumbnails/6.jpg')?>" alt="..." />
                            <div class="portfolio-box-caption p-3">
                                <div class="project-category text-white-50">Category</div>
                                <div class="project-name">Project Name</div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Call to action-->
        <section class="page-section bg-dark text-white">
            <div class="container px-4 px-lg-5 text-center">
                <h2 class="mb-4">Free Download at Start Bootstrap!</h2>
                <a class="btn btn-light btn-xl" href="https://startbootstrap.com/theme/creative/">Download Now!</a>
            </div>
        </section>
        <!-- Contact TO DO REMOVE LATER-->
        <section class="page-section" id="contact">
        </section>
        <?= $this->Html->script('https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js') ?>
        <?= $this->Html->script('https://cdnjs.cloudflare.com/ajax/libs/SimpleLightbox/2.1.0/simpleLightbox.min.js') ?>
        <?= $this->Html->script('/landing-detail/js/scripts.js') ?>

    </body>
</html>
