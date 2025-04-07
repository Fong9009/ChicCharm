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
        <?= $this->Html->css('https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css', ['rel' => 'stylesheet']); ?>
        <!-- SimpleLightbox plugin CSS-->
        <?= $this->Html->css('https://cdnjs.cloudflare.com/ajax/libs/SimpleLightbox/2.1.0/simpleLightbox.min.css', ['rel' => 'stylesheet']); ?>
        <?= $this->Html->charset() ?>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>
            <?= $this->fetch('title') ?>
        </title>
    </head>
    <body id="page-top" class="landing-page">
        <!-- Masthead-->
        <header class="masthead"
                style="background-image: url('<?= $this->Url->image('bg-mastheadv2.jpg')?>');
                    background-size: cover; background-position: center;"
        >
            <div class="container px-4 px-lg-5 h-100">
                <div class="row gx-3 gx-lg-5 h-100 align-items-center  text-start">
                    <div class="col-lg-8 align-self-end">
                        <h1 class="text-white font-weight-bold fade-in-title">
                            Australia's Go-To For Fashion & Entertainment Services
                        </h1>
                        <hr class="w-100 mx-auto" style="border: none; height: 3px; background-color: #c99863;"/>
                    </div>
                    <div class="col-lg-8 align-self-baseline">
                        <p class="text-white-75 mb-5 fade-in-para">
                            Out with the old and in with the new, ChicCharm's diverse range of services are sure to
                            assist your needs in makeup artistry, wig styling, fashion design and hairstyling
                        </p>
                        <a class="btn btn-primary btn-xl fade-in-para" href="#about">Discover the World of ChicCharm</a>
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

                        <p class="text-white-75 mb-4">
                            ChicCharm is commited to providing a fresh experience to returning
                            customers all while attracting new clients far and wide.
                            We are committed to providing the highest quality and expertise for your fashion shoots and
                            theatre entertainments
                            Even so we still provide the same loved services that our customers adore With a new
                            Business direction our services are only going to get wider!
                        </p>

                        <hr class="divider divider-light" />
                    </div>
                    <div class="col-lg-8 d-flex justify-content-center">
                    <img src="<?= $this->Url->image('model.jpg')?>" alt="Image Description" style="width: 1000px; height: auto;">
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
                            Michonne had envisioned a future business that would take the fashion and entertainment
                            industry by storm. <br>
                            Seeing that the fashion and entertainment industry had yet to take its shape in Australia,
                            Michonne had saw an opportunity. <br>
                            An opportunity to provide fashion and makeup services to cater for modelling and the
                            entertainment industry <br>
                            Michonne believes that with enough dedication and a new makeover of
                            ChicCharm she can bring it onto the not just the local stage<br>
                            But the whole of Australia.
                        </p>

                        <hr class="flex-grow-1 mx-auto" style="border: none; height: 3px; background-color: #c99863;"/>
                    </div>
                    <div class="col-lg-3 mb-2 order-lg-2">
                        <img
                            src="<?= $this->Url->image('Michonne.jpg')?>"
                            alt="Owner of ChicCharm Michonne" style="width: auto; height: 400px;"
                        >
                    </div>
                </div>
            </div>
        </section>
        <!-- Services-->
        <section class="page-section" id="services">
            <div class="container px-4 px-lg-5">
                <h2 class="text-center mt-0">ChicCharm At Your Service</h2>
                <hr class="divider" />
                <h3 class="text-center mt-0">ChicCharm is dedicated to provide your show with the finest of artisens who are masters of their craft.
                    They will make sure that your models or stars of the show will shine brighter than ever.</h3>
                <hr class="divider" />
                <div class="row gx-4 gx-lg-5">
                    <div class="col-lg-3 col-md-5 text-center">
                        <h4>Makeup Artistry</h4>
                        <p>ChicCharm can make sure your stars of the show shine.</p>
                        <hr class="flex-grow-1 mx-auto" style="border: none; height: 3px; background-color: #c99863;"/>
                    </div>
                    <div class="col-lg-3 col-md-5 text-center">
                        <h4>Wig Styling</h4>
                        <p>ChicCharm can design custom wigs for all your needs.</p>
                        <hr class="flex-grow-1 mx-auto" style="border: none; height: 3px; background-color: #c99863;"/>
                    </div>
                    <div class="col-lg-3 col-md-5 text-center">
                        <h4>Fashion Design</h4>
                        <p>ChicCharm can design custom clothes for all your theatre needs.</p>
                        <hr class="flex-grow-1 mx-auto" style="border: none; height: 3px; background-color: #c99863;"/>
                    </div>
                    <div class="col-lg-3 col-md-5 text-center">
                        <h4>Hair Styling</h4>
                        <p>ChicCharm can make sure your stars hairs are stunning and fabulous.</p>
                        <hr class="flex-grow-1 mx-auto" style="border: none; height: 3px; background-color: #c99863;"/>
                    </div>
                </div>
                <div class="row gx-4 gx-lg-5">
                    <div class="col-lg-3 col-md-5 text-center">
                        <img src="<?= $this->Url->image('services/makeup.jpg')?>" alt="Makeup Artistry" class="img-fluid">
                    </div>
                    <div class="col-lg-3 col-md-5 text-center">
                        <img src="<?= $this->Url->image('services/wigstyling.jpg')?>" alt="Wig Styling" class="img-fluid">
                    </div>
                    <div class="col-lg-3 col-md-5 text-center">
                        <img src="<?= $this->Url->image('services/fashiondesign.jpg')?>" alt="Fashion Design" class="img-fluid">
                    </div>
                    <div class="col-lg-3 col-md-5 text-center">
                        <img src="<?= $this->Url->image('services/hairstyling.jpg')?>" alt="Hair Styling" class="img-fluid">
                    </div>
                </div>
                <hr class="flex-grow-1 mx-auto" style="border: none; height: 3px; background-color: #c99863;"/>
                <div class="row gx-4 gx-lg-5">
                    <div class="d-flex justify-content-center mb-4">
                        <a class="btn btn-primary btn-xl" href="#about">Make a Booking with ChicCharm</a>
                    </div>
                </div>
            </div>
            <div class="container px-4 px-lg-5">
                <div class="row gx-4 gx-lg-5">
                    <h2 class="text-center mt-0">Some of ChicCharms latest works</h2>
                    <hr class="divider" />
                    <h3 class="text-center mt-0">ChicCharm is dedicated to helping make sure that your show is ready on the stage or a model show,
                    Our Business is nothing without our fabulous customers who continue to work with us</h3>
                    <hr class="divider" />
                </div>
            </div>
        </section>
        <!-- Portfolio-->
        <div id="portfolio">
            <div class="container-fluid p-0">
                <div class="row g-0">
                    <!--Hair Styling Image-->
                    <div class="col-lg-4 col-sm-6">
                        <a class="portfolio-box"
                           href="<?= $this->Url->image('portfolio/fullsize/hair.jpg')?>"
                           title="Hair Styling"
                        >
                            <img class="img-fluid w-100"
                                 src="<?= $this->Url->image('portfolio/thumbnails/hair.jpg')?>"
                                 alt="Smaller Version of Hairstyling"
                            />
                            <div class="portfolio-box-caption">
                                <div class="project-category text-white-50">Hair Styling</div>
                                <div class="project-name">Styling for next show</div>
                            </div>
                        </a>
                    </div>
                    <!--Make Up Image-->
                    <div class="col-lg-4 col-sm-6">
                        <a class="portfolio-box"
                           href="<?= $this->Url->image('portfolio/fullsize/make.jpg')?>"
                           title="Makeup"
                        >
                            <img class="img-fluid w-100"
                                 src="<?= $this->Url->image('portfolio/thumbnails/make.jpg')?>"
                                 alt="Makeup artist"
                            />
                            <div class="portfolio-box-caption">
                                <div class="project-category text-white-50">Makeup</div>
                                <div class="project-name">Makeup for next show</div>
                            </div>
                        </a>
                    </div>
                    <!--Stage Image-->
                    <div class="col-lg-4 col-sm-6">
                        <a class="portfolio-box"
                           href="<?= $this->Url->image('portfolio/fullsize/stage.jpg')?>"
                           title="Stage Show"
                        >
                            <img class="img-fluid w-100"
                                 src="<?= $this->Url->image('portfolio/thumbnails/stage.jpg')?>"
                                 alt="Stage Show Dance"
                            />
                            <div class="portfolio-box-caption">
                                <div class="project-category text-white-50">Stage Shows</div>
                                <div class="project-name">Stage show of dance</div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
