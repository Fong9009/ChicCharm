<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Booking Entity
 *
 * @property int $id
 * @property string|null $booking_name
 * @property \Cake\I18n\Date $booking_date
 * @property string $total_cost
 * @property string $remaining_cost
 * @property int|null $customer_id
 *
 * @property \App\Model\Entity\Customer $customer
 * @property \App\Model\Entity\Stylist[] $stylists
 */
class Booking extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'booking_name' => true,
        'booking_date' => true,
        'start_time' => true,
        'end_time' => true,
        'total_cost' => true,
        'remaining_cost' => true,
        'customer_id' => true,
        'customer' => true,
        'stylists' => true,
        'services' => true,
        'status' => true,
        'notes' => true,
    ];
}
