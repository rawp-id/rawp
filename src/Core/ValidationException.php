<?php

// src/Core/ValidationException.php
namespace Core;

class ValidationException extends \Exception
{
    protected $errors;

    public function __construct(array $errors)
    {
        parent::__construct('Validation failed.');
        $this->errors = $errors;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
