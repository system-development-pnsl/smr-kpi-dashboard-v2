<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanySetting extends Model
{
    protected $fillable = [
        'hotel_name', 'hotel_name_km', 'tagline', 'star_rating', 'established_year', 'logo',
        'address', 'city', 'country', 'phone', 'email', 'website',
        'checkin_time', 'checkout_time', 'currency', 'timezone', 'vat_rate', 'total_rooms',
        'facebook', 'instagram', 'tripadvisor', 'booking_com',
    ];

    public static function instance(): self
    {
        return static::firstOrCreate(['id' => 1], ['hotel_name' => 'Sun & Moon Riverside Hotel']);
    }
}
