<?php

namespace Database\Seeders;

use App\Http\Controllers\PaymentController;
use App\Models\Bike;
use App\Models\City;
use App\Models\Club;
use App\Models\User;
use Faker\Generator;
use App\Models\Address;
use App\Models\HikeVtt;
use App\Models\Zipcode;
use App\Models\BikeType;
use App\Models\ClubPost;
use App\Models\PostUser;
use App\Models\ImageUser;
use App\Models\ClubPostLike;
use App\Models\HikeVttImage;
use App\Models\Organization;
use App\Models\PostUserLike;
use App\Models\ClubPostImage;
use App\Models\PostUserImage;
use App\Models\ClubPostComment;
use App\Models\PostUserComment;
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
    // protected $premium3;
    // protected $description3;

    /**
     * Create a new seeder instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->faker = $this->withFaker();
        // $this->premium3 = 'Premium 3';
        // $this->description3 = 'Sub Premium de tier 3';
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

        // $payment= new PaymentController();

        $bike_type_1 = BikeType::create([
            'name' => 'Vélo Tout Terrain (VTT)'
        ]);

        $bike_type_2 = BikeType::create([
            'name' => 'Vélo de Route'
        ]);

        $bike_type_3 = BikeType::create([
            'name' => 'Vélo de Gravel'
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
            'name' => 'Premium 1',
            'stripe_product_id' => 'prod_MIJkg5X0wnpHb6',
            'stripe_price_id' => 'price_1LZjCyGofnt4tufZ9NWGMSIF'
        ]);

        $sub_type_2 = SubscriptionType::create([
            'name' => 'Premium 2',
            'stripe_product_id' => 'prod_MIJlTw1G77xNbZ',
            'stripe_price_id' => 'price_1LZjEKGofnt4tufZaVgerppF'
        ]);


        // Villes
        // $city_1 = City::create([
        //     'name' => 'Plabennec'
        // ]);
        // $city_2 = City::create([
        //     'name' => 'Lesneven'
        // ]);

        // Code postaux
        // $zipcode_1 = Zipcode::create([
        //     'code' => 29860
        // ]);

        // $zipcode_2 = Zipcode::create([
        //     'code' => 29260
        // ]);

        // Adresses
        // $address_1 = Address::create([
        //     'street_address' => '1 Rue Alexandre Baley',
        //     'lat' => 48.5740185,
        //     'lng' => -4.3335965,
        //     'region' => 'Bretgane',
        //     'department' => 'Finistère',
        //     'department_code' => 29,
        //     'zipcode_id' => $zipcode_2->id,
        //     'city_id' => $city_2->id
        // ]);

        // $address_2 = Address::create([
        //     'street_address' => '41 Rue Edmond Michelet',
        //     'lat' => 48.50274001409201,
        //     'lng' => -4.417145225223007,
        //     'region' => 'Bretgane',
        //     'department' => 'Finistère',
        //     'department_code' => 29,
        //     'zipcode_id' => $zipcode_1->id,
        //     'city_id' => $city_1->id
        // ]);

        // Clubs
        // $club_1 = Club::create([
        //     'name' => $this->faker->name(),
        //     'address_id' => $address_1->id,
        //     'organization_id' => $organization_2->id
        // ]);

        // $club_2 = Club::create([
        //     'name' => $this->faker->name(),
        //     'address_id' => $address_2->id,
        //     'organization_id' => $organization_1->id
        // ]);

        // $user_1 = User::create([
        //     'email' => 'test1@gmail.com',
        //     'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        // ]);
        // $user_2 = User::create([
        //     'email' => 'test2@gmail.com',
        //     'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        // ]);
        // $user_3 = User::create([
        //     'email' => 'test3@gmail.com',
        //     'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        // ]);
        // $user_4 = User::create([
        //     'email' => 'test4@gmail.com',
        //     'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        // ]);
        // $user_4 = User::create([
        //     'email' => 'test5@gmail.com',
        //     'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        // ]);

        // User test avec un token sanctum
        // $user_test = User::create([
        //     'id' => 10005,
        //     'email' => 'my@email.com',
        //     'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        // ])->tokens()->create([
        //     'id' => 10005,
        //     'name' => 'api',
        //     'token' => hash('sha256', 'N7fp6GTjO9CJD1QIhqv0Ty1ZZbJeS3tFIbToFJZQ'),
        //     'abilities' => ['*'],
        // ]);

        // $user_test = User::findOrFail($user_test->id);

        // $bike_1 = Bike::create([
        //     'name' => $this->faker->name(),
        //     'brand' => $this->faker->name(),
        //     'model' => $this->faker->name(),
        //     'bike_type_id' => $bike_type_1->id,
        //     'date' => $this->faker->dateTimeBetween('-30 week', '-10 week'),
        //     'image' => 'image-bike-1.png',
        //     'user_id' => $user_test->id
        // ]);

        // $bike_2 = Bike::create([
        //     'name' => $this->faker->name(),
        //     'brand' => $this->faker->name(),
        //     'model' => $this->faker->name(),
        //     'bike_type_id' => $bike_type_2->id,
        //     'date' => $this->faker->dateTimeBetween('-30 week', '-10 week'),
        //     'image' => 'image-bike-2.png',
        //     'user_id' => $user_test->id
        // ]);

        // PostUser::factory(5)->create([
        //     'user_id' => 10005
        // ])->each(function ($post) {
        //     PostUserImage::factory(rand(0, 3))->create([
        //         'user_id' => 10005,
        //         'post_user_id' => $post->id
        //     ]);

        //     PostUserComment::factory(rand(0, 3))->create([
        //         'post_user_id' => $post->id,
        //         'user_id' => rand(1, 4)
        //     ]);

        //     PostUserLike::factory(rand(0, 2))->create([
        //         'user_id' => rand(1, 4),
        //         'post_user_id' => $post->id
        //     ]);
        // });

        // Club::factory(5)->create([
        //     'address_id' => rand(1, 2),
        //     'organization_id' => rand(1, 3)
        // ])->each(function ($club) {

        //     HikeVtt::factory(rand(1, 3))->create([
        //         'club_id' => $club->id
        //     ])->each(function ($hike) use ($club) {

        //         HikeVttImage::factory(rand(2, 5))->create([
        //             'club_id' => $club->id,
        //             'hike_vtt_id' => $hike->id,
        //         ]);

        //         ClubPost::factory(1)->create([
        //             'club_id' => $club->id,
        //             'hike_vtt_id' => $hike->id
        //         ])->each(function ($post) use ($club) {

        //             ClubPostImage::factory(rand(0, 5))->create([
        //                 'club_post_id' => $post->id,
        //                 'club_id' => $club->id,
        //             ]);

        //             ClubPostLike::factory(rand(0, 2))->create([
        //                 'user_id' => rand(1, 4),
        //                 'club_post_id' => $post->id
        //             ]);

        //             ClubPostComment::factory(rand(0, 5))->create([
        //                 'user_id' => rand(1, 4),
        //                 'club_post_id' => $post->id
        //             ]);
        //         });
        //     });
        // });

        // $user_test->followings()->attach($user_1->id);
        // $user_test->followings()->attach($user_2->id);
        // $user_test->followings()->attach($user_3->id);
        // $user_test->followings()->attach($user_4->id);

        // $user_test->clubFollows()->attach(1);
        // $user_test->clubFollows()->attach(2);
        // $user_test->clubFollows()->attach(3);
        // $user_test->clubFollows()->attach(4);
    }
}
