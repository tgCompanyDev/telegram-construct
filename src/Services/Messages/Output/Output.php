<?php

namespace Valibool\TelegramConstruct\Services\Messages\Output;

use GuzzleHttp\Client;
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Valibool\TelegramConstruct\Models\File\TgConstructAttachment;

abstract class Output
{
    public int|null $lastTgMessageId = null;
    protected string $token;
    protected string $text;
    protected array $mediaGroup = [];
    protected OutputButtons $buttons;
    private Client $client;
    public bool $status;
    public bool $deletePrevMessage = false;
    public string|null $message_id = null;
    public TgConstructAttachment|null $photo = null;
    public array|null $mediaGroupMessagesIds = null;
    public string|null $date = null;
    public int|null $errorCode = null;
    public string|null $errorMessage = null;

    const TG_API_URL = 'https://api.telegram.org/bot';

    public function __construct($token)
    {
        $this->token = $token;
        $this->client = new Client();
    }

    abstract function setText(string $text): string;

    abstract function setButtons(OutputButtons $buttons): self;

    abstract function setPhoto($photo);


    abstract function sendMessage(string $chatId);

    public function sendMediaGroup(string $chatId): self
    {
        $response = $this->client->get(
            self::TG_API_URL . $this->token . '/sendMediaGroup',
            [
                "multipart" => $this->mediaGroup,
                'http_errors' => false,
                'query' => [
                    'chat_id' => $chatId,
                ],
                'verify' => false
            ]
        );
        $data = json_decode($response->getBody()->getContents(), true);
        $this->setResponse($data);

        return $this;
    }

    public function asyncDeleteLastMessageAndSendNew(string $chatId, int $messageId, $queryToSend): self
    {

        $response = Http::pool(function (Pool $pool) use ($queryToSend, $chatId, $messageId) {
            $pool->as('delete')->get(self::TG_API_URL . $this->token . '/deleteMessage', [
                'chat_id' => $chatId,
                'message_id' => $messageId,
            ]);
            $pool->as('newMessage')->get(self::TG_API_URL . $this->token . '/sendMessage',
                $queryToSend['query']
            );
        });
        $data =  json_decode($response['newMessage']->getBody()->getContents(),true);
        $this->setResponse($data);

        return $this;
    }
    public function sendTextMessage(string $chatId): self
    {

        $query = [
            'http_errors' => false,
            'query' => [
                'chat_id' => $chatId,
                'text' => $this->text,
                'parse_mode' => 'HTML',
//                    'parse_mode' => 'MarkdownV2',
                'items_in_row' => '1',
                'reply_markup' => $this->buttons->keyboard ?? null,
            ],
        ];

        if($this->deletePrevMessage && $this->lastTgMessageId){
            $this->asyncDeleteLastMessageAndSendNew($chatId, $this->lastTgMessageId,$query );
        } else {
            $response = $this->client->get(
                self::TG_API_URL . $this->token . '/sendMessage',
                [
                    'http_errors' => false,
                    'query' => [
                        'chat_id' => $chatId,
                        'text' => $this->text,
                        'parse_mode' => 'HTML',
//                    'parse_mode' => 'MarkdownV2',
                        'items_in_row' => '1',
                        'reply_markup' => $this->buttons->keyboard ?? null,
                    ],
                    'verify' => false
                ]
            );
            $data = json_decode($response->getBody()->getContents(), true);
            $this->setResponse($data);

        }

        return $this;
    }

    public function deleteMessage(string $chatId, int $messageId): self
    {
        $response = $this->client->get(
            self::TG_API_URL . $this->token . '/deleteMessage',
            [
                'http_errors' => false,
                'query' => [
                    'chat_id' => $chatId,
                    'message_id' => $messageId,
                ],
                'verify' => false
            ]
        );

        return $this;
    }

    public function deleteMessages(string $chatId, array $messageIds): self
    {
        $response = $this->client->get(
            self::TG_API_URL . $this->token . '/deleteMessages',
            [
                'http_errors' => false,
                'query' => [
                    'chat_id' => $chatId,
                    'message_ids' => $messageIds,
                ],
                'verify' => false
            ]
        );
        $data = json_decode($response->getBody()->getContents(), true);

        return $this;
    }

    public function sendPhoto($chatId): self
    {
        $response = $this->client->get(
            self::TG_API_URL . $this->token . '/sendPhoto',
            [
                'multipart' => [
                    [
                        'name' => 'photo',
                        'contents' => fopen(Storage::getConfig()['root'] . '/' . $this->photo->disk . '/' . $this->photo->physicalPath(), 'r'),
                    ],
                ],
                'http_errors' => false,
                'query' => [
                    'chat_id' => $chatId,
                    'caption' => $this->text ?? null,
                    'reply_markup' => $this->buttons->keyboard ?? null,
                ],
                'verify' => false
            ]
        );
        $data = json_decode($response->getBody()->getContents(), true);
        $this->setResponse($data);

        return $this;
    }

    /**
     * @param array $data
     * @return void
     */
    private function setResponse(array $data): void
    {
        $this->status = $data['ok'];

        if (isset($data['error_code'])) {

            $this->setErrorResponse($data['error_code'], $data['description']);

        } else {
            if (isset($data['result']['message_id'])) {
                $this->message_id = $data['result']['message_id'];
                $this->date = $data['result']['date'];
            } else {
                $this->setMessagesMediaGroup($data['result']);
            }
        }
    }

    /**
     * @param array $messages
     * @return void
     */
    private function setMessagesMediaGroup(array $messages): void
    {
        foreach ($messages as $message) {
            $this->mediaGroupMessagesIds[] = $message['message_id'];
        }
    }

    /**
     * @param $code
     * @param $message
     * @return void
     */
    private function setErrorResponse($code, $message): void
    {
        $this->errorCode = $code;
        $this->errorMessage = $message;
    }

}
