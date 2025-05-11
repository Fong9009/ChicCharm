<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class PaypalWebhookEventsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('paypal_webhook_events');
        $this->setDisplayField('event_type');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar('event_type')
            ->maxLength('event_type', 100)
            ->notEmptyString('event_type');

        $validator
            ->scalar('resource_type')
            ->maxLength('resource_type', 100)
            ->allowEmptyString('resource_type');

        $validator
            ->scalar('resource_id')
            ->maxLength('resource_id', 255)
            ->allowEmptyString('resource_id');

        $validator
            ->decimal('amount')
            ->allowEmptyString('amount');

        $validator
            ->scalar('currency')
            ->maxLength('currency', 3)
            ->allowEmptyString('currency');

        $validator
            ->scalar('status')
            ->maxLength('status', 50)
            ->allowEmptyString('status');

        $validator
            ->email('payer_email')
            ->allowEmptyString('payer_email');

        $validator
            ->scalar('payer_name')
            ->maxLength('payer_name', 255)
            ->allowEmptyString('payer_name');

        return $validator;
    }
} 