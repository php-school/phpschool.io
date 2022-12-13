## Install

You will need `composer`, `node` and `docker`.

```shell
npm install
cp .env.dist .env
docker-compose build
```

## Run
```shell
docker-compose up -d
docker compose exec php composer install
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

You can disable the cache by setting `CACHE.FPC.ENABLE` to `false` in your `.env` file.

## Build CSS & JS

This needs to be done for the main website (non cloud) to run in development mode.

```shell
npm run build
```

## Building CSS & JS for cloud dev

The cloud styles and JS are built using `vite.js` and therefore has a dev/watcher mode with hot/live reloading.

Run:

```shell
npm run dev
```

### View cache keys

```shell
docker-compose exec redis redis-cli keys '*'
```

### Clear cache

```shell
docker-compose exec php composer app:cc
```

## Deploy

You will need capistrano installed and SSH access to the production server.

```shell
cap production deploy
```

## Production deploy requisites

Letsencrypt certs should be setup and located in `/etc/letsencrypt`

Make sure `.env` file exists in `shared` folder. You can use `env.dist` as an example.
