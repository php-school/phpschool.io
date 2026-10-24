<?php

namespace Deployer;

require 'recipe/common.php';

set('application', 'phpschool');
set('repository', 'git@github-phpschool:php-school/phpschool.io.git');
set('branch', 'master');

set('shared_files', ['.env', 'public/workshops.json']);
set('shared_dirs', ['var/logs', 'public/uploads']);
set('writable_dirs', ['var/logs', 'public/uploads']);
set('writable_use_sudo', true);
set('keep_releases', 5);

set('git_tty', false);
set('allow_anonymous_stats', false);

host('imp')
    ->setHostname('100.70.13.88')
    ->setRemoteUser('deploy')
    ->set('deploy_path', '/var/www/phpschool.io');

// Install composer dependencies for the new release.
task('deploy:vendors', function () {
    run('cd {{release_path}} && {{bin/composer}} install --no-dev --optimize-autoloader --no-interaction --no-progress');
})->desc('Install composer dependencies');

// Post-deploy application tasks (ported from the old Capistrano hooks).
task('deploy:clear-cache', function () {
    run('cd {{release_path}} && {{bin/php}} bin/app clear-cache');
})->desc('Clear the application cache');

task('deploy:schema-update', function () {
    run('cd {{release_path}} && {{bin/php}} bin/app orm:schema-tool:update -f');
})->desc('Update the Doctrine schema');

// Student code runs in these images, so they must exist before the release goes live.
task('deploy:build-runtime-images', function () {
    //exercises that expose ports run on this network, it has no internet access
    run('docker network inspect php-school-sandbox >/dev/null 2>&1 || docker network create -o com.docker.network.bridge.enable_ip_masquerade=false php-school-sandbox');
    run('cd {{release_path}} && {{bin/php}} bin/app build-runtime-images');
})->desc('Build the workshop sandbox images');

// The Vue site, prerendered into public/dist. The prerender fetches posts and events from the live API.
task('deploy:build-frontend', function () {
    run('cd {{release_path}} && npm ci --no-audit --no-fund && VITE_API_URL=https://www.phpschool.io npm run build');
})->desc('Build the frontend');

task('deploy:generate-blog', function () {
    run('cd {{release_path}} && {{bin/php}} bin/app generate-blog');
})->desc('Generate the blog');

// Full deploy flow.
task('deploy', [
    'deploy:prepare',
    'deploy:vendors',
    'deploy:clear-cache',
    'deploy:schema-update',
    'deploy:generate-blog',
    'deploy:build-runtime-images',
    'deploy:build-frontend',
    'deploy:publish',
])->desc('Deploy phpschool.io');

// Unlock automatically if the deploy fails.
after('deploy:failed', 'deploy:unlock');
