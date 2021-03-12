# Voting PHP Registry

Here you'll find an implementation of a meta-belarus voting registry implemented in PHP.

## Run

Just run `make up` and open the page `http://localhost`

## Data Modeling

We're using atk4/data (https://github.com/atk4/data, https://agile-data.readthedocs.io/en/develop/index.html),
to simplify database interaction. You can easily define 'Models' inside 'app/Models' folder. Simply duplicate
existing file and adopt.

After you have added a new model, update Http/Controllers/Migrator and invoke /migrate endpoint. This will
create table in MySQL.

## Attaching API endpoints


