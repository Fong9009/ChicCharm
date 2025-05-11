<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class PaypalWebhookEvent extends Entity
{
    protected array $_accessible = [
        'event_type' => true,
        'resource_type' => true,
        'resource_id' => true,
        'amount' => true,
        'currency' => true,
        'status' => true,
        'payer_email' => true,
        'payer_name' => true,
        'created' => true,
        'raw_data' => true,
    ];
} 