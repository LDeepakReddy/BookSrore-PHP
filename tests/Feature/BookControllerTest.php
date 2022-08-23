<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BookControllerTest extends TestCase
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

    public function test_SuccessfullAddingBook()
    {
        $token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNjYxMTM4MjY0LCJleHAiOjE2NjExNDE4NjQsIm5iZiI6MTY2MTEzODI2NCwianRpIjoidnhBM1JSZ2czcm5ZWWw5MSIsInN1YiI6IjIiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.fKp14rWPRjaN1NVsbrd50-kV8XWtWZV04A1kGij9ACg";
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'authorization'=> 'Bearer ' . "$token",
        ])->json('POST', '/api/addingBook', [
            "name" => "LDJSA",
            "description" => "IPL ARTICLE",
            "author" => "Anil",
            "image" => "images.jpg",
            "Price" => "1000",
            "quantity" => "10",
        ]);
        $response->assertStatus(200);
    }

    public function test_SuccessfullAddQuantityToExistingBook()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNjUwMDMxODM4LCJleHAiOjE2NTAwMzU0MzgsIm5iZiI6MTY1MDAzMTgzOCwianRpIjoiYzhMV2hkMU9MTjRsaXREeCIsInN1YiI6IjIiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.j6WmGlYAb4g7IIRlI5PjLEPcx8dKjYlx4oIuqZhi_Jw'
        ])->json(
            'POST',
            '/api/addQuantityToExistBook',
            [
                "id" => "5",
                "quantity" => "7"
            ]
        );
        $response->assertStatus(201);
    }

    public function test_UnSuccessfullAddQuantityToExistingBook()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNjUwMDMxODM4LCJleHAiOjE2NTAwMzU0MzgsIm5iZiI6MTY1MDAzMTgzOCwianRpIjoiYzhMV2hkMU9MTjRsaXREeCIsInN1YiI6IjIiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.j6WmGlYAb4g7IIRlI5PjLEPcx8dKjYlx4oIuqZhi_Jw'
        ])->json(
            'POST',
            '/api/addQuantityToExistBook',
            [
                "id" => "30",
                "quantity" => "5"
            ]
        );
        $response->assertStatus(404)->assertJson(['message' => 'Couldnot found a book with that given id']);
    }

    public function test_SuccessfullDeleteBook()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNjUwMDMxODM4LCJleHAiOjE2NTAwMzU0MzgsIm5iZiI6MTY1MDAzMTgzOCwianRpIjoiYzhMV2hkMU9MTjRsaXREeCIsInN1YiI6IjIiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.j6WmGlYAb4g7IIRlI5PjLEPcx8dKjYlx4oIuqZhi_Jw'
        ])->json(
            'POST',
            '/api/deleteBookById',
            [
                "id" => "4",
            ]
        );
        $response->assertStatus(201)->assertJson(['message' => 'Book deleted Sucessfully']);
    }


    public function test_UnSuccessfullDeleteBook()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Application/json',
            'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNjUwMDMxODM4LCJleHAiOjE2NTAwMzU0MzgsIm5iZiI6MTY1MDAzMTgzOCwianRpIjoiYzhMV2hkMU9MTjRsaXREeCIsInN1YiI6IjIiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.j6WmGlYAb4g7IIRlI5PjLEPcx8dKjYlx4oIuqZhi_Jw'
        ])->json(
            'POST',
            '/api/deleteBookById',
            [
                "id" => "33",
            ]
        );
        $response->assertStatus(404)->assertJson(['message' => 'Book not Found']);
    }
}
