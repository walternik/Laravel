<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PlayTest extends TestCase
{
    
    public function test_api_tournament_successful_male()
    {
        $data = '
            {
                "tournament_type": "male",
                "players": [
                    {
                        "name": "Goran Svenson",
                        "skill": 95,
                        "strength": 120,
                        "speed": 18
                    },
                    {
                        "name": "Sebastian Petersen",
                        "skill": 92,
                        "strength": 128,
                        "speed": 21
                    }
                ]
            }';
        $data = json_decode($data, true);
        $response = $this->post('/api/play', $data);
        $response->assertStatus(200);
    }


    public function test_api_tournament_successful_female()
    {
        $data = '
            {
                "tournament_type": "female",
                "players": [
                    {
                        "name": "Erika Stevenson",
                        "skill": 95,
                        "reaction": 85
                    },
                    {
                        "name": "Gabriela Sabat",
                        "skill": 96,
                        "reaction": 81
                    }
                ]
            }';
        $data = json_decode($data, true);
        $response = $this->post('/api/play', $data);
        $response->assertStatus(200);
    }


    public function test_api_tournament_wrong_type()
    {
        $data = '
            {
                "tournament_type": "mixed",
                "players": []
            }';
        $data = json_decode($data, true);
        $response = $this->post('/api/play', $data);
        $response->assertStatus(400);
    }


    public function test_api_tournament_wrong_number_of_players()
    {
        $data = '
            {
                "tournament_type": "male",
                "players": [
                    {
                        "name": "Goran Svenson",
                        "skill": 95,
                        "strength": 120,
                        "speed": 18
                    },
                    {
                        "name": "Victor Bhorn",
                        "skill": 91,
                        "strength": 130,
                        "speed": 18
                    },
                    {
                        "name": "Sebastian Petersen",
                        "skill": 92,
                        "strength": 128,
                        "speed": 21
                    }
                ]
            }';
        $data = json_decode($data, true);
        $response = $this->post('/api/play', $data);
        $response->assertStatus(400);
    }
}
