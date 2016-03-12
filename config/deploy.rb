# config valid only for current version of Capistrano
lock '3.4.0'

set :composer_install_flags, '--no-dev --no-interaction --quiet --optimize-autoloader'
set :application, 'phpschool'
set :repo_url, 'git@github.com:php-school/phpschool.io.git'
set :branch, 'master'
set :deploy_to, '/var/www/html'
set :pty, true
set :log_level, :info

set :linked_dirs, %w{cache}
namespace :deploy do

  task :clear_cache do
    on roles(:web) do |host|
      within release_path do
          execute "php #{release_path}/bin/app clear-cache"
      end
    end
  end

  before 'deploy:updated', 'deploy:clear_cache'
end
