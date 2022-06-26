# Laravel multi-auth example
Here I cover 3type of user functionality


## Video Tutorial

 **[ ▶️ Laravel multi-auth Tutorial](https://www.youtube.com/watch?v=KBA22pSeoe4)**

## Setup

Download or clone this repo



Install all dependency required by Laravel.
```shell
$ composer install
```

Generate app key, configure `.env` file and do migration.
```shell
# create copy of .env
$ cp .env.example .env

# create Laravel key
$ php artisan key:generate
```

Next, add your database credentials in `.env` file and then run migrations.
```shell
# run migration
$ php artisan migrate --seed
```
