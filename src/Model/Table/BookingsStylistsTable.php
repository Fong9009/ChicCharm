<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * BookingsStylists Model
 *
 * @property \App\Model\Table\BookingsTable&\Cake\ORM\Association\BelongsTo $Bookings
 * @property \App\Model\Table\StylistsTable&\Cake\ORM\Association\BelongsTo $Stylists
 *
 * @method \App\Model\Entity\BookingsStylist newEmptyEntity()
 * @method \App\Model\Entity\BookingsStylist newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\BookingsStylist> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\BookingsStylist get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\BookingsStylist findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\BookingsStylist patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\BookingsStylist> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\BookingsStylist|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\BookingsStylist saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\BookingsStylist>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\BookingsStylist>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\BookingsStylist>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\BookingsStylist> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\BookingsStylist>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\BookingsStylist>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\BookingsStylist>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\BookingsStylist> deleteManyOrFail(iterable $entities, array $options = [])
 */
class BookingsStylistsTable extends Table
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

        $this->setTable('bookings_stylists');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Bookings', [
            'foreignKey' => 'booking_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Stylists', [
            'foreignKey' => 'stylist_id',
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
            ->time('start_time')
            ->requirePresence('start_time', 'create')
            ->notEmptyTime('start_time');

        $validator
            ->time('end_time')
            ->requirePresence('end_time', 'create')
            ->notEmptyTime('end_time');

        $validator
            ->decimal('selected_cost')
            ->requirePresence('selected_cost', 'create')
            ->notEmptyString('selected_cost');

        $validator
            ->integer('booking_id')
            ->notEmptyString('booking_id');

        $validator
            ->integer('stylist_id')
            ->notEmptyString('stylist_id');

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
        $rules->add($rules->existsIn(['stylist_id'], 'Stylists'), ['errorField' => 'stylist_id']);

        return $rules;
    }
}
