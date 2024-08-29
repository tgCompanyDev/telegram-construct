<?php

namespace Valibool\TelegramConstruct\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="OpenApi Documentation",
 *      description="Документация Botamba",
 * )
 * @OA\Server(
 *       url="https://api.botamba.ru/",
 *       description="prod",
 *  )
 * @OA\Server(
 *      url="http://smm-planer.loc",
 *      description="local",
 * )
 * @OA\SecurityScheme(
 *      type="apiKey",
 *      in = "header",
 *      name = "Authorization",
 *      securityScheme="apiKey"
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
