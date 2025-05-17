<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Stylist[] $stylists
 */
use Cake\Routing\Router;
$identity = $this->request->getAttribute('identity');
$bookingButton = $this->ContentBlock->text('booking-button');
// Set up the booking link based on user type
if ($identity) {
    if ($identity->type === 'customer') {
        $link = ['controller' => 'Bookings', 'action' => 'customerbooking'];
    } elseif ($identity->type === 'admin') {
        $link = ['controller' => 'Bookings', 'action' => 'adminbooking'];
    } elseif ($identity->type === 'stylist') {
        $link = ['controller' => 'Stylists', 'action' => 'dashboard'];
    } else {
        $link = ['controller' => 'Bookings', 'action' => 'guestbooking'];
    }
} else {
    $link = ['controller' => 'Bookings', 'action' => 'guestbooking'];
}
?>
<div style="background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url(<?= $this->Url->image('stylistbackground.jpg')?>) center center / cover no-repeat;">
    <div class="row p-2 justify-content-center">
        <div class="col-lg-8 mt-5 text-center">
            <h1 class="fw-bold text-white"><?= $this->ContentBlock->text('stylist-page-title'); ?></h1>
            <h2 class="text-white"> <?= $this->ContentBlock->text('stylist-page-desc'); ?></h2>
            <h3 class="text-white">Got someone in mind?</h3>
            <a> <?= $this->Html->link($bookingButton, $link, ['class' => 'btn btn-primary btn-xl', 'onclick' => 'handleBookingClick(event)', 'style' =>"background-color: orange"]) ?></a>
        </div>
    </div>
    <div class="row p-2 justify-content-center">
        <div class="col-lg-4 col-md-12 col-sm-12">
            <?= $this->Form->create(null, ['type' => 'get', 'class' => 'search-form']) ?>
            <div class="justify-content-center">
                <?= $this->Form->control('search', [
                    'label' => false,
                    'placeholder' => 'Search...',
                    'class' => 'form-control',
                    'value' => $this->request->getQuery('search'),
                ]) ?>
            </div>
            <?= $this->Form->end() ?>
        </div>
        <div class="col-12 text-center p-2">
            <h5 class="text-white"> Can't find who you are looking for? <a href="<?= $this->Url->build(['controller' => 'Contacts', 'action' => 'enquiry'])?>">
                    <span>Contact Us</span>
                </a></h5>
        </div>
    </div>
    <hr class="flex-grow-1 mx-auto" style="border: none; height: 3px; background-color: #c99863;"/>
</div>


<section class="px-5 mt-4">
    <?php
    $counter = 0;
    foreach ($stylists as $stylist):
        $imagePath = $stylist->profile_picture;
        if ($imagePath != null) {
            $imagePath = Router::url('/img/profile/' . $stylist->profile_picture, true);
        } else {
            $imagePath = Router::url('/img/profile/stylist-placeholder.jpg', true);
        }
        if ($counter % 2 === 0): ?>
            <div class="row justify-content-center">
        <?php endif; ?>

        <div class="col-lg-6 col-md-12 col-sm-12 mb-4">
            <div class="card h-100 fade-in-title service-border">
                <div class="row g-0 h-100">
                    <!-- Left side: Image -->
                    <div class="col-md-5">
                        <div class="position-relative w-100 h-100" style="min-height: 500px; background: url('<?= $imagePath ?>') center center / cover no-repeat;">
                            <div class="position-absolute bottom-0 start-0 end-0 text-center bg-dark bg-opacity-50 text-white p-2">
                                <strong>"<?= h($stylist->stylist_motto) ?>"</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Right side: Text -->
                    <div class="col-md-7 d-flex flex-column justify-content-center">
                        <div class="card-body">
                            <h2>Stylist Details</h2>
                            <hr style="border: none; height: 3px; background-color: #c99863;"/>
                            <h4><strong>Stylist Name:</strong> <?= h($stylist->first_name) . " " . h($stylist->last_name) ?></h4>
                            <button class="btn btn-primary btn-sm mb-2" style="background-color: orange" type="button" data-bs-toggle="collapse" data-bs-target="#stylistBio<?= $stylist->id ?>" aria-expanded="false" aria-controls="stylistBio<?= $stylist->id ?>" >
                                Show/Hide Bio
                            </button>
                            <div class="collapse" id="stylistBio<?= $stylist->id ?>">
                                <div class="card card-body mb-3">
                                    <h5><strong>Stylist Bio</strong></h5>
                                    <?= h($stylist->stylist_bio) ?>
                                </div>
                            </div>
                            <h5><strong>Services that <?= h($stylist->first_name)?> Offers: </strong></h5>
                            <?php foreach ($stylist->services as $service): ?>
                                <li style="font-family: Raleway, sans-serif;"><?= h($service->service_name) ?></li>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div class="card-footer" style="background-color: orange;"></div>
            </div>
        </div>
        <?php
        $counter++;
        if ($counter % 2 === 0): ?>
            </div>
        <?php endif;
    endforeach;

    // Close row if last row has less than 3 cards
    if ($counter % 2 !== 0): ?>
    <?php endif; ?>
    <?php if (!empty($stylists)) : ?>
        <?php if (count($stylists) > 6) : ?>
            <div class="paginator text-center p-2">
                <ul class="pagination justify-content-center">
                    <?= $this->Paginator->first('<<') ?>
                    <?= $this->Paginator->prev('Previous Page') ?>
                    <?= $this->Paginator->next('Next Page') ?>
                    <?= $this->Paginator->last('>>') ?>
                </ul>
                <p><?= $this->Paginator->counter(__('Page {{page}} of {{pages}}, showing {{current}} Services out of {{count}} total')) ?></p>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</section>
