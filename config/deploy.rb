# config valid only for current version of Capistrano
lock '3.4.0'

set :composer_install_flags, '--no-dev --no-interaction --quiet --optimize-autoloader --ignore-platform-reqs'
set :application, 'phpschool'
set :repo_url, 'git@github.com:php-school/phpschool.io.git'
set :branch, 'master'
set :deploy_to, '/var/www/html'
set :pty, true
set :log_level, :debug
set :keep_releases, 2

set :linked_dirs, %w{.docker/mysql}
set :linked_files, %w{public/workshops.json}
namespace :deploy do

  task :setup_container do
    on roles(:web) do |host|
      within release_path do
        execute('docker-compose', 'stop', ';true')
        execute('docker', 'rm', '-f', '`docker ps -aq`', ';true')
        execute('docker-compose', 'build')
        execute('docker-compose', '-f', 'docker-compose.yml', '-f', 'docker-compose-prod.yml', 'up', '-d')
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

  #before 'deploy:finished', 'deploy:clear_cache'
  #after 'deploy:clear_cache', 'deploy:build_api_docs'

  after "deploy:finished", "deploy:setup_container"
end
