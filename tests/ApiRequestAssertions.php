<?php

namespace AlfaDevTeam\RestApiResponses\Tests;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Testing\TestResponse;

trait ApiRequestAssertions
{
    protected function assertSuccessResponse(TestResponse $response)
    {
        return $response->assertJsonPath('success', true)
            ->assertStatus(JsonResponse::HTTP_OK);
    }

    protected function assertErrorResponse(TestResponse $response, $status = JsonResponse::HTTP_BAD_REQUEST)
    {
        return $response->assertJsonPath('success', false)
            ->assertStatus($status);
    }

    protected function assertValidationExceptionResponse(TestResponse $response, array $fields = [], array $attribute = [])
    {
        $isAssociativeArray = $this->isAssociativeArray($fields);

        if ($isAssociativeArray) {
            $this->replaceValidationKeysToMessage($fields, $attribute);
        }

        return $response->assertJsonStructure([
            'response' => [
                'errors' => $isAssociativeArray ? array_keys($fields) : $fields,
            ],
            'success'])
            ->assertJsonFragment(['success' => false])
            ->assertJsonFragment($isAssociativeArray ? $fields : [])
            ->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
    }


    protected function postAuthorizationJson($url, array $params = [])
    {
        return $this->requestAuthorizationJson($url, 'postJson', $params);
    }

    protected function getAuthorizationJson($url): TestResponse
    {
        return $this->requestAuthorizationJson($url, 'getJson');
    }

    protected function deleteAuthorizationJson($url)
    {
        return $this->requestAuthorizationJson($url, 'deleteJson');
    }

    /**
     * @param string $url
     * @param string $method
     * @param array $params
     * @return mixed
     */
    protected function requestAuthorizationJson(string $url, string $method, array $params = [])
    {
        return $this->withHeaders(['Authorization' => 'Bearer ' . $this->plainTextSanctumToken])
            ->requestJson($url, $method, $params);
    }

    protected function postRequestJson($url, array $params = [])
    {
        return $this->requestJson($url, 'postJson', $params);
    }

    protected function getRequestJson($url)
    {
        return $this->requestJson($url, 'getJson');
    }

    /**
     * @param string $url
     * @param string $method
     * @param array $params
     * @return mixed
     */
    protected function requestJson(string $url, string $method, array $params = [])
    {
        $response = $this->$method($url, $params);
        if (App::environment('testing')) {
            $response->dump();
        }
        return $response;
    }

    private function replaceValidationKeysToMessage(&$fields, array $attribute = [], $parentKey = null)
    {
        foreach ($fields as $key => &$field) {
            if (is_array($field)) {
                $this->replaceValidationKeysToMessage($field, $attribute, $key);
            } else {
                $field = __($field, array_merge($attribute,
                    ['attribute' =>
                        str_replace('_', ' ', $parentKey ?? $key)
                    ]
                ));
            }
        }
    }

    private function isAssociativeArray($fields)
    {
        return $fields !== array_values($fields);
    }

    private function getKeysIfAssociativeArray($fields)
    {
        return $this->isAssociativeArray($fields) ? array_keys($fields) : $fields;
    }

    private function getArrayIfAssociative($fields)
    {
        return $this->isAssociativeArray($fields) ? $fields : [];
    }
}
