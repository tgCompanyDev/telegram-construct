## add to project composer.json
   - "minimum-stability": "dev",
   - "repositories": [ { "type" : "vcs", "url" : "https://github.com/tgCompanyDev/telegram-construct.git" } ]
## for dev from local folder "packages"

   - "repositories": [ { "type" : "path", "url" : "packages/telegram-construct" } ]
## For access bots webhook must be write request header X-Telegram-Bot-Api-Secret-Token as secret_token 

## Publish construct config 
    -  php artisan vendor:publish --provider="Valibool\TelegramConstruct\Providers\TelegramConstructServiceProvider"

## Publish Swagger config 
    - php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider"

## Add to config/l5-swagger.php documentations/default/annotations
   - base_path('packages/telegram-construct/src/Http') - for local package
   - base_path('vendor/valibool/telegram-construct/src/Http')- for composer package


