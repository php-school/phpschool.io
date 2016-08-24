require 'yaml'
lock '3.4.0'

set :application, 'phpschool'
set :repo_url, 'git@github.com:php-school/phpschool.io.git'
set :branch, 'master'
set :deploy_to, '/var/www/html'
set :pty, true
set :log_level, :debug
set :keep_releases, 2

set :linked_dirs, %w{var}
set :linked_files, %w{public/workshops.json .env}
namespace :deploy do

  task :schema_update do
    on roles(:web) do |host|
      within release_path do
        execute('php', 'vendor/bin/doctrine', 'orm:schema-tool:update', '-f')
      end
    end
  end

  task :clear_cache do
    on roles(:web) do |host|
      within release_path do
          execute('php', 'bin/app', 'clear-cache')
      end
    end
  end

  after "deploy:finished", "deploy:schema_update"
  after "deploy:schema_update", "deploy:clear_cache"
end
