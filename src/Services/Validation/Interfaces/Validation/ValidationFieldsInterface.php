<?php

namespace Valibool\TelegramConstruct\Services\Validation\Interfaces\Validation;


use Valibool\TelegramConstruct\Services\Messages\MessageConfirmation;
use Valibool\TelegramConstruct\Services\Messages\MessageValidation;

interface ValidationFieldsInterface
{
    /**
     * @param $value
     * @param string $field
     * @param $rule
     * @param string|null $errorMessage
     * @return bool
     */
    public function validate($value, string $field, $rule, string $errorMessage = null) : bool;

    /**
     * @return bool
     */
    public function getValidateFail() : bool;

    /**
     * @return MessageValidation|null
     */
    public function getValidationErrorMessage() : null|MessageValidation;

    /**
     * @param string $errorText
     * @return MessageValidation
     */
    public function createValidationErrorMessage(string $errorText) : MessageValidation;
    public function createValidationConfirmationMessage(string|null $text) : MessageConfirmation;
}
