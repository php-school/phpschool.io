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
docker-compose up -d
```

### Create DB Scheme
```shell
docker compose exec php composer app:db:update
```

### Generate Blog
```shell
docker compose exec php composer app:gen:blog
```

Then navigate to `http://localhost` !

Pages are cached on first view.
If you need to clear the cache, run `docker compose exec php composer app:cc`.

## Build CSS

```shell
gulp sass
```

## Building CSS for cloud

The cloud styles are built using the Tailwind CSS tool and use the builtin watcher:

```shell
composer tw
```

## Build SVG's
```shell
gulp svg
```

### View cache keys

```shell
docker-compose exec redis redis-cli keys '*'
```

### Clear cache

```shell
docker compose exec php composer app:cc
```

## Deploy

You will need capistrano installed and SSH access to the production server.

```shell
cap production deploy
```

## Production deploy requisites

Letsencrypt certs should be setup and located in `/etc/letsencrypt`

Make sure `.env` file exists in `shared` folder. You can use `env.dist` as an example.
