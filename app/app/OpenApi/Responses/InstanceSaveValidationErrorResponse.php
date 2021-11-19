<?php

namespace App\OpenApi\Responses;

use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Vyuldashev\LaravelOpenApi\Factories\ResponseFactory;

class InstanceSaveValidationErrorResponse extends ResponseFactory
{
    public function build(): Response
    {
        $response = Schema::object()->properties(
            Schema::string('message')->example('The given data was invalid.'),
            Schema::object('errors')
                ->additionalProperties(
                    Schema::array()->items(Schema::string())
                )
                ->example([
                    'name' => ['The name must not be greater than 255 characters.']
                ])
        );

        return Response::create()
            ->statusCode(HttpResponse::HTTP_UNPROCESSABLE_ENTITY)
            ->description('Validation errors')
            ->content(
                MediaType::json()->schema($response)
            );
    }
}
