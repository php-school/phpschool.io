# config valid only for current version of Capistrano
lock '3.4.0'

set :composer_install_flags, '--no-dev --no-interaction --quiet --optimize-autoloader'
set :application, 'phpschool'
set :repo_url, 'https://github.com/php-school/php-school.github.io'
set :branch, 'fw-me-up'
set :deploy_to, '/var/www/html'
set :pty, true
