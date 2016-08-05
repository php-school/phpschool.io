# config valid only for current version of Capistrano
lock '3.4.0'

if ENV['PHPSCHOOL_DB_USER'].nil?
  puts 'PHPSCHOOL_DB_USER not set'
  exit
end

if ENV['PHPSCHOOL_DB_PASS'].nil?
  puts 'PHPSCHOOL_DB_PASS not set'
  exit
end

if ENV['PHPSCHOOL_ROOT_DB_PASS'].nil?
  puts 'PHPSCHOOL_ROOT_DB_PASS not set'
  exit
end

set :db_user, ENV['PHPSCHOOL_DB_USER']
set :db_pass, ENV['PHPSCHOOL_DB_PASS']
set :db_root_pass, ENV['PHPSCHOOL_ROOT_DB_PASS']

set :composer_install_flags, '--no-dev --no-interaction --quiet --optimize-autoloader --ignore-platform-reqs'
set :application, 'phpschool'
set :repo_url, 'git@github.com:php-school/phpschool.io.git'
set :branch, 'master'
set :deploy_to, '/var/www/html'
set :pty, true
set :log_level, :debug
set :keep_releases, 2

set :linked_dirs, %w{.docker/mysql}
namespace :deploy do

  task :setup_container do
    on roles(:web) do |host|
      within release_path do
        with MYSQL_ROOT_PASSWORD: fetch(:db_root_pass),  MYSQL_USER: fetch(:db_user), MYSQL_PASSWORD: fetch(:db_pass) do
          execute('docker-compose', 'stop', ';true')
          execute('docker', 'rm', '-f', '`docker ps -aq`', ';true')
          execute('docker-compose', 'build')
          execute('docker-compose', '-f', 'docker-compose.yml', '-f', 'docker-compose-prod.yml', 'up', '-d')
       end
      end
    end
  end

  task :schema_update do
    on roles(:web) do |host|
      within release_path do
        execute('sleep', '5') #db is not usually booted
        execute('docker', 'exec', 'php-school-fpm', 'vendor/bin/doctrine', 'orm:schema-tool:update', '-f')
      end
    end
  end

  task :clear_cache do
    on roles(:web) do |host|
      within release_path do
          execute "docker exec php-school-fpm php bin/app clear-cache"
      end
    end
  end

  task :build_api_docs do
    on roles(:web) do |host|
      within release_path do
          execute "docker exec php-school-fpm php bin/app generate-docs"
      end
    end
  end


  after "deploy:finished", "deploy:setup_container"
  after "deploy:setup_container", "deploy:schema_update"
  after "deploy:schema_update", "deploy:clear_cache"
  after "deploy:clear_cache", "deploy:build_api_docs"
end
