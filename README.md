## Install

You will need `composer`, `node` and `docker`.

```shell
npm install
npm run build
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

### Import DB
```shell
docker-compose exec -T db mysql -uroot phpschool -proot < phpschool.sql
```

### Generate Blog
```shell
docker compose exec php composer app:gen:blog
```

Then navigate to `http://localhost` !

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

You will also need to symlink the image directory:

```shell
ln -s ../../assets/img/cloud public/img/cloud
````

## For GitHub login

Add `127.0.0.1 www.phpschool.local` to `/etc/hosts`

Create a GitHub oauth App:

Application Name: PHP School Local
Homepage: http://www.phpschool.local
Authorization Callback URL: http://www.phpschool.local/student-login: 

Take the client secret and client ID and place them in your `.env` file under the keys: `GITHUB_CLIENT_ID` & `GITHUB_CLIENT_SECRET`.

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
