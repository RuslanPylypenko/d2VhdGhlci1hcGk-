<?php

declare(strict_types=1);

namespace App\Exceptions;

use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationException extends \RuntimeException
{
    private ConstraintViolationListInterface $errors;

    public static function create(ConstraintViolationListInterface $errors): self
    {
        return (new self())->setErrors($errors);
    }

    public function setErrors(ConstraintViolationListInterface $errors): self
    {
        $this->errors = $errors;

        return $this;
    }

    public function getErrors(): ConstraintViolationListInterface
    {
        return $this->errors;
    }
}
