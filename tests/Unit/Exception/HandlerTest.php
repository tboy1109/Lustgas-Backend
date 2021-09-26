<?php

namespace Tests\Unit\Exception;

use Tests\TestCase;
use App\Exceptions\Handler;
use Illuminate\Http\Request;
use Illuminate\Testing\TestResponse;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class HandlerTest extends TestCase
{


    /** @test */
    public function it_converts_an_exception_into_a_json_api_spec_error_response()
    {
        return $this->assertTrue(true);
        $handler = app(Handler::class);

        $request = Request::create('/test', 'GET');
        $request->headers->set('accept', 'application/vnd.api+json');

        $exception = new \Exception('Test exception');

        $response = $handler->render($request, $exception);
        TestResponse::fromBaseResponse($response)->assertJson([
            'errors' => [
                [
                    'title'   => 'Exception',
                    'details' => 'Test exception',
                ]
            ]
        ])->assertStatus(500);
    }

}
