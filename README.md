# Voting PHP Registry

Here you'll find an implementation of a meta-belarus voting registry implemented in PHP.

## Configuring

In your env file specify `DB=mysql://root:secret@localhost/voting-registry-php` then run lumen
with `php -S localhost:8000 -t public`

## Data Modeling

We're using atk4/data (https://github.com/atk4/data, https://agile-data.readthedocs.io/en/develop/index.html),
to simplify database interaction. You can easily define 'Models' inside 'app/Models' folder. Simply duplicate
existing file and adopt.

After you have added a new model, update Http/Controllers/Migrator and invoke /migrate endpoint. This will
create table in MySQL.

## Attaching API endpoints


## Specs

Swagger: `/specs/swagger/index.html`  
Redoc: `/specs/redoc/index.html`