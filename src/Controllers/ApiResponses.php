<?php

namespace RestApiResponses\Controllers;

use RestApiResponses\Exceptions\ValidationException;
use Illuminate\Http\JsonResponse;

trait ApiResponses
{
    private $statusCode;

    protected function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    protected function respondSuccess($data = [])
    {
        return $this->setStatusCode(JsonResponse::HTTP_OK)
            ->respond($data, true);
    }

    protected function respondError($data = [], int $statusCode = JsonResponse::HTTP_BAD_REQUEST)
    {
        return $this->setStatusCode($statusCode)
            ->respond($data, false);
    }

    protected function respondSuccessMessage($message = 'respond.successful')
    {
        return $this->setStatusCode(JsonResponse::HTTP_OK)
            ->respond($message, true, 'message');
    }

    protected function respondErrorMessage($message, int $status = JsonResponse::HTTP_BAD_REQUEST)
    {
        return $this->setStatusCode($status)
            ->respond($message, false, 'message');
    }

    protected function respondUnexpectedException($message = 'respond.exception')
    {
        return $this->setStatusCode(JsonResponse::HTTP_INTERNAL_SERVER_ERROR)
            ->respond($message, false, 'message');
    }

    protected function respondValidationException(array $errors)
    {
        throw new ValidationException($errors);
    }

    private function respond($response, bool $success, $typeRespond = 'response')
    {
        if (empty($response)) {
            return response()->json(['success' => $success], $this->statusCode);
        }
        return response()->json([$typeRespond => $response, 'success' => $success], $this->statusCode);
    }
}