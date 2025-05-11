<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * PaymentHistories Model
 *
 * @property \App\Model\Table\BookingsTable&\Cake\ORM\Association\BelongsTo $Bookings
 * @property \App\Model\Table\CustomersTable&\Cake\ORM\Association\BelongsTo $Customers
 *
 * @method \App\Model\Entity\PaymentHistory newEmptyEntity()
 * @method \App\Model\Entity\PaymentHistory newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\PaymentHistory> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\PaymentHistory get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\PaymentHistory findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\PaymentHistory patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\PaymentHistory> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\PaymentHistory|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\PaymentHistory saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\PaymentHistory>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\PaymentHistory>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\PaymentHistory>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\PaymentHistory> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\PaymentHistory>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\PaymentHistory>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\PaymentHistory>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\PaymentHistory> deleteManyOrFail(iterable $entities, array $options = [])
 */
class PaymentHistoriesTable extends Table
{
    /**
     * Initialize method
     *
     * @param array<string, mixed> $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('payment_histories');
        $this->setDisplayField('paypal_transaction_id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Bookings', [
            'foreignKey' => 'booking_id',
        ]);
        $this->belongsTo('Customers', [
            'foreignKey' => 'customer_id',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('booking_id')
            ->allowEmptyString('booking_id');

        $validator
            ->integer('customer_id')
            ->allowEmptyString('customer_id');

        $validator
            ->scalar('paypal_transaction_id')
            ->maxLength('paypal_transaction_id', 255)
            ->requirePresence('paypal_transaction_id', 'create')
            ->notEmptyString('paypal_transaction_id');

        $validator
            ->scalar('paypal_payer_id')
            ->maxLength('paypal_payer_id', 255)
            ->allowEmptyString('paypal_payer_id');

        $validator
            ->decimal('payment_amount')
            ->requirePresence('payment_amount', 'create')
            ->notEmptyString('payment_amount');

        $validator
            ->scalar('payment_currency')
            ->maxLength('payment_currency', 3)
            ->requirePresence('payment_currency', 'create')
            ->notEmptyString('payment_currency');

        $validator
            ->scalar('payment_status')
            ->maxLength('payment_status', 50)
            ->requirePresence('payment_status', 'create')
            ->notEmptyString('payment_status');

        $validator
            ->scalar('payment_method')
            ->maxLength('payment_method', 50)
            ->allowEmptyString('payment_method');

        $validator
            ->dateTime('payment_date')
            ->notEmptyDateTime('payment_date');

        $validator
            ->scalar('notes')
            ->allowEmptyString('notes');

        $validator
            ->dateTime('created_at')
            ->allowEmptyDateTime('created_at');

        $validator
            ->dateTime('updated_at')
            ->allowEmptyDateTime('updated_at');

        $validator
            ->scalar('invoice_pdf')
            ->maxLength('invoice_pdf', 255)
            ->allowEmptyString('invoice_pdf');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['booking_id'], 'Bookings'), ['errorField' => 'booking_id']);
        $rules->add($rules->existsIn(['customer_id'], 'Customers'), ['errorField' => 'customer_id']);

        return $rules;
    }
}
