<?php

namespace Tests\Feature\Http\Requests;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreatePorductRequestTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        Sanctum::actingAs(User::factory()->create());
    }

    public function sendPatchWithHeaders($arrayOfData)
    {
        return $this->postJson(
            '/api/v1/products', $arrayOfData,[
                'accept' => 'application/vnd.api+json',
                'content-type' => 'application/vnd.api+json',
            ]);
    }

    /** @test */
    public function it_validates_that_the_type_is_given_when_creating_a_product()
    {
        $response = $this->sendPatchWithHeaders(
            [
            'data' => [
                'type' => '',
                'attributes' => [
                    'title' => 'John Doe',
                ]
            ]
        ]);

        $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['data.type']);


        $this->assertFalse(Product::whereTitle('John Doe')->exists());
    }

    /** @test */
    public function it_does_require_products_is_plural(): void
    {
        $response = $this->sendPatchWithHeaders(
            [
            'data' => [
                'type' => 'product',
                'attributes' => [
                    'title' => 'John Doe',
                ]
            ]
        ]);

        $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['data.type']);


        $this->assertFalse(Product::whereTitle('John Doe')->exists());
    }

    /** @test */
    public function it_valides_that_the_attributes_is_required(): void
    {
        $response = $this->sendPatchWithHeaders(
            [
            'data' => [
                'type' => 'products',
            ]
        ]);

        $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['data.attributes']);

        $this->assertFalse(Product::whereTitle('John Doe')->exists());
    }

    public function it_valides_that_the_attributes_is_object(): void
    {

        $response = $this->sendPatchWithHeaders(
            [
            'data' => [
                'type' => 'products',
                'attributes' => 'not an object',
            ]
        ]);

        $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['data.attributes']);


        $this->assertFalse(Product::whereTitle('John Doe')->exists());
    }

    /** @test */
    public function it_valides_that_the_title_is_required(): void
    {
        $response = $this->sendPatchWithHeaders(
            [
            'data' => [
                'type' => 'products',
                'attributes' => [
                    'title' => '',
                ]
            ]
        ]);

        $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['data.attributes.title']);


        $this->assertFalse(Product::whereTitle('John Doe')->exists());
    }

    /** @test */
    public function it_valides_that_the_title_is_string(): void
    {
        $response = $this->sendPatchWithHeaders(
            [
            'data' => [
                'type' => 'products',
                'attributes' => [
                    'title' => 12,
                ]
            ]
        ]);

        $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['data.attributes.title'])
        ->assertJson([
            "message" => "The given data was invalid.",
            "errors" => [
            "data.attributes.title" => [
                "The data.attributes.title must be a string.",
                ],
            ],
        ]);

        $this->assertEquals('The data.attributes.title must be a string.',$response->decodeResponseJson()['errors']['data.attributes.title'][0]);

        $response->assertJson(
            fn (AssertableJson $json) =>
            $json->has('message')
                 ->has('errors', 1)
                );

        $this->assertFalse(Product::whereTitle('John Doe')->exists());
    }


}
