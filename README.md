## Install

You will need `composer`, `gulp` and `docker`.

```shell
composer install
npm install
```

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


## Dev Mode

You can enable dev mode by creating `app/dev-config.php` which should return an array of config. This config takes precedence over `app/config.php`. They will be merged together. This file is ignored from git.
We provide a sample file which disables caching, you can use it by copying it: `cp app/dev-config.php.dist app/dev-config.php`

## Deploy

You will need capistrano installed and SSH access to the production server.

```shell
gulp deploy
``

## Booting docker container for production

Letsencrypt certs should be setup and located in `/etc/letsencrypt`

```shell
sudo docker-compose -f docker-compose.yml -f docker-compose-prod.yml up -d
```