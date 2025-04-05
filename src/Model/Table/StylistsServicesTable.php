<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * StylistsServices Model
 *
 * @property \App\Model\Table\StylistsTable&\Cake\ORM\Association\BelongsTo $Stylists
 * @property \App\Model\Table\ServicesTable&\Cake\ORM\Association\BelongsTo $Services
 *
 * @method \App\Model\Entity\StylistsService newEmptyEntity()
 * @method \App\Model\Entity\StylistsService newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\StylistsService> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\StylistsService get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\StylistsService findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\StylistsService patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\StylistsService> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\StylistsService|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\StylistsService saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\StylistsService>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\StylistsService>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\StylistsService>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\StylistsService> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\StylistsService>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\StylistsService>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\StylistsService>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\StylistsService> deleteManyOrFail(iterable $entities, array $options = [])
 */
class StylistsServicesTable extends Table
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

        $this->setTable('stylists_services');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Stylists', [
            'foreignKey' => 'stylist_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Services', [
            'foreignKey' => 'service_id',
            'joinType' => 'INNER',
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
            ->integer('stylist_id')
            ->notEmptyString('stylist_id');

        $validator
            ->integer('service_id')
            ->notEmptyString('service_id');

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
        $rules->add($rules->existsIn(['stylist_id'], 'Stylists'), ['errorField' => 'stylist_id']);
        $rules->add($rules->existsIn(['service_id'], 'Services'), ['errorField' => 'service_id']);

        return $rules;
    }
}
