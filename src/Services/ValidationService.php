<?php

namespace Valibool\TelegramConstruct\Services;

use Illuminate\Support\Facades\Validator;
use Valibool\TelegramConstruct\Models\User;
use Valibool\TelegramConstruct\Models\UsersConfirmation;

class ValidationService
{
    private mixed $telegram;

    public function __construct($telegram)
    {
        $this->telegram = $telegram;

    }

    public function validateAnswerTheQuestion($inputMessageText, $questionMessage): bool
    {

        $explode = explode('_', $questionMessage->wait_input);
        $model = app($explode[0]);
        $field = $explode[1];
        $validationFieldsParams = $model->fieldsToUserInput();
        $type = $validationFieldsParams[$field]['validate'];

        $validator = Validator::make(['text' => $inputMessageText], [
            'text' => $type,
        ]);
        return $validator->fails();
    }

    public function sendValidationMessage($chatId)
    {
        $text = 'Неправильно введены данные';

        return Output::sendMessage($this->telegram, $text, false, $chatId);
    }

    public function sendConfirmationMessage($inputMessageText, $chatId, $userId)
    {
//        $inputMessageText = $inputMessageText->getMessage()->text;
        $text = 'Подтвердите введенные данные: ' . $inputMessageText;
        $confirmationButtons = [
            [
                'text' => 'Да',
                'callback_data' => 'confirmation_true',
            ],
            [
                'text' => 'Нет',
                'callback_data' => 'confirmation_false',
            ],
        ];
        $keyboard = Output::renderInlineKeyboardCustom($confirmationButtons);
        $sentConfirmation = Output::sendMessage($this->telegram, $text, $keyboard, $chatId);
        UsersConfirmation::create([
            'user_id' => $userId,
            'input' => $inputMessageText,
        ]);
        return $sentConfirmation;

    }

    public function confirmLastMessage(User $user)
    {
        $usersInput = $user->waitConfirmation->input;
        $lastQuestion = $user->lastQuestion;
        $confirmation = explode('_', $user->lastQuestion->wait_input);

        $model = app($confirmation[0]);
        $field = $confirmation[1];

        if (get_class($model) == get_class($user)) {
            $user->{$field} = $usersInput;
            if ($user->save()) {
                $user->waitConfirmation->delete();
            }
        }
    }
}
