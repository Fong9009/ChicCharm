<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * BookingsStylist Entity
 *
 * @property int $id
 * @property int $duration
 * @property string $selected_cost
 * @property int $booking_id
 * @property int $stylist_id
 *
 * @property \App\Model\Entity\Booking $booking
 * @property \App\Model\Entity\Stylist $stylist
 */
class BookingsStylist extends Entity
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
        'duration' => true,
        'selected_cost' => true,
        'booking_id' => true,
        'stylist_id' => true,
        'booking' => true,
        'stylist' => true,
    ];
}
