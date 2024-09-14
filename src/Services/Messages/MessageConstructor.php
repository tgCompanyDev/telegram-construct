<?php

namespace Valibool\TelegramConstruct\Services\Messages;

use Illuminate\Support\Facades\Storage;
use Valibool\TelegramConstruct\Models\Bot;
use Valibool\TelegramConstruct\Services\Messages\Output\Output;
use Valibool\TelegramConstruct\Services\Messages\Output\OutputButtons;
use Valibool\TelegramConstruct\Services\Messages\Output\OutputMessage;

class MessageConstructor
{
    private Message $message;
    private Output $output;

    public function __construct(Message $message, Bot $bot)
    {
        $this->message = $message;
        $this->output = new OutputMessage($bot->token);
    }

    public function constructOutputMessage(): Output
    {
        $this->output->setText($this->message->text);
        $this->setAttachments();

        if ($keyboard = $this->convertButtons()) {
            $this->output->setButtons($keyboard);
        }

        return $this->output;
    }

    public function setAttachments(): void
    {
        if($this->message->attachments ){
            if(count($this->message->attachments) >= 2){
                $mediaGroup = $this->mediaGroupFormat();
                $this->output->setMediaGroup($mediaGroup);

            }else{
                $this->output->setPhoto($this->message->attachments->first());
            }
        }
    }

    public function formatMimeAttachment($mime) : string
    {
        switch ($mime) {
            case str_contains($mime,'image'):
                return 'photo';
                break;
            case str_contains($mime,'video'):
                return 'video';
                break;
        }
    }

    public function mediaGroupFormat() : array
    {
        $mediaGroup = [];
        $fields = [];
        $files = [];
        foreach ($this->message->attachments as $key => $attachment) {
            $type = $this->formatMimeAttachment($attachment->mime);

            $fields[$key]= [
                'type' => $type,
                'media' => 'attach://'.$attachment->name.'.'. $attachment->extension,
            ];

            if($this->message->text){
                if ($key === 0) {
                    $fields[$key]['caption'] = $this->message->text;
                }
            }

            $files[] = [
                'name'=>$attachment->name.'.'. $attachment->extension,
                'contents'=>fopen(Storage::getConfig()['root'] . '/' . $attachment->disk . '/' . $attachment->physicalPath(), 'r')
            ];
        }
        $mediaGroup[] = [
            'name' => 'media',
            'contents' => json_encode($fields),
        ];

        return array_merge($mediaGroup,$files);
    }

    public function convertButtons(): ?OutputButtons
    {
        $outputMessageButtons = $this->message->buttons;
        $keyboard = null;
        if ($outputMessageButtons && $outputMessageButtons->count()) {
            $buttons = [];
            $countInRow = 1;
            $i = 0;

            $keyboard = new OutputButtons(
                $this->message->keyboard->resize_keyboard,
                $this->message->keyboard->one_time_keyboard
            );

            foreach ($outputMessageButtons as $button) {
                if (isset($button['callback_data'])) {
                    $buttons[] = ['text' => $button['text'], 'callback_data' => $button['callback_data']];
                }
            }
            $keyboard->row($buttons);
        }
        return $keyboard;
    }

}
