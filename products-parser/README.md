## Products Parser Challenge

Products Parser Challenge é uma API REST para informações alimentícias usando como base a api [Open Food Facts](https://br.openfoodfacts.org/data)

This is a challenge by [Coodesh](https://coodesh.com/)

## Tecnologias
 - PHP
 - Framework Laravel
 - Mysql
 - Docker
 - API Key

 ## Instruções
 Build de imagens criadas <br />
   - docker-compose build
 Subir container docker <br />
   - docker-compose up
 Adiconar enviroment file <br />
   - docker-compose exec php cp .env.example .env
 Gerar chave da aplicação <br />
   - docker-compose exec php php artisan key:generate
 Rodar migrations no banco <br />
   - docker-compose exec php php artisan migrate:fresh --seed
 Rodar link da pasta storage e adionar permissoes  <br />
   - docker-compose exec php php artisan storage:link
   - docker-compose exec php chmod -R 777 /var/www/html/storage 
 
 Foi adicionado a configuração para verificar a cada minuto se existem crons para serem executadas <br />
 Laravel fornece uma ferramenta de Schedule e neste projeto foi definido para 1 vez ao dia as 00:00 que normalmente é fora do horario comercial em sistemas reais <br />
 ## Crontab - Rodar a cron instantaneamente para utilizar a API com registros no banco
    - docker-compose exec php php artisan product:import
    ----    * * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
            - - - - -
            | | | | |
            | | | | ----- Day of week (0 - 7) (Sunday=0 or 7)
            | | | ------- Month (1 - 12)
            | | --------- Day of month (1 - 31)
            | ----------- Hour (0 - 23)
            ------------- Minute (0 - 59)
 ## Documentação da API
 Arquivo api gerado no formato openapi: 3.0.0 dentro de docs/api.yml , pode ser importado dentro do Postman ou a ferramenta desejada. <br />
 API KEY disponivel dentor do .env .example <br />
