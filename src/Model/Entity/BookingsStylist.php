<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * BookingsStylist Entity
 *
 * @property int $id
 * @property \Cake\I18n\Date $stylist_date
 * @property \Cake\I18n\Time $start_time
 * @property \Cake\I18n\Time $end_time
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
        'stylist_date' => true,
        'start_time' => true,
        'end_time' => true,
        'selected_cost' => true,
        'booking_id' => true,
        'stylist_id' => true,
        'booking' => true,
        'stylist' => true,
    ];
}
