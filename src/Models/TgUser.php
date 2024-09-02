<?php

namespace Valibool\TelegramConstruct\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TgUser extends Model
{
    protected $fillable = [
        'user_id',
        'email',
        'name',
        'last_message_id',
        'last_tg_message_id',
        'phone',
        'tg_user_id',
        'tg_user_name',
    ];

    public static string $modelName = "Клиенты";

    /**
     * Array for tgUsers question answer to save in DB
     * @var array|array[]
     */
    public static array $fieldsToChangeByUser = [
        'email' => [
            "title" => "Емэйл",
            'validator' => 'string|unique:tg_users|email|max:255',
            'errorMessage' => 'Некорректный или уже существующий емэйл'
        ],
        'name' => [
            "title" => "Имя и фамилия",
            'validator' => 'string|max:255',
            'errorMessage' => 'Максимальная длина 255 символов'

        ],
        'phone' => [
            "title" => "Телефон",
            'validator' => 'numeric|unique:tg_users',
            'errorMessage' => 'Некорректный телефон/ телефон уже существует'
        ],
    ];

    /**
     * @return HasOne
     */
    public function lastQuestion(): HasOne
    {
        return $this->hasOne(Message::class, 'id', 'last_message_id');
    }

    /**
     * @return HasOne
     */
    public function waitConfirmation(): HasOne
    {
        return $this->hasOne(UsersConfirmation::class);
    }

    /**
     * @return bool
     */
    public function mustAnswer(): bool
    {
        if ($this->waitConfirmation || ($this->lastQuestion && $this->lastQuestion->type == 'question')) {
            return true;
        }
        return false;
    }

    /**
     * @param Message $message
     * @param string $lastTgMessageId
     * @return bool
     */
    public function saveLastMessage(Message $message, string $lastTgMessageId): bool
    {
        $this->last_message_id = $message->id;
        $this->last_tg_message_id = $lastTgMessageId;
        return $this->save();
    }

}

