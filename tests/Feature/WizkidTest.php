<?php

namespace Tests\Feature;

use App\Services\FileService;
use Faker\Factory as Faker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Http\Testing\File;

class WizkidTest extends TestCase
{


    public function testGetAllWizkids()
    {
        $response = $this->json('GET', 'api/wizkid/list');
        $response->assertStatus(201);
    }

    public function testCreateWizkid()
    {
        $wizkid_rols = User::$ROLE_OPTIONS; 
        for ($i = 1; $i <= 3; $i++) {
            $random_role = $wizkid_rols[array_rand($wizkid_rols)];
            $faker = Faker::create();
            $data = [
                'name' => $faker->sentence(),
                'picture' => File::create("imageTestCreateWizkid.jpg", 200),
                'email' => $faker->email,
                'role' => $random_role,
            ];
            $response = $this->json('POST', '/api/wizkid/create', $data);
            $response->assertStatus(201);
        }
    }

    public function testUpdateWizkid()
    {
        $wizkid_rols = User::$ROLE_OPTIONS;
        $random_role = $wizkid_rols[array_rand($wizkid_rols)];
        $wizkid = User::take(1)->first();
        $faker = Faker::create();
        $data = [
            'name' => $faker->sentence(),
            'picture' => File::create("imageTestUpdateWizkid.jpg", 200),
            'email' => $faker->email,
            'role' => $random_role,
            '_method' => 'PUT'
        ];
        $response = $this->json('POST', '/api/wizkid/update/' . $wizkid->id, $data);
        $response->assertStatus(201);
    }

    public function testDeleteWizkid()
    {
        $wizkid = User::take(1)->first();
        $wizkid = $this->json('DELETE', '/api/wizkid/delete/' . $wizkid->id);
        $wizkid->assertStatus(201);
    }
}
