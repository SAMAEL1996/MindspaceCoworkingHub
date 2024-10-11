<?php

namespace App\Library;

class Helper
{
    public static function get12HourTimeSelectOptions()
    {
        return [
            "12:00 AM" => "12:00 AM",
            "12:30 AM" => "12:30 AM",
            "01:00 AM" => "01:00 AM",
            "01:30 AM" => "01:30 AM",
            "02:00 AM" => "02:00 AM",
            "02:30 AM" => "02:30 AM",
            "03:00 AM" => "03:00 AM",
            "03:30 AM" => "03:30 AM",
            "04:00 AM" => "04:00 AM",
            "04:30 AM" => "04:30 AM",
            "05:00 AM" => "05:00 AM",
            "05:30 AM" => "05:30 AM",
            "06:00 AM" => "06:00 AM",
            "06:30 AM" => "06:30 AM",
            "07:00 AM" => "07:00 AM",
            "07:30 AM" => "07:30 AM",
            "08:00 AM" => "08:00 AM",
            "08:30 AM" => "08:30 AM",
            "09:00 AM" => "09:00 AM",
            "09:30 AM" => "09:30 AM",
            "10:00 AM" => "10:00 AM",
            "10:30 AM" => "10:30 AM",
            "11:00 AM" => "11:00 AM",
            "11:30 AM" => "11:30 AM",
            "12:00 PM" => "12:00 PM",
            "12:30 PM" => "12:30 PM",
            "01:00 PM" => "01:00 PM",
            "01:30 PM" => "01:30 PM",
            "02:00 PM" => "02:00 PM",
            "02:30 PM" => "02:30 PM",
            "03:00 PM" => "03:00 PM",
            "03:30 PM" => "03:30 PM",
            "04:00 PM" => "04:00 PM",
            "04:30 PM" => "04:30 PM",
            "05:00 PM" => "05:00 PM",
            "05:30 PM" => "05:30 PM",
            "06:00 PM" => "06:00 PM",
            "06:30 PM" => "06:30 PM",
            "07:00 PM" => "07:00 PM",
            "07:30 PM" => "07:30 PM",
            "08:00 PM" => "08:00 PM",
            "08:30 PM" => "08:30 PM",
            "09:00 PM" => "09:00 PM",
            "09:30 PM" => "09:30 PM",
            "10:00 PM" => "10:00 PM",
            "10:30 PM" => "10:30 PM",
            "11:00 PM" => "11:00 PM",
            "11:30 PM" => "11:30 PM"
        ];
    }

    public static function getConferencePackages()
    {
        return [
            [
                'type' => 'Package 1',
                'max_pax' => 8,
                'rates' => [
                    [
                        'label' => '3 Hours',
                        'hours' => 3,
                        'price' => 1500,
                    ],
                    [
                        'label' => '5 Hours',
                        'hours' => 5,
                        'price' => 2000,
                    ],
                    [
                        'label' => '8 Hours',
                        'hours' => 8,
                        'price' => 2500,
                    ],
                    [
                        'label' => 'Whole Day',
                        'hours' => 24,
                        'price' => 3500,
                    ],
                ],
                'succeeding_hours' => 250,
                'additional_person' => 300
            ],
            [
                'type' => 'Package 2',
                'max_pax' => 15,
                'rates' => [
                    [
                        'label' => '3 Hours',
                        'hours' => 3,
                        'price' => 2000,
                    ],
                    [
                        'label' => '5 Hours',
                        'hours' => 5,
                        'price' => 2500,
                    ],
                    [
                        'label' => '8 Hours',
                        'hours' => 8,
                        'price' => 3000,
                    ],
                    [
                        'label' => 'Whole Day',
                        'hours' => 24,
                        'price' => 4500,
                    ],
                ],
                'succeeding_hours' => 300,
                'additional_person' => null
            ]
        ];
    }

    public static function getConferencePackageInfo($packageId)
    {
        if($packageId == 1) {
            return [
                'id' => 1,
                'type' => 'Package 1',
                'max_pax' => 8,
                'rates' => [
                    [
                        'label' => '3 Hours',
                        'hours' => 3,
                        'price' => 1500,
                    ],
                    [
                        'label' => '5 Hours',
                        'hours' => 5,
                        'price' => 2000,
                    ],
                    [
                        'label' => '8 Hours',
                        'hours' => 8,
                        'price' => 2500,
                    ],
                    [
                        'label' => 'Whole Day',
                        'hours' => 24,
                        'price' => 3500,
                    ],
                ],
                'succeeding_hours' => 250,
                'additional_person' => 300
            ];
        }
        
        if($packageId == 2) {
            return [
                'id' => 2,
                'type' => 'Package 2',
                'max_pax' => 15,
                'rates' => [
                    [
                        'label' => '3 Hours',
                        'hours' => 3,
                        'price' => 2000,
                    ],
                    [
                        'label' => '5 Hours',
                        'hours' => 5,
                        'price' => 2500,
                    ],
                    [
                        'label' => '8 Hours',
                        'hours' => 8,
                        'price' => 3000,
                    ],
                    [
                        'label' => 'Whole Day',
                        'hours' => 24,
                        'price' => 4500,
                    ],
                ],
                'succeeding_hours' => 300,
                'additional_person' => null
            ];
        }

        return false;
    }

    public static function getConferencePackageRate($packageId, $hours)
    {
        $package = self::getConferencePackageInfo($packageId);

        if(!$package) {
            return null;
        }

        $rates = $package['rates'];
        foreach($rates as $rate) {
            if($rate['hours'] == $hours) {
                return $rate;
            }
        }

        return null;
    }
}