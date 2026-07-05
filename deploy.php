<?php

namespace Deployer;

require 'recipe/common.php';

set('application', 'phpschool');
set('repository', 'git@github-phpschool:php-school/phpschool.io.git');
set('branch', 'master');

set('shared_files', ['.env', 'public/workshops.json']);
set('shared_dirs', ['var/logs', 'public/uploads']);
set('writable_dirs', ['var/logs', 'public/uploads']);
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
    run('cd {{release_path}} && {{bin/php}} vendor/bin/doctrine orm:schema-tool:update -f');
})->desc('Update the Doctrine schema');

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
    'deploy:publish',
])->desc('Deploy phpschool.io');

// Unlock automatically if the deploy fails.
after('deploy:failed', 'deploy:unlock');
