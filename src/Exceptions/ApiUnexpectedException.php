<?php

namespace AlfaDevTeam\RestApiResponses\Exceptions;

use AlfaDevTeam\RestApiResponses\Controllers\ApiResponses;

class ApiUnexpectedException extends \Exception
{
    use ApiResponses;

    public function render()
    {
        return $this->respondUnexpectedException();
    }
}
