require 'yaml'

set :application, 'phpschool'
set :repo_url, 'git@github.com:php-school/phpschool.io.git'
set :branch, 'master'
set :deploy_to, '/var/www/phpschool.io/'
set :pty, true
set :log_level, :debug
set :keep_releases, 2

set :linked_dirs, %w{var public/uploads}
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
          execute('cachetool', 'opcache:reset', '--fcgi=/var/run/php/php7.1-fpm.sock')
      end
    end
  end

  task :generate_blog do
      on roles(:web) do |host|
        within release_path do
            execute('php', 'bin/app', 'generate-blog')
        end
      end
    end

  after "deploy:finished", "deploy:schema_update"
  after "deploy:schema_update", "deploy:clear_cache"
  after "deploy:clear_cache", "deploy:generate_blog"
end
