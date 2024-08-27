<?php

namespace Ghostscypher\Mpesa\Exceptions;

/**
 * MpesaValidationException class
 *
 * This will be thrown when validation fails
 */
class MpesaValidationException extends \Exception
{
    public array $errors;

    /**
     * MpesaValidationException constructor.
     *
     * @param  Throwable|null  $previous
     */
    public function __construct(array $errors, string $message = 'Validation failed', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->errors = $errors;

        $this->message = $message.' ---> '.json_encode($errors);
    }
}
