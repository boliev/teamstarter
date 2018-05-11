<?php

namespace Deployer;

require 'recipe/symfony3.php';

// Project name
set('application', 'teamstarter');

// Project repository
set('repository', 'git@github.com:boliev/teamstarter.git');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true);

// Shared files/dirs between deploys
add('shared_files', [
    'app/config/parameters.yml',
]);
add('shared_dirs', [
    'var/logs',
    'web/avatars',
    'web/screes',
]);

// Writable dirs by web server
add('writable_dirs', [
    'var',
    'web/avatars',
    'web/screens',
]);
set('allow_anonymous_stats', false);
set('writable_use_sudo', true);

// Hosts

host('dev')
    ->hostname('voovle.ru')
    ->user('deploy')
    ->set('deploy_path', '/var/www/ts_dev/{{application}}');

// Tasks

task('build', function () {
    run('cd {{release_path}} && build');
});

task('disk_free', function () {
    $df = run('df -h /');
    writeln($df);
});

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

// Migrate database before symlink new release.

before('deploy:symlink', 'database:migrate');