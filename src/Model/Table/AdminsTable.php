<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Admins Model
 *
 * @method \App\Model\Entity\Admin newEmptyEntity()
 * @method \App\Model\Entity\Admin newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\Admin> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Admin get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\Admin findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\Admin patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\Admin> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Admin|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\Admin saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\Admin>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Admin>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Admin>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Admin> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Admin>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Admin>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\Admin>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\Admin> deleteManyOrFail(iterable $entities, array $options = [])
 */
class AdminsTable extends Table
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

        $this->setTable('admins');
        $this->setDisplayField('first_name');
        $this->setPrimaryKey('id');
        $this->addBehavior('CanAuthenticate');

        $this->addBehavior('Timestamp');
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
            ->notEmptyString('first_name');

        $validator
            ->scalar('last_name')
            ->maxLength('last_name', 255)
            ->requirePresence('last_name', 'create')
            ->notEmptyString('last_name');

        $validator
            ->email('email')
            ->notEmptyString('email', 'Email is required')
            ->add('email', 'unique', [
                'rule' => 'validateUnique',
                'provider' => 'table',
                'message' => 'This email is already in use.'
            ]);

        $validator
            ->scalar('password')
            ->maxLength('password', 255)
            ->requirePresence('password', 'create')
            ->notEmptyString('password')
            ->minLength('password', 8, 'Password must be at least 8 characters long')
            ->add('password', 'custom', [
                'rule' => function($value) {
                    $hasUppercase = preg_match('/[A-Z]/', $value);
                    $hasLowercase = preg_match('/[a-z]/', $value);
                    $hasNumber = preg_match('/[0-9]/', $value);
                    $hasSpecialChar = preg_match('/[^A-Za-z0-9]/', $value);
                    
                    return $hasUppercase && $hasLowercase && $hasNumber && $hasSpecialChar;
                },
                'message' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character'
            ]);

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
            ->scalar('avatar')
            ->maxLength('avatar', 255)
            ->allowEmptyString('avatar');

        $validator
            ->scalar('nonce')
            ->maxLength('nonce', 128)
            ->allowEmptyString('nonce');

        $validator
            ->scalar('nonce_expiry')
            ->maxLength('nonce_expiry', 128)
            ->allowEmptyString('nonce_expiry');

        $validator
            ->scalar('profile_picture')
            ->maxLength('profile_picture', 255)
            ->allowEmptyString('profile_picture');

        return $validator;
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
            ->minLength('password', 8, 'Password must be at least 8 characters long')
            ->add('password', 'custom', [
                'rule' => function($value) {
                    $hasUppercase = preg_match('/[A-Z]/', $value);
                    $hasLowercase = preg_match('/[a-z]/', $value);
                    $hasNumber = preg_match('/[0-9]/', $value);
                    $hasSpecialChar = preg_match('/[^A-Za-z0-9]/', $value);
                    
                    return $hasUppercase && $hasLowercase && $hasNumber && $hasSpecialChar;
                },
                'message' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character'
            ]);

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
