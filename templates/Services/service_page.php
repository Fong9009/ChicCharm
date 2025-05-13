<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Service[] $services
 */
use Cake\Routing\Router;
?>
<section class="px-5 mt-4">
    <div class="row p-2 justify-content-center">
        <div class="col-12 text-center">
            <h1 class="fw-bold"><?= $this->ContentBlock->text('service-page-title'); ?></h1>
            <h2> <?= $this->ContentBlock->text('service-page-description'); ?></h2>
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
            <h5> Can't find what you are looking for? <a href="<?= $this->Url->build(['controller' => 'Contacts', 'action' => 'enquiry'])?>">
                    <span>Contact Us</span>
                </a></h5>
        </div>
    </div>
    <hr class="flex-grow-1 mx-auto" style="border: none; height: 3px; background-color: #c99863;"/>
    <?php
    $counter = 0;
    foreach ($services as $service):
        $imagePath = $service->service_image;
        if ($imagePath != null) {
           $imagePath = Router::url('/img/service/' . $service->service_image, true);
        } else {
            $imagePath = Router::url('/img/service/service-placeholder.jpg', true);
        }
        if ($counter % 3 === 0): ?>
            <div class="row justify-content-center">
        <?php endif; ?>

        <div class="col-lg-4 mb-4">
            <div class="card h-100 fade-in-title">
                <div class="card-header d-flex justify-content-between align-items-center"
                     style="background: url('<?= $imagePath ?>') center center / cover no-repeat; height: 150px;">
                    <div>
                        <h4 class="admin-card-h4 text-white bg-dark bg-opacity-50 p-1 rounded"><strong><?= h($service->service_name) ?></strong></h4>
                    </div>
                </div>
                <div class="card-body">
                    <p><strong>Service Description: </strong><?= h($service->service_desc) ?></p>
                    <p><strong>Cost:</strong> $<?= h($service->service_cost) ?></p>
                </div>
                <div class="card-footer" style="background-color: orange">
                    <?= $this->Html->link('View Service', ['controller' => 'Services', 'action' => 'serviceView', $service->id], ['class' => 'btn btn-primary w-100', 'style' =>"background-color: orange"]) ?>
                </div>
            </div>
        </div>

        <?php
        $counter++;
        if ($counter % 3 === 0): ?>
            </div>
        <?php endif;
    endforeach;

    // Close row if last row has less than 3 cards
    if ($counter % 3 !== 0): ?>
        </div>
    <?php endif; ?>
    <div class="paginator text-center p-2">
        <ul class="pagination justify-content-center">
            <?= $this->Paginator->first('<<') ?>
            <?= $this->Paginator->prev('<') ?>
            <?= $this->Paginator->next('>') ?>
            <?= $this->Paginator->last('>>') ?>
        </ul>
        <p><?= $this->Paginator->counter(__('Page {{page}} of {{pages}}, showing {{current}} Services out of {{count}} total')) ?></p>
    </div>
</section>
