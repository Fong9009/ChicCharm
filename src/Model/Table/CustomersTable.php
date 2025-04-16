<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Customers Model
 *
 * @method \App\Model\Entity\Customer newEmptyEntity()
 * @method \App\Model\Entity\Customer newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Customer> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Customer get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Customer findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Customer patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Customer> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Customer|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Customer saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Customer>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Customer>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Customer>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Customer> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Customer>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Customer>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Customer>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Customer> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class CustomersTable extends Table
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

        $this->setTable('customers');
        $this->setDisplayField('first_name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('Bookings', [
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
            ->scalar('first_name')
            ->maxLength('first_name', 255)
            ->requirePresence('first_name', 'create')
            ->notEmptyString('first_name')
            ->add('first_name', 'alphanumeric', [
                'rule' => ['custom', "/^[a-zA-Z' ]+$/"],
                'message' => 'First name must be alphanumeric.',
            ]);

        $validator
            ->scalar('last_name')
            ->maxLength('last_name', 255)
            ->requirePresence('last_name', 'create')
            ->notEmptyString('last_name')
            ->add('last_name', 'alphanumeric', [
                'rule' => ['custom', "/^[a-zA-Z' ]+$/"],
                'message' => 'Last name must be alphanumeric.',
            ]);


        $validator
            ->email('email')
            ->notEmptyString('email')
            ->add('email', 'unique', [
                'rule' => 'validateUnique',
                'provider' => 'table',
                'message' => 'This email is already in use.',
            ]);

        $validator
            ->scalar('password')
            ->maxLength('password', 255)
            ->requirePresence('password', 'create')
            ->notEmptyString('password')
            ->minLength('password', 8, 'Password must be at least 8 characters long');

        $validator
            ->scalar('password_confirm')
            ->maxLength('password_confirm', 255)
            ->requirePresence('password_confirm', 'create')
            ->notEmptyString('password_confirm')
            ->add('password_confirm', 'custom', [
                'rule' => function($value, $context) {
                    return isset($context['data']['password']) && $value === $context['data']['password'];
                },
                'message' => 'Passwords do not match'
            ]);

        $validator
            ->scalar('nonce')
            ->maxLength('nonce', 255)
            ->allowEmptyString('nonce');

        $validator
            ->dateTime('nonce_expiry')
            ->allowEmptyDateTime('nonce_expiry');

        $validator
            ->scalar('profile_picture')
            ->maxLength('profile_picture', 255)
            ->allowEmptyString('profile_picture');

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
        $rules->add($rules->isUnique(['email']), ['errorField' => 'email']);

        return $rules;
    }

    /**
     * Reset password validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationResetPassword(Validator $validator): Validator
    {
        $validator
            ->scalar('password')
            ->maxLength('password', 255)
            ->requirePresence('password', true)
            ->notEmptyString('password')
            ->minLength('password', 8, 'Password must be at least 8 characters long');

        $validator
            ->scalar('confirm_password')
            ->maxLength('confirm_password', 255)
            ->requirePresence('confirm_password', true)
            ->notEmptyString('confirm_password')
            ->add('confirm_password', 'custom', [
                'rule' => function($value, $context) {
                    return isset($context['data']['password']) && $value === $context['data']['password'];
                },
                'message' => 'Passwords do not match'
            ]);

        return $validator;
    }
}
