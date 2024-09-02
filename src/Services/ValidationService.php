<?php

namespace Valibool\TelegramConstruct\Services;

use Illuminate\Support\Facades\Validator;

use Valibool\TelegramConstruct\Models\Message;
use Valibool\TelegramConstruct\Models\TgUser;
use Valibool\TelegramConstruct\Models\UsersConfirmation;
use Valibool\TelegramConstruct\Services\Response\ResponseService;

class ValidationService
{
    private Message $errorMessage;


    public static function getAllowedUsersInputs()
    {
        $fieldsList = [];
        $models = config('telegram-construct.models_to_users_inputs');
        foreach ($models as $model => $modelClass) {
            if (isset($modelClass::$fieldsToChangeByUser)) {
                foreach ($modelClass::$fieldsToChangeByUser as $key => $params) {
                    $fieldsList[$model . '_' . $key] = $modelClass::$modelName . ' [' . $params['title'] . ']';
                }
            }
        }
        if ($fieldsList)
            return ResponseService::success($fieldsList);
        return ResponseService::notFound();
    }

    /**
     * @param $inputMessageText
     * @param $questionMessage
     * @return bool
     */
    public function validateAnswerTheQuestion($inputMessageText, $questionMessage): bool
    {
        $configs = config('telegram-construct.models_to_users_inputs');

        $explode = explode('_', $questionMessage->wait_input);
        $model = app($configs[$explode[0]]);
        $field = $explode[1];

        $validationFieldsParams = $model::$fieldsToChangeByUser;
        $validatorParams = $validationFieldsParams[$field]['validator'];
        $validatorMessage = $validationFieldsParams[$field]['errorMessage'];

        $validator = Validator::make([$field => $inputMessageText], [
            $field => $validatorParams,
        ],
            [
                $field => $validatorMessage
            ]);

        if ($validatorFails = $validator->fails()) {
            $validatorErrorMessages = $validator->messages();
            $validatorErrorText = $validatorErrorMessages->first($field);

            $this->errorMessage = new Message();
            $this->errorMessage->text = $validatorErrorText;
            $this->errorMessage->type = 'validation_error';

        }

        return $validatorFails;
    }

    /**
     * @param $inputMessageText
     * @param $userId
     * @return Message
     */
    public function getConfirmationMessage(string $inputMessageText, TgUser $user): Message
    {
        $confirmationMessage = new Message();
        $confirmationMessage->text = "Подтвердите ввод: ".$inputMessageText.". Или введите заново.";
        $confirmationMessage->type = 'confirmation';
        $confirmationButtons = [
            [
                'text' => 'Подтверждаю',
                'callback_data' => 'confirmation_true',
            ],
            [
                'text' => 'Пропустить',
                'callback_data' => 'skip',
            ],
        ];

        $confirmationMessage->buttons = $confirmationButtons;

        if ($userConfirmations = UsersConfirmation::where('tg_user_id', $user->id)->first()) {
            $userConfirmations->update([
                'input' => $inputMessageText,
            ]);
        } else {
            $userConfirmations = UsersConfirmation::create([
                'tg_user_id' => $user->id,
                'input' => $inputMessageText,
            ]);
        }

        return $confirmationMessage;
    }

    /**
     * @return Message
     */
    public function getValidationErrorMessage(): Message
    {
        return $this->errorMessage;
    }

    public function confirmLastMessage(TgUser $user)
    {
        if($user->waitConfirmation){
            $usersInput = $user->waitConfirmation->input;
            $confirmation = explode('_', $user->lastQuestion->wait_input);
            $model = app(config('telegram-construct.models_to_users_inputs')[$confirmation[0]]);
            $field = $confirmation[1];

            if (get_class($model) == get_class($user)) {
                $user->{$field} = $usersInput;
                if ($user->save()) {
                    $user->waitConfirmation->delete();
                }
            }
        }
    }

    public function skipLastMessage(TgUser $user)
    {
        if($user->waitConfirmation){
            $user->waitConfirmation->delete();
        }
    }
}
