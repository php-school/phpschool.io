## Install

You will need `composer`, `gulp` and `PHP7`

```shell
composer install
npm install
```


## Run
```shell
gulp
```

Then navigate to `localhost:3000` !

All SASS modifications will be auto inject live to the browser with ![Browsersync](https://www.browsersync.io)
Any changes to `.phtml` will cause the browser to reload

Gulp will boot up a PHP server on port `8000` by default and will use the default system PHP version.

## Build CSS

```shell
gulp sass
```

## Build SVG's
```shell
gulp svg
```


All pages are cached the first time you hit them so any HTML changes will not loaded unless you clear the cache, or enable dev mode.

### Clear cache

```shell
php bin/app clear-cache
```


## Dev Mode

You can enable dev mode by creating `app/dev-config.php` which should return an array of config. This config takes precedence over `app/config.php`. They will be merged together. This file is ignored from git.
We provide a sample file which disables caching, you can use it by copying it: `cp app/dev-config.php.dist app/dev-config.php`
