<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserControllerTest extends TestCase
{


    /**
     * A basic feature test example.
     *
     * @return void
     */

    public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
    public function test_SuccessfulRegistration()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
        ])->json('POST', '/api/register', [
            "role" => "user",
            "first_name" => "Anil",
            "last_name" => "Kumar",
            "email" => "anilss@gmail.com",
            "phone_no" => "9134352413",
            "password" => "12345678",
            "confirm_password" => "12345678"
        ]);
        $response->assertStatus(201);
    }

    public function test_UnSuccessfulRegistration()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
        ])->json('POST', '/api/register', [
            "role" => "user",
            "first_name" => "Anil",
            "last_name" => "Kumar",
            "email" => "anil@gmail.com",
            "phone_no" => "9134352413",
            "password" => "12345678",
            "confirm_password" => "12345678"
        ]);
        $response->assertStatus(401);
    }


     /**
     * @test for
     * Successfull Login
     */
    public function test_SuccessfulLogin()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
        ])->json(
            'POST',
            '/api/login',
            [
                "email" => "anil@gmail.com",
                "password" => "12345678"
            ]
        );
        $response->assertStatus(200);
    }


    public function test_UnSuccessfulLogin()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
        ])->json(
            'POST',
            '/api/login',
            [
                "email" => "xyez@gmail.com",
                "password" => "123456"
            ]
        );
        $response->assertStatus(404);
    }

    public function test_SuccessfulLogout()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNjUwMDMwODk3LCJleHAiOjE2NTAwMzQ0OTcsIm5iZiI6MTY1MDAzMDg5NywianRpIjoidUd4bm93Q3FyQTFCU0FyTSIsInN1YiI6IjIiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.ZGwsV7npZXWXbX-IBxDGJN0mphS8R42Gp0v4XRKs3sc'
        ])->json('POST', '/api/logout');
        $response->assertStatus(201);
    }

    public function test_SuccessfulForgotPassword()
    { {
            $response = $this->withHeaders([
                'Content-Type' => 'Application/json',
            ])->json('POST', '/api/forgotPassword', [
                "email" => "depaknb5@gmail.com"
            ]);

            $response->assertStatus(201);
        }
    }
}
