<?php

class QldbtableError
{
    private array $errors = [];
    public const ERROR_TYPE_ERROR = 'error';
    public const ERROR_TYPE_INFO = 'info';
    public const ERROR_TYPE_WARNING = 'warning';
    public const ATTR_MESSAGE = 'message';
    public const ATTR_TYPE = 'type';
    public function addError(string $message, string $type = self::ERROR_TYPE_INFO)
    {
        $this->errors[] = [
            self::ATTR_MESSAGE => $message,
            self::ATTR_TYPE => $type,
        ];
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}