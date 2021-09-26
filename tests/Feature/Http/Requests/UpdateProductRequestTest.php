<?php

namespace Tests\Feature\Http\Requests;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateProductRequestTest extends TestCase
{
    use RefreshDatabase;

    public function setUp():void
    {
        parent::setUp();
        Sanctum::actingAs(User::factory()->create());
        $this->product = Product::factory()->create();
    }



    public function sendPatchWithHeaders($arrayOfData)
    {
        return $this->patchJson(
            '/api/v1/products/1', $arrayOfData,[
                'accept' => 'application/vnd.api+json',
                'content-type' => 'application/vnd.api+json',
            ]);
    }
    // // Domain: project177.convey1.cloud
    // | Username: proj177
    // | Password: N2SKV2H4GXuzt8cRQP
    // CPANEL: https://server.vevisto.com/cpanel
    /** @test */


    /** @test */
    public function it_can_update_an_product_from_a_resource_object()
    {
        $response = $this->sendPatchWithHeaders(
                      [
                        'data' => [
                            'id' => '1',
                            'type' => 'products',
                            'attributes' => [
                                'title' => 'Jane Doe',
                            ]
                        ]
                    ]
                );

        $response
        ->assertStatus(200)
        ->assertJson([
            "data" => [
                "id" => '1',
                "type" => "products",
                "attributes" => [
                    'title' => 'Jane Doe',
                    'created_at' => now()->setMilliseconds(0)->toJSON(),
                    'updated_at' => now() ->setMilliseconds(0)->toJSON(),
                ]
            ]
        ]);

        $this->assertSame('Jane Doe', Product::first()->title);
    }


    public function it_validates_product_id_is_required()
    {
        $response = $this->sendPatchWithHeaders(
            [
                'data' => [
                    'type' => 'products',
                    'attributes' => [
                        'title' => 'Jane Doe',
                    ]
                ]
            ]
        );

        $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['data.id']);

        $this->assertSame($this->product->title, Product::first()->title);
    }

    /** @test */
    public function it_validates_product_id_is_string()
    {
        $response = $this->sendPatchWithHeaders(
            [
                'data' => [
                    'id' => 1,
                    'type' => 'products',
                    'attributes' => [
                        'title' => 'Jane Doe',
                    ]
                ]
            ]
        );

        $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['data.id']);

        $this->assertSame($this->product->title, Product::first()->title);
    }

    /** @test */
    public function it_validates_product_type_is_given()
    {
        $response = $this->sendPatchWithHeaders(
            [
                'data' => [
                    'id' => '1',
                    'type' => '',
                    'attributes' => [
                        'title' => 'Jane Doe',
                    ]
                ]
            ]
        );

        $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['data.type']);

        $this->assertSame($this->product->title, Product::first()->title);
    }

    /** @test */
    public function it_validates_products_type_is_given_in_plural()
    {
        $response = $this->sendPatchWithHeaders(
            [
                'data' => [
                    'id' => '1',
                    'type' => 'product',
                    'attributes' => [
                        'title' => 'Jane Doe',
                    ]
                ]
            ]
        );

        $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['data.type']);

        $this->assertSame($this->product->title, Product::first()->title);
    }

    /** @test */
    public function it_validates_attributes_are_given_in_product()
    {
        $response = $this->sendPatchWithHeaders(
            [
                'data' => [
                    'id' => '1',
                    'type' => 'products',

                ]
            ]
        );

        $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['data.attributes']);

        $this->assertDatabaseHas('products', [
            'id' => 1,
            'title' => $this->product->title,
        ]);
    }

    /** @test */
    public function it_validates_attributes_is_given_in_product()
    {
        $response = $this->sendPatchWithHeaders(
             [
                'data' => [
                    'id' => '1',
                    'type' => 'products',
                    'attributes' => 'not object'

                ]
            ]
        );

        $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['data.attributes']);

        $this->assertSame($this->product->title, Product::first()->title);
    }

    /** @test */
    public function it_validates_title_is_string()
    {
        $response = $this->sendPatchWithHeaders(
            [
                'data' => [
                    'id' => '1',
                    'type' => 'products',
                    'attributes' => [
                        'title' => 123,
                    ]
                ]
            ]
        );

        $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['data.attributes.title']);

        $this->assertSame($this->product->title, Product::first()->title);
    }



}
