<?php

return [
    'param' => env('EXAMPLE_PARAM', 100),
    'models_to_users_inputs' => [
        "TgUser" => \Valibool\TelegramConstruct\Models\TgUser::class,
    ],
];
