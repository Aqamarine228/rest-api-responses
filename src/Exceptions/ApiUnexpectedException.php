<?php

namespace Aqamarine\RestApiResponses\Exceptions;

use Aqamarine\RestApiResponses\Controllers\ApiResponses;

class ApiUnexpectedException extends \Exception
{
    use ApiResponses;

    public function render()
    {
        return $this->respondUnexpectedException();
    }
}
