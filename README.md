## Install

You will need `composer`, `gulp` and `docker`.

```shell
composer install
npm install
```

## Setup config

```shell
cp app/local-config.php.dist app/local-config.php
```

This file should specify the database details, you can also overwrite any config from the main `app/config.php` file.
Database username and password is specified at `config -> doctrine -> connection`, the keys `username` and `password` are required.

On the first `gulp` run the docker containers will be created. The username and password it uses is sepcified in the `project.env` file, so if you want to to change the username and password you should change it there before the first run and you should also update `app/local-config.php`.

## Run
```shell
gulp
```

Then navigate to `localhost:3000` !

All SASS modifications will be auto inject live to the browser with [Browsersync](https://www.browsersync.io)
Any changes to `.phtml` will cause the browser to reload.

Pages are cached on first view, but any changes to the `.phtml` files will cause the cache to be removed.

Gulp will boot up the docker infrastructure automatically, which includes PHP 7, Nginx & Redis.

## Build CSS

```shell
gulp sass
```

## Build SVG's
```shell
gulp svg
```

All pages are cached the first time you hit them so any HTML changes will not loaded unless you clear the cache, or enable dev mode.

### View cache keys

```shell
docker exec php-school-redis redis-cli keys '*'
```

### Clear cache

```shell
docker exec php-school-fpm php bin/app clear-cache
```


## Disable Caching

Check `app/local-config.php` and update `config -> enablePageCache`.

## Deploy

You will need capistrano installed and SSH access to the production server.

```shell
gulp deploy
```

## Booting docker container for production

Letsencrypt certs should be setup and located in `/etc/letsencrypt`

```shell
sudo docker-compose -f docker-compose.yml -f docker-compose-prod.yml up -d
```
