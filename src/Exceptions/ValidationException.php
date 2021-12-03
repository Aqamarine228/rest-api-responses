<?php

namespace RestApiResponses\Exceptions;

use RestApiResponses\Controllers\ApiResponses;
use Illuminate\Http\JsonResponse;

class ValidationException extends \Exception
{
    use ApiResponses;

    private $errors;

    public function __construct(array $errors = [])
    {
        parent::__construct();

        foreach ($errors as $field => $error){
            $this->errors[$field] = [__($error, ['attribute' => $field])];
        }
    }

    public function render()
    {
        return $this->respondError(['errors' => $this->errors], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
    }
}
