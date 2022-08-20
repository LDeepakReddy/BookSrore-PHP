<?php

namespace App\Exceptions;

use Exception;

class bookstoreexception extends Exception
{
    public function message()
    {
        return $this->getMessage();
    }
    public function statusCode()
    {
        return $this->getCode();
    }
}
