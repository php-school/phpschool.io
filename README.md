## Install

You will need `composer`, `gulp` and `docker`.

```shell
composer install
npm install
cp .env.dist .env
docker-compose build
```

## Run
```shell
gulp
```

Then navigate to `localhost:3000` !

All SASS modifications will be auto inject live to the browser with [Browsersync](https://www.browsersync.io)
Any changes to `.phtml` will cause the browser to reload.

Pages are cached on first view, but any changes to the `.phtml` files will cause the cache to be removed. If you need to clear it manually, run `docker exec php-school-fpm php bin/app clear-cache`.

Gulp will boot up the docker infrastructure automatically, which includes PHP 7, Nginx & Redis.

## Build CSS

```shell
gulp sass
```

## Build SVG's
```shell
gulp svg
```

### View cache keys

```shell
docker exec php-school-redis redis-cli keys '*'
```

### Clear cache

```shell
docker exec php-school-fpm php bin/app clear-cache
```

## Deploy

You will need capistrano installed and SSH access to the production server.

```shell
gulp deploy
```

## Production deploy requisites

Letsencrypt certs should be setup and located in `/etc/letsencrypt`

Make sure `.env` file exists in `shared` folder. You can use `env.dist` as an example.
