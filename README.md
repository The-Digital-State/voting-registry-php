Run `make up` and open the page `http://localhost`

Run `make down` to down all docker containers

Run `make exec` to run shell in container

### inside php container

Run `php artisan migrate:fresh` to drop all tables and run all migrations

Run `php artisan db:seed` to seed the database with records

Run `./vendor/bin/phpunit` to run tests

## Specs OPEN API

Swagger: `http://localhost/specs/swagger/index.html`  
Redoc: `http://localhost/specs/redoc/index.html`