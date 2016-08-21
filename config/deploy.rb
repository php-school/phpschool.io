require 'yaml'
lock '3.4.0'

env = YAML.load_file(File.join(File.dirname(__FILE__), 'env.yaml'));
requiredKeys = ['MYSQL_ROOT_PASSWORD', 'MYSQL_DATABASE', 'MYSQL_USER', 'MYSQL_PASSWORD', 'SEND_GRID_API_KEY']

requiredKeys.each do |key|
  if !env.include? key
     puts "Key #{key} must be set"
     exit
  end
end

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
        with MYSQL_ROOT_PASSWORD: env['MYSQL_ROOT_PASSWORD'],  MYSQL_USER: env['MYSQL_USER'], MYSQL_PASSWORD: env['MYSQL_PASSWORD'], SEND_GRID_API_KEY: env['SEND_GRID_API_KEY'] do
          execute('docker-compose', 'down', ';true')
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
