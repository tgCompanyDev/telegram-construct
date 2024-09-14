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
        'last_tg_media_group_messages_ids',
        'phone',
        'tg_user_id',
        'tg_user_name',
    ];
    /**
     * Array for tgUsers question answer to save in DB
     * @var array|array[]
     */
    public static array $fieldsToChangeByUser = [
        'email' => [
            "title" => "Емэйл",
            'rule' => 'string|unique:tg_users|email|max:255',
            'errorMessage' => 'Некорректный или уже существующий емэйл'
        ],
        'name' => [
            "title" => "Имя и фамилия",
            'rule' => 'string|max:255',
            'errorMessage' => 'Максимальная длина 255 символов'

        ],
        'phone' => [
            "title" => "Телефон",
            'rule' => 'numeric|unique:tg_users',
            'errorMessage' => 'Некорректный телефон/ телефон уже существует'
        ],
    ];

    protected $casts = [
        'last_tg_media_group_messages_ids' => 'array'
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
     * @param int $messageId
     * @param string|array $lastTgMessageId
     * @return bool
     */
    public function saveLastMessage(int $messageId, string|array $lastTgMessageId): bool
    {
        if (is_array($lastTgMessageId)) {
            $this->last_tg_media_group_messages_ids = $lastTgMessageId;
        } else {
            $this->last_tg_message_id = $lastTgMessageId;
        }
        $this->last_message_id = $messageId;

        return $this->save();
    }

    /**
     * @return void
     */
    public function confirmLastInput(): void
    {
        if($this->waitConfirmation){
            $lastInput = $this->waitConfirmation->input;
            $field = $this->lastQuestion->wait_input;
            $this->{$field} = $lastInput;
            $this->save();
            $this->waitConfirmation()->delete();
        }
    }

}

