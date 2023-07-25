<?php

namespace Aqamarine\RestApiResponses\Exceptions;

use Aqamarine\RestApiResponses\Controllers\ApiResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

trait HasRestApiResponses
{
    use ApiResponses;

    protected function invalidJson($request, ValidationException $exception)
    {
        return $this->respondError([
            'errors' => $exception->errors()
        ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
    }
}
