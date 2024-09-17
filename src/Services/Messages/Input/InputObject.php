<?php

namespace Valibool\TelegramConstruct\Services\Messages\Input;

use Illuminate\Support\Facades\Validator;
use Valibool\TelegramConstruct\Models\Message;
use Valibool\TelegramConstruct\Models\TgUser;
use Valibool\TelegramConstruct\Models\UsersConfirmation;
use Valibool\TelegramConstruct\Services\Messages\MessageConfirmation;
use Valibool\TelegramConstruct\Services\Messages\MessageDB;
use Valibool\TelegramConstruct\Services\Messages\MessageValidation;
use Valibool\TelegramConstruct\Services\Validation\Interfaces\Validation\ValidationFieldsInterface;

abstract class InputObject implements ValidationFieldsInterface
{
    public TgUser $user;
    public Message|null $lastMessage;
    public Message|null $nextMessage;
    public null|MessageValidation $errorMessage = null;
    public string $chatId;
    public string $type;
    public string|null $callBackData = null;
    public string|null $inputText = null;

    /**
     * @param $inputFrom
     */
    public function __construct($inputFrom)
    {
        $this->setUser($inputFrom);
        $this->lastMessage = $this->user->lastQuestion ?? null;
        $this->nextMessage = $this->lastMessage->nextMessage ?? null;
    }

    abstract function setChatId(string $chatId): string;

    /**
     * @param $inputFrom
     * @return void
     */
    private function setUser($inputFrom): void
    {
        $this->user = TgUser::where('tg_user_id', $inputFrom->id)->firstOrCreate([
            'tg_user_id' => $inputFrom->id,
            'tg_user_name' => $inputFrom->username ?? null,
            'name' => $inputFrom->username ?? 'noname [' . $inputFrom->id . ']',
        ]);
    }

    /**
     * @return bool
     */
    public function getValidateFail(): bool
    {
        if ($this->lastMessage && $this->lastMessage->wait_input) {
            $validationParams = TgUser::$fieldsToChangeByUser[$this->lastMessage->wait_input];

            return $this->validate(
                $this->inputText,
                $this->lastMessage->wait_input,
                $validationParams['rule'],
                $validationParams['errorMessage']
            );
        }
        return false;
    }

    /**
     * @param $value
     * @param string $field
     * @param $rule
     * @param string|null $errorMessage
     * @return bool
     */
    public function validate($value, string $field, $rule, string $errorMessage = null): bool
    {
        $validator = Validator::make([$field => $value], [
            $field => $rule,
        ],
            [
                $field => $errorMessage
            ]);

        if ($validatorFails = $validator->fails()) {
            $validatorErrorMessages = $validator->messages();
            $validatorErrorText = $validatorErrorMessages->first($field);
            $this->createValidationErrorMessage($validatorErrorText);
        }

        return $validatorFails;
    }

    public function saveInputField(): bool
    {
        if(in_array($this->lastMessage->wait_input, TgUser::$fieldsToChangeByUser)){
            $this->user->{$this->lastMessage->wait_input} = $this->inputText;
            return $this->user->save();
        }
        $this->createValidationErrorMessage('Неверное значение');
        return false;
    }
    /**
     * @param string $errorText
     * @return MessageValidation
     */
    public function createValidationErrorMessage(string $errorText): MessageValidation
    {
        return $this->errorMessage = new MessageValidation($errorText);
    }

    /**
     * @return MessageValidation|null
     */
    public function getValidationErrorMessage(): null|MessageValidation
    {
        return $this->errorMessage;
    }

    public function createValidationConfirmationMessage($text = null): MessageConfirmation
    {
        $this->saveInputForConfirmation($this->inputText, $this->user->id);
        return new MessageConfirmation($text, $this->inputText);
    }

    public function saveInputForConfirmation(string $inputMessageText, string $tgUserId): void
    {
        if ($userConfirmations = UsersConfirmation::where('tg_user_id', $tgUserId)->first()) {
            $userConfirmations->update([
                'input' => $inputMessageText,
            ]);
        } else {
            $userConfirmations = UsersConfirmation::create([
                'tg_user_id' => $tgUserId,
                'input' => $inputMessageText,
            ]);
        }
    }

    /**
     * @return MessageDB|null
     */
    public function getAnswer(): null|MessageDB
    {
        if ($this->nextMessage)
            return new MessageDB($this->nextMessage);
        if ($this->lastMessage)
            return new MessageDB($this->lastMessage);
        return null;
    }

    /**
     * @return string
     */
    public function getChat(): string
    {
        return $this->chatId;
    }


}
