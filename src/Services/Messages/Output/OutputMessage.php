<?php

namespace Valibool\TelegramConstruct\Services\Messages\Output;

use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Valibool\TelegramConstruct\Services\Messages\MessageConstructor;

class OutputMessage
{
    protected static string $token;
    public int|null $lastTgMessageId = null;
    protected string $text;
    protected array $mediaGroup = [];
    protected OutputButtons|null $keyboard;
    public bool $status;
    public bool $deletePrevMessage = false;
    public string|null $message_id = null;
    public array|null $photo = null;
    public array|null $animation = null;
    public int|null $errorCode = null;
    public string|null $errorMessage = null;
    const TG_API_URL = 'https://api.telegram.org/bot';
    private ?OutputButtons $buttons;

    public function __construct(MessageConstructor $message, $token)
    {
        self::$token = $token;
        self::$token = $token;
        $this->text = $message->text;
        $this->buttons = $message->buttons;
        $this->setAttachments($message->attachments);
    }

    public static function client(): Client
    {
        return new Client(['base_uri' => self::TG_API_URL . self::$token . '/']);
    }

    public function sendRequest($method, $url, $body): array
    {
        $response = self::client()->request($method, $url, $body);
        $data = json_decode($response->getBody()->getContents(), true);
        $this->setResponse($data);
        return $data;
    }

    public function sendAsyncRequest($params): array
    {
        $promises = [];
        $data = [];
        foreach ($params as $key => $item) {
            $request = new \GuzzleHttp\Psr7\Request($item['method'], $item['url']);

            $promises[$key] = self::client()->sendAsync($request, $item['body']);

        }
        $results = \GuzzleHttp\Promise\Utils::settle($promises)->wait(true);

        foreach ($results as $key => $result) {
            if ($result['state'] != 'fulfilled' || !isset($result['value'])) {
                continue;
            }
            $response = $result['value'];
            $data[$key] = json_decode($response->getBody()->getContents(), true);
        }

        return $data;
    }

    public function setAttachments($attachments): self
    {
        if ($attachments) {
            if ($attachments->count() >= 2) {
                $this->mediaGroup = self::mediaGroupFormat($attachments, $this->text);
                return $this;
            }

            if(self::formatMimeAttachment($attachments->first()['mime']) == 'animation')
            {
                $this->animation = $attachments->first();
                return $this;

            }

            if(self::formatMimeAttachment($attachments->first()['mime']) == 'photo')
            {
                $this->photo = $attachments->first();
                return $this;
                
            }
        }
    }

    public function sendMessage(string $chatId): self
    {
        if ($this->mediaGroup)
            return $this->sendMediaGroup($chatId);

        if ($this->photo)
            return $this->sendPhoto($chatId);
        if ($this->animation)
            return $this->sendAnimation($chatId);

        return $this->sendTextMessage($chatId);

    }

    public static function formatMimeAttachment($mime): string
    {
        switch ($mime) {
            case str_contains($mime, 'image'):
                if(str_contains($mime, 'gif'))
                    return 'animation';
                return 'photo';
                break;
            case str_contains($mime, 'video'):
                return 'video';
                break;
        }
    }

    public static function mediaGroupFormat($attachments, string $message = null): array
    {
        if (!$attachments) {
            return [];
        }
        $mediaGroup = [];
        $fields = [];
        $files = [];

        foreach ($attachments as $key => $attachment) {

            $type = self::formatMimeAttachment($attachment['mime']);
            if (in_array($type,['photo','video','doc','audio'])) {
                $fields[$key] = [
                    'type' => $type,
                    'media' => 'attach://' . $attachment['name'] . '.' . $attachment['extension'],
                ];

                if ($message) {
                    if ($key === 0) {
                        $fields[$key]['caption'] = $message;
                    }
                }

                $files[] = [
                    'name' => $attachment['name'] . '.' . $attachment['extension'],
                    'contents' => fopen(Storage::getConfig()['root'] . '/' . $attachment['disk'] . '/' . $attachment['path'] . '/' . $attachment['name'] . '.' . $attachment['extension'], 'r')
                ];
            }
        }
        $mediaGroup[] = [
            'name' => 'media',
            'contents' => json_encode($fields),
        ];

        return array_merge($mediaGroup, $files);
    }

    public function sendMediaGroup(string $chatId): self
    {
        $body = [
            "multipart" => $this->mediaGroup,
            'http_errors' => false,
            'query' => [
                'chat_id' => $chatId,
            ],
            'verify' => false
        ];

        $this->sendRequest('GET', 'sendMediaGroup', $body);
        return $this;
    }

    public function asyncDeleteLastMessageAndSendNew(string $chatId, int $messageId, string $command, $body): self
    {
        $requests = [
            $command => [
                'method' => 'GET',
                'url' => $command,
                'body' => $body,
            ],
            'deleteMessage' => [
                'method' => 'GET',
                'url' => 'deleteMessage',
                'body' => ['query' => ['chat_id' => $chatId, 'message_id' => $messageId]],
            ],
        ];
        $data = $this->sendAsyncRequest($requests);
        $this->setResponse($data[$command]);


        return $this;
    }

    public function sendTextMessage(string $chatId): self
    {
        $body = [
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
        if ($this->deletePrevMessage && $this->lastTgMessageId) {
            $this->asyncDeleteLastMessageAndSendNew($chatId, $this->lastTgMessageId, 'sendMessage', $body);
        } else {

            $result = $this->sendRequest('GET', 'sendMessage', $body);
        }
        return $this;
    }

    public function sendPhoto($chatId): self
    {
        $body = [
            'multipart' => [
                [
                    'name' => 'photo',
                    'contents' => fopen(Storage::getConfig()['root'] . '/' . $this->photo['disk'] . '/' . $this->photo['path'] . '/' . $this->photo['name'] . '.' . $this->photo['extension'], 'r')
                ],
            ],
            'http_errors' => false,
            'query' => [
                'chat_id' => $chatId,
                'caption' => $this->text ?? null,
                'reply_markup' => $this->buttons->keyboard ?? null,
            ],
            'verify' => false
        ];
        if ($this->deletePrevMessage && $this->lastTgMessageId) {
            $this->asyncDeleteLastMessageAndSendNew($chatId, $this->lastTgMessageId, 'sendPhoto', $body);
        } else {
            $this->sendRequest('GET', 'sendPhoto', $body);
        }

        return $this;
    }

    public function sendAnimation($chatId): self
    {
        $body = [
            'multipart' => [
                [
                    'name' => 'animation',
                    'contents' => fopen(Storage::getConfig()['root'] . '/' . $this->animation['disk'] . '/' . $this->animation['path'] . '/' . $this->animation['name'] . '.' . $this->animation['extension'], 'r')
                ],
            ],
            'http_errors' => false,
            'query' => [
                'chat_id' => $chatId,
                'caption' => $this->text ?? null,
                'reply_markup' => $this->buttons->keyboard ?? null,
            ],
            'verify' => false
        ];
        if ($this->deletePrevMessage && $this->lastTgMessageId) {
            $this->asyncDeleteLastMessageAndSendNew($chatId, $this->lastTgMessageId, 'sendAnimation', $body);
        } else {
            $this->sendRequest('GET', 'sendAnimation', $body);
        }

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
            }
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
