## add to project composer.json
   - "minimum-stability": "dev",
   - "repositories": [ { "type" : "vcs", "url" : "https://github.com/Vlad06013/telegram-construct.git" } ]
## for dev from local folder "packages"

   - "repositories": [ { "type" : "path", "url" : "packages/telegram-construct" } ]
## For access bots webhook must be write request header X-Telegram-Bot-Api-Secret-Token as secret_token 
