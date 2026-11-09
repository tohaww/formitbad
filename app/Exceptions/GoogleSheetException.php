<?php

namespace App\Exceptions;

use Exception;

class GoogleSheetException extends Exception
{
    public function __construct(
        string $message,
        int $code = 0,
        protected ?string $detail = null,
        ?Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function getDetail(): ?string
    {
        return $this->detail;
    }
}

