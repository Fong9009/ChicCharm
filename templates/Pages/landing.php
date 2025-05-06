<?php
$this->layout = 'publiclayout';
$identity = $this->request->getAttribute('identity');
$bookingButton = $this->ContentBlock->text('booking-button');
// Set up the booking link based on user type
if ($identity) {
    if ($identity->type === 'customer') {
        $link = ['controller' => 'Bookings', 'action' => 'customerbooking'];
    } elseif ($identity->type === 'admin') {
        $link = ['controller' => 'Bookings', 'action' => 'adminbooking'];
    } elseif ($identity->type === 'stylist') {
        $link = ['controller' => 'Stylist', 'action' => 'Dashboard'];
    } else {
        $link = ['controller' => 'Bookings', 'action' => 'guestbooking'];
    }
} else {
    $link = ['controller' => 'Bookings', 'action' => 'guestbooking'];
}
$message = '';

//Used for removing the extra img src tag we do not need
function extractImageSrc($contentBlock, $key) {
    $imgTag = $contentBlock->image($key);
    preg_match('/src="([^"]+)"/', $imgTag, $matches);

    return $matches[1] ?? '';
}

//Image for Masthead
$imgMasthead = extractImageSrc($this->ContentBlock, 'photo-masthead');
$imgMeet = extractImageSrc($this->ContentBlock, 'photo-meet');
//Images for the Services Carousel
$imgSrc1 = extractImageSrc($this->ContentBlock, 'photo-carousel-1');
$imgSrc2 = extractImageSrc($this->ContentBlock, 'photo-carousel-2');
$imgSrc3 = extractImageSrc($this->ContentBlock, 'photo-carousel-3');
$imgSrc4 = extractImageSrc($this->ContentBlock, 'photo-carousel-4');

//Images for Portfolio
$imgPort1 = extractImageSrc($this->ContentBlock, 'portfolio-1');
$imgPort2 = extractImageSrc($this->ContentBlock, 'portfolio-2');
$imgPort3 = extractImageSrc($this->ContentBlock, 'portfolio-3');
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
    <body id="page-top">
        <!-- Masthead-->
        <header class="masthead"
                style="background-image: url('<?= $imgMasthead?>');
                    background-size: cover; background-position: center;"
        >
            <div class="container px-4 px-lg-5 h-100">
                <div class="row gx-3 gx-lg-5 h-100 align-items-center text-start text-md-start">
                    <div class="col-lg-8 align-self-end">
                        <h1 class="text-white font-weight-bold fade-in-title text-center text-md-start">
                            <?= $this->ContentBlock->text('web-title'); ?>
                        </h1>
                        <hr class="w-100 mx-auto" style="border: none; height: 3px; background-color: #c99863;"/>
                    </div>
                    <div class="col-lg-8 align-self-baseline">
                        <p class="text-white-75 mb-5 fade-in-para text-center text-md-start">
                            <?= $this->ContentBlock->text('title-catch'); ?>
                        </p>
                        <div class="text-center text-md-start">
                            <?php if ($message): ?>
                                <div class="alert alert-info mb-3"><?= h($message) ?></div>
                            <?php endif; ?>
                            <a> <?= $this->Html->link($bookingButton, $link, ['class' => 'btn btn-primary btn-xl', 'onclick' => 'handleBookingClick(event)']) ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <!-- About-->
        <section class="page-section bg-primary" id="about">
            <div class="container mb-5 px-4 px-lg-5">
                <div class="row gx-4 gx-lg-5 justify-content-center">
                    <div class="col-lg-8 text-center">
                        <h2 class="text-white mt-0 display-1"><?= $this->ContentBlock->text('about-chiccharm-title'); ?></h2>
                        <h4 class="text-white mt-0"><?= $this->ContentBlock->text('about-catch'); ?></h4>
                        <hr class="divider divider-light" />
                        <div class="text-white-75 fs-5 mb-4">
                            <?= $this->ContentBlock->text('about-desc'); ?>
                        </div>
                        <hr class="divider divider-light" />
                    </div>
                    <div class="col-lg-8 d-flex justify-content-center mb-xl-5">
                        <div style="max-width: 950px; max-height: 520px; overflow: hidden;">
                            <?= $this->ContentBlock->image('photo-about', [
                                'style' => 'max-width: 100%; max-height: 520px; width: auto; height: auto; object-fit: contain;',
                                'class' => 'img-fluid'
                            ]); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="w-100 mb-5" style="height: 2px; background-color: #6c757d;"></div>
            <div class="container mb-4 px-4 px-lg-5">
                <div class="row gx-5 gx-lg-5 h-50 align-items-center text-start px-lg-5">
                    <div class="col-lg-9 text-start align-self-start order-lg-1">
                        <h2 class="text-white mt-0 display-1"><?= $this->ContentBlock->text('owner-title-text'); ?></h2>
                        <h4 class="text-white mt-0"><?= $this->ContentBlock->text('desc-owner'); ?></h4>
                        <hr class="flex-grow-1 mx-auto" style="border: none; height: 3px; background-color: #c99863;"/>
                        <h5 class="text-white mt-0"><?= $this->ContentBlock->text('owner-quote'); ?></h5>
                        <div class="text-white-75 fs-5 mb-4">
                            <?= $this->ContentBlock->text('vision-statement'); ?>
                        </div>
                        <hr class="flex-grow-1 mx-auto" style="border: none; height: 3px; background-color: #c99863;"/>
                    </div>
                    <div class="col-lg-3 mb-2 order-lg-2">
                        <img
                            src="<?=$imgMeet?>"
                            alt="Owner of ChicCharm Michonne"
                            class="img-fluid mx-auto d-block"
                            style="width: 100%; max-width: 400px; height: auto; object-fit: cover; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);"
                        >
                    </div>
                </div>
            </div>
            <div class="w-100 mb-5" style="height: 2px; background-color: #6c757d;"></div>
        </section>

        <!-- Services Carousel -->
        <section id="services">
            <div id="ChiccharmCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner mb-4">
                    <div class="carousel-item active">
                        <div class="page-header d-flex align-items-center"
                             style="height: 100vh;
                                 background-image: url('<?=$imgSrc1?>');
                                 background-size: cover;
                                 background-position: center;">
                            <span class="mask bg-gradient-dark position-absolute w-100 h-100 top-0 start-0"></span>
                            <div class="container ps-5 me-5">
                                <div class="row">
                                    <div class="col-lg-6 my-auto text-white">
                                        <div style="background-color: rgba(0, 0, 0, 0.5); padding: 20px; border-radius: 10px;">
                                            <h1 class="text-white font-weight-bold fade-in-title text-center text-md-start">
                                                <?= $this->ContentBlock->text('service-title'); ?>
                                            </h1>
                                            <h2 class="text-white font-weight-bold fade-in-title text-center text-md-start">
                                                <?= $this->ContentBlock->text('service-desc'); ?>
                                            </h2>
                                            <h1 class="text-white font-weight-bold fade-in-title text-center text-md-start">
                                                <?= $this->ContentBlock->text('service-one-title'); ?>
                                            </h1>
                                            <hr class="w-100 mx-auto" style="border: none; height: 3px; background-color: #c99863;"/>
                                            <h1 class="text-white fade-in-title">The Finest of Wig Stylists</h1>
                                            <p class="lead opacity-8 fade-in-title"><?= $this->ContentBlock->text('service-one-desc'); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <div class="page-header d-flex align-items-center"
                             style="height: 100vh;
                                 background-image: url('<?=$imgSrc2?>');
                                 background-size: cover;
                                 background-position: center;">
                            <span class="mask bg-gradient-dark position-absolute w-100 h-100 top-0 start-0"></span>
                            <div class="container ps-5 me-5">
                                <div class="row">
                                    <div class="col-lg-6 my-auto text-white">
                                        <div style="background-color: rgba(0, 0, 0, 0.5); padding: 20px; border-radius: 10px;">
                                            <h1 class="text-white font-weight-bold fade-in-title text-center text-md-start">
                                                <?= $this->ContentBlock->text('service-title'); ?>
                                            </h1>
                                            <h2 class="text-white font-weight-bold fade-in-title text-center text-md-start">
                                                <?= $this->ContentBlock->text('service-desc'); ?>
                                            </h2>
                                            <h1 class="text-white font-weight-bold fade-in-title text-center text-md-start">
                                                <?= $this->ContentBlock->text('service-two-title'); ?>
                                            </h1>
                                            <hr class="w-100 mx-auto" style="border: none; height: 3px; background-color: #c99863;"/>
                                            <h1 class="text-white fade-in-title">The Finest of Wig Stylists</h1>
                                            <p class="lead opacity-8 fade-in-title"><?= $this->ContentBlock->text('service-two-desc'); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <div class="page-header d-flex align-items-center"
                             style="height: 100vh;
                                 background-image: url('<?=$imgSrc3?>');
                                 background-size: cover;
                                 background-position: center;">
                            <span class="mask bg-gradient-dark position-absolute w-100 h-100 top-0 start-0"></span>
                            <div class="container ps-5 me-5">
                                <div class="row">
                                    <div class="col-lg-6 my-auto text-white">
                                        <div style="background-color: rgba(0, 0, 0, 0.5); padding: 20px; border-radius: 10px;">
                                            <h1 class="text-white font-weight-bold fade-in-title text-center text-md-start">
                                                <?= $this->ContentBlock->text('service-title'); ?>
                                            </h1>
                                            <h2 class="text-white font-weight-bold fade-in-title text-center text-md-start">
                                                <?= $this->ContentBlock->text('service-desc'); ?>
                                            </h2>
                                            <h1 class="text-white font-weight-bold fade-in-title text-center text-md-start">
                                                <?= $this->ContentBlock->text('service-three-title'); ?>
                                            </h1>
                                            <hr class="w-100 mx-auto" style="border: none; height: 3px; background-color: #c99863;"/>
                                            <h1 class="text-white fade-in-title">The Finest of dressmakers</h1>
                                            <p class="lead opacity-8 fade-in-title"><?= $this->ContentBlock->text('service-three-desc'); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <div class="page-header d-flex align-items-center"
                             style="height: 100vh;
                                 background-image: url('<?=$imgSrc4?>');
                                 background-size: cover;
                                 background-position: center;">
                            <span class="mask bg-gradient-dark position-absolute w-100 h-100 top-0 start-0"></span>
                            <div class="container ps-5 me-5">
                                <div class="row">
                                    <div class="col-lg-6 my-auto text-white">
                                        <div style="background-color: rgba(0, 0, 0, 0.5); padding: 20px; border-radius: 10px;">
                                            <h1 class="text-white font-weight-bold fade-in-title text-center text-md-start">
                                                <?= $this->ContentBlock->text('service-title'); ?>
                                            </h1>
                                            <h2 class="text-white font-weight-bold fade-in-title text-center text-md-start">
                                                <?= $this->ContentBlock->text('service-desc'); ?>
                                            </h2>
                                            <h1 class="text-white font-weight-bold fade-in-title text-center text-md-start">
                                                <?= $this->ContentBlock->text('service-four-title'); ?>
                                            </h1>
                                            <hr class="w-100 mx-auto" style="border: none; height: 3px; background-color: #c99863;"/>
                                            <h1 class="text-white fade-in-title">The Finest of Wig Stylists</h1>
                                            <p class="lead opacity-8 fade-in-title"><?= $this->ContentBlock->text('service-four-desc'); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="position-absolute top-50 start-0 translate-middle-y w-100 d-flex justify-content-between px-4">
                    <a class="carousel-control-prev gap-4" href="#ChiccharmCarousel" role="button" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true" style="background-color: rgba(0, 0, 0, 0.6);"></span>
                        <span class="visually-hidden">Previous</span>
                    </a>
                    <a class="carousel-control-next" href="#ChiccharmCarousel" role="button" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true" style="background-color: rgba(0, 0, 0, 0.6);"></span>
                        <span class="visually-hidden">Next</span>
                    </a>
                </div>
                <div class="carousel-indicators">
                    <button type="button" data-bs-target="#ChiccharmCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                    <button type="button" data-bs-target="#ChiccharmCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
                    <button type="button" data-bs-target="#ChiccharmCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
                    <button type="button" data-bs-target="#ChiccharmCarousel" data-bs-slide-to="3" aria-label="Slide 4"></button>
                </div>
            </div>
        </section>
        <section>
            <div>
                <hr class="flex-grow-1 mx-auto" style="border: none; height: 3px; background-color: #c99863;"/>
                <div class="row gx-4 gx-lg-5">
                    <div class="d-flex justify-content-center mb-4">
                        <?php if (isset($message) && $message): ?>
                            <div class="alert alert-info mb-3"><?= h($message) ?></div>
                        <?php endif; ?>
                        <?= $this->Html->link($bookingButton, $link, ['class' => 'btn btn-primary btn-xl', 'onclick' => 'handleBookingClick(event)']) ?>
                    </div>
                </div>
            </div>
            <div class="container px-4 px-lg-5">
                <div class="row gx-4 gx-lg-5">
                    <h2 class="text-center mt-0"><?= $this->ContentBlock->text('past-work-title'); ?></h2>
                    <hr class="divider" />
                    <h3 class="text-center mt-0"><?= $this->ContentBlock->text('past-text'); ?></h3>
                    <hr class="divider" />
                </div>
            </div>
        </section>
        <!-- Portfolio-->
        <div id="portfolio">
            <div class="container-fluid">
                <div class="row g-0 justify-content-center">
                    <!--Hair Styling Image-->
                    <div class="col-12 col-sm-6 col-lg-4">
                        <div class="portfolio-box">
                            <a href="<?=$imgPort1?>"
                               title="Hair Styling"
                               class="d-block"
                            >
                                <img src="<?=$imgPort1?>"
                                     alt="Smaller Version of Hairstyling"
                                     class="img-fluid w-100"
                                     style="aspect-ratio: 4/3; object-fit: cover;"
                                />
                                <div class="portfolio-box-caption">
                                    <div class="project-category text-white-50">Hair Styling</div>
                                    <div class="project-name">Styling for next show</div>
                                </div>
                            </a>
                        </div>
                    </div>
                    <!--Make Up Image-->
                    <div class="col-12 col-sm-6 col-lg-4">
                        <div class="portfolio-box">
                            <a href="<?=$imgPort2?>"
                               title="Makeup"
                               class="d-block"
                            >
                                <img src="<?=$imgPort2?>"
                                     alt="Makeup artist"
                                     class="img-fluid w-100"
                                     style="aspect-ratio: 4/3; object-fit: cover;"
                                />
                                <div class="portfolio-box-caption">
                                    <div class="project-category text-white-50">Makeup</div>
                                    <div class="project-name">Makeup for next show</div>
                                </div>
                            </a>
                        </div>
                    </div>
                    <!--Stage Image-->
                    <div class="col-12 col-sm-6 col-lg-4">
                        <div class="portfolio-box">
                            <a href="<?=$imgPort3?>"
                               title="Stage Show"
                               class="d-block"
                            >
                                <img src="<?=$imgPort3?>"
                                     alt="Stage Show Dance"
                                     class="img-fluid w-100"
                                     style="aspect-ratio: 4/3; object-fit: cover;"
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
        </div>
    </body>
</html>
