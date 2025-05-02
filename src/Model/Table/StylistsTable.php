<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Stylists Model
 *
 * @property \App\Model\Table\BookingsTable&\Cake\ORM\Association\BelongsToMany $Bookings
 * @property \App\Model\Table\ServicesTable&\Cake\ORM\Association\BelongsToMany $Services
 *
 * @method \App\Model\Entity\Stylist newEmptyEntity()
 * @method \App\Model\Entity\Stylist newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Stylist> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Stylist get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Stylist findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Stylist patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Stylist> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Stylist|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Stylist saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Stylist>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Stylist>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Stylist>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Stylist> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Stylist>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Stylist>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Stylist>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Stylist> deleteManyOrFail(iterable $entities, array $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class StylistsTable extends Table
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

        $this->setTable('stylists');
        $this->setDisplayField('first_name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsToMany('Bookings', [
            'foreignKey' => 'stylist_id',
            'targetForeignKey' => 'booking_id',
            'joinTable' => 'bookings_stylists',
        ]);
        $this->belongsToMany('Services', [
            'foreignKey' => 'stylist_id',
            'targetForeignKey' => 'service_id',
            'joinTable' => 'stylists_services',
        ]);

        $this->hasMany('BookingsStylists', [
            'foreignKey' => 'stylist_id',
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
            ->add('first_name', 'alphabetic', [
                'rule' => ['custom', "/^[a-zA-Z' ]+$/"],
                'message' => 'First name must be alphabetic.',
            ]);

        $validator
            ->scalar('last_name')
            ->maxLength('last_name', 255)
            ->requirePresence('last_name', 'create')
            ->notEmptyString('last_name')
            ->add('last_name', 'alphabetic', [
                'rule' => ['custom', "/^[a-zA-Z' ]+$/"],
                'message' => 'Last name must be alphabetic.',
            ]);

        $validator
            ->email('email')
            ->requirePresence('email', 'create')
            ->notEmptyString('email')
            ->add('email', 'unique', [
                'rule' => 'validateUnique',
                'provider' => 'table',
                'message' => 'This email is already in use.',
            ])
            ->add('email', 'mxRecord', [
                'rule' => function ($value, $context) {
                    if (empty($value) || !is_string($value) || strpos($value, '@') === false) {
                        return false;
                    }
                    $domain = substr(strrchr($value, "@"), 1);
                    if ($domain === false || empty($domain)) {
                        return false;
                    }
                    return checkdnsrr($domain . '.', 'MX');
                },
                'message' => 'The email domain does not appear valid (e.g., must be like @gmail.com or @outlook.com).'
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
            ->dateTime('nonce')
            ->allowEmptyDateTime('nonce');

        $validator
            ->dateTime('nonce_expiry')
            ->allowEmptyDateTime('nonce_expiry');

        $validator
            ->scalar('type')
            ->maxLength('type', 50)
            ->notEmptyString('type');

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
