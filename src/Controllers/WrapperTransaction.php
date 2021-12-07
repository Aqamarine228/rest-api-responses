<?php

namespace AlfaDevTeam\RestApiResponses\Controllers;

use AlfaDevTeam\RestApiResponses\Exceptions\ApiUnexpectedException;
use AlfaDevTeam\RestApiResponses\Exceptions\ValidationException;
use Illuminate\Support\Facades\DB;

trait WrapperTransaction
{
    public function runInTransaction(callable $function)
    {
        DB::beginTransaction();
        try {
            $response = $function();
            DB::commit();
            return $response;
        } catch (ValidationException $exception) {
            $this->afterRollBack($exception);
        } catch (\Exception $exception) {
            $this->afterRollBack(new ApiUnexpectedException(
                $exception->getMessage(),
                gettype($exception->getCode()) == 'integer'? $exception->getCode() : 500,
                $exception->getPrevious()
            ));
            return null;
        }
    }

    protected function afterRollBack(\Exception $exception)
    {
        DB::rollBack();
        if (config('app.env') == 'local') {
            dd($exception->getMessage());
        }
        throw $exception;
    }
}
