<?php
namespace database\seeds;

use Random\RandomException;

class UserFactory
{
    /**
     * @throws RandomException
     */
    public static function create(): array
    {
        $countries = ['US', 'CA', 'UK', 'DE', 'FR', 'JP'];

        $cities = [
            'US' => ['New York', 'Los Angeles', 'Chicago'],
            'CA' => ['Toronto', 'Vancouver', 'Montreal'],
            'UK' => ['London', 'Manchester', 'Birmingham'],
            'DE' => ['Berlin', 'Hamburg', 'Munich'],
            'FR' => ['Paris', 'Marseille', 'Lyon'],
            'JP' => ['Tokyo', 'Osaka', 'Kyoto']
        ];

        $random_value = random_int(0,10000);

        $defaults = [
            'email' => "test$random_value@example.com",
            'name' => "Test User$random_value",
            'country' => $countries[array_rand($countries)],
            'gender' => rand(0, 1) ? 'male' : 'female',
            'status' => rand(0, 1) ? 'active' : 'inactive',
        ];

        $defaults['city'] = $cities[$defaults['country']][random_int(0, 2)];

        return $defaults;
    }
}