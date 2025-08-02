<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUid;

class LoyaltyCard extends Model
{
    use HasUid;

    protected $fillable = [
        'uid',
        'type',
        'card_no',
        'slot',
        'is_compleated',
        'status',
    ];

    public static function getDiscount($loyaltCardNo)
    {
        $item = self::where('card_no', $loyaltCardNo)->where('status', true)->first();

        if(!$item) {
            return 0;
        }

        $slot = (int)$item->slot + 1;

        if($item->type == 'student') {
            return self::getStudentDiscountPlaces($slot);
        } else {
            return self::getProfessionalDiscountPlaces($slot);
        }
    }

    public static function getStudentDiscountPlaces($slot)
    {
        $items = [
            1 => 0,
            2 => 0,
            3 => 10,
            4 => 0,
            5 => 0,
            6 => 10,
            7 => 0,
            8 => 0,
            9 => 15,
            10 => 0,
            11 => 0,
            12 => 10
        ];

        return $items[$slot];
    }

    public static function getProfessionalDiscountPlaces($slot)
    {
        $items = [
            1 => 0,
            2 => 0,
            3 => 10,
            4 => 0,
            5 => 0,
            6 => 15,
            7 => 0,
            8 => 0,
            9 => 20,
            10 => 0,
            11 => 0,
            12 => 10
        ];

        return $items[$slot];
    }
}
