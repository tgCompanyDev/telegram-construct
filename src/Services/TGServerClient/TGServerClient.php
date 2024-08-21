<?php

namespace Valibool\TelegramConstruct\Services\TGServerClient;

use danog\MadelineProto\API;
use Valibool\TelegramConstruct\Models\Post;
use Valibool\TelegramConstruct\Services\Response\ResponseService;

class TGServerClient
{

    private API $MadelineProto;

    public function initSession(){
        $this->MadelineProto = new API('session.madeline');
        $this->MadelineProto->start();
    }

    public function auth()
    {
        $this->initSession();
        return $this->MadelineProto->getSelf();
    }

    public function logout()
    {
        $this->initSession();
        $this->MadelineProto->logout();
    }

    public function getMessageViews($postId)
    {
        $post = Post::find($postId);
        if($post){
           return ResponseService::success($post);
//            $this->initSession();
//            $this->MadelineProto->logout();
        }
        return ResponseService::notFound();

    }
}
