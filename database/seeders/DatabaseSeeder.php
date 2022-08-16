<?php

namespace Database\Seeders;

use App\Models\Bike;
use App\Models\City;
use App\Models\Club;
use App\Models\User;
use Faker\Generator;
use App\Models\Address;
use App\Models\Zipcode;
use App\Models\BikeType;
use App\Models\ImageUser;
use App\Models\Organization;
use Illuminate\Database\Seeder;
use App\Models\SubscriptionType;
use Illuminate\Container\Container;

class DatabaseSeeder extends Seeder
{
    /**
     * The current Faker instance.
     *
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * Create a new seeder instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->faker = $this->withFaker();
    }

    /**
     * Get a new Faker instance.
     *
     * @return \Faker\Generator
     */
    protected function withFaker()
    {
        return Container::getInstance()->make(Generator::class);
    }

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        $bike_type_1 = BikeType::create([
            'name' => 'VTT'
        ]);

        $bike_type_2 = BikeType::create([
            'name' => 'Route'
        ]);

        $bike_type_3 = BikeType::create([
            'name' => 'Gravel'
        ]);

        // Organizations (club)
        $organization_1 = Organization::create([
            'name' => 'Club affilié à une fédération'
        ]);
        $organization_2 = Organization::create([
            'name' => 'Association'
        ]);
        $organization_3 = Organization::create([
            'name' => 'Groupe'
        ]);

        // Type de subs
        $sub_type_1 = SubscriptionType::create([
            'name' => 'Premium 1'
        ]);

        $sub_type_2 = SubscriptionType::create([
            'name' => 'Premium 2'
        ]);


        // Villes
        $city_1 = City::create([
            'name' => 'Plabennec'
        ]);
        $city_2 = City::create([
            'name' => 'Lesneven'
        ]);

        // Code postaux
        $zipcode_1 = Zipcode::create([
            'code' => 29860
        ]);

        $zipcode_2 = Zipcode::create([
            'code' => 29260
        ]);

        // Adresses
        $address_1 = Address::create([
            'street_address' => '1 Rue Alexandre Baley',
            'lat' => 48.5740185,
            'lng' => -4.3335965,
            'region' => 'Bretgane',
            'department' => 'Finistère',
            'department_code' => 29,
            'zipcode_id' => $zipcode_2->id,
            'city_id' => $city_2->id
        ]);

        $address_2 = Address::create([
            'street_address' => '41 Rue Edmond Michelet',
            'lat' => 48.50274001409201,
            'lng' => -4.417145225223007,
            'region' => 'Bretgane',
            'department' => 'Finistère',
            'department_code' => 29,
            'zipcode_id' => $zipcode_1->id,
            'city_id' => $city_1->id
        ]);

        // Clubs
        $club_1 = Club::create([
            'name' => $this->faker->name(),
            'address_id' => $address_1->id,
            'organization_id' => $organization_2->id
        ]);

        $club_2 = Club::create([
            'name' => $this->faker->name(),
            'address_id' => $address_2->id,
            'organization_id' => $organization_1->id
        ]);

        $user = User::create([
            'email' => 'test@gmail.com',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        ]);

        // User test avec un token sanctum
        $user_test = User::create([
            'id' => 10005,
            'email' => 'my@email.com',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
            'address_id' => 1
        ])->tokens()->create([
            'id' => 10005,
            'name' => 'api',
            'token' => hash('sha256', 'N7fp6GTjO9CJD1QIhqv0Ty1ZZbJeS3tFIbToFJZQ'),
            'abilities' => ['*'],
        ]);

        $bike_1 = Bike::create([
            'name' => $this->faker->name(),
            'brand' => $this->faker->name(),
            'model' => $this->faker->name(),
            'bike_type_id' => $bike_type_1->id,
            'date' => $this->faker->dateTimeBetween('-30 week', '-10 week'),
            'image' => 'image-bike-1.png',
            'user_id' => $user_test->id
        ]);

        $bike_2 = Bike::create([
            'name' => $this->faker->name(),
            'brand' => $this->faker->name(),
            'model' => $this->faker->name(),
            'bike_type_id' => $bike_type_2->id,
            'date' => $this->faker->dateTimeBetween('-30 week', '-10 week'),
            'image' => 'image-bike-2.png',
            'user_id' => $user_test->id
        ]);
    }
}
