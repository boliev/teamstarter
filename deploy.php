<?php

namespace Deployer;

use Symfony\Component\Console\Input\InputOption;

require 'recipe/symfony4.php';

// Project name
set('application', 'teamstarter');

// Project repository
set('repository', 'git@github.com:boliev/teamstarter.git');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true);

// Shared files/dirs between deploys
add('shared_files', [
    '.env.local',
]);
add('shared_dirs', [
    'var/log',
    'public/avatars',
    'public/screens',
]);

// Writable dirs by web server
add('writable_dirs', [
    'var',
    'public/avatars',
    'public/screens',
]);
set('allow_anonymous_stats', false);
set('writable_use_sudo', true);

// Hosts

host('dev')
    ->hostname('voovle.ru')
    ->stage('feature/#59-design')
    ->user('deploy')
    ->set('deploy_path', '/var/www/ts_dev4/{{application}}');

// Tasks

task('build', function () {
    run('cd {{release_path}} && build');
});

task('search:projects:index', function () {
    run('cd {{release_path}} && bin/console projects:index --env=prod');
});

task('search:specialists:index', function () {
    run('cd {{release_path}} && bin/console specialists:index --env=prod');
});

task('disk_free', function () {
    $df = run('df -h /');
    writeln($df);
});

option('u', null, InputOption::VALUE_REQUIRED, 'user email.');
option('n', null, InputOption::VALUE_REQUIRED, 'number of projects.');
// dep test:projects:add-to-user dev --u voff.web+0509@gmail.com --n 1
task('test:projects:add-to-user', function () {
    // For option
    $tag = null;
    if (input()->hasOption('u')) {
        $user = input()->getOption('u');
    }
    if (input()->hasOption('n')) {
        $number = input()->getOption('n');
    }

    $df = run(sprintf('cd {{release_path}} && bin/console test:projects:add-to-user %s %d Published --env=prod', $user, $number));
    writeln($df);
});

// dep test:specialists:add dev --n 1
task('test:specialists:add', function () {
    // For option
    $number = 1;
    if (input()->hasOption('n')) {
        $number = input()->getOption('n');
    }

    $df = run(sprintf('cd {{release_path}} && bin/console test:specialists:add %d --env=prod', $number));
    writeln($df);
});

task('user:make:admin', function () {
    // For option
    if (input()->hasOption('u')) {
        $user = input()->getOption('u');
    }

    $df = run(sprintf('cd {{release_path}} && php bin/console fos:user:promote %s ROLE_ADMIN --env=prod', $user));
    writeln($df);
});

task('user:remove:admin', function () {
    // For option
    if (input()->hasOption('u')) {
        $user = input()->getOption('u');
    }

    $df = run(sprintf('cd {{release_path}} && php bin/console fos:user:promote %s ROLE_ADMIN --env=prod', $user));
    writeln($df);
});

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

// Migrate database before symlink new release.

before('deploy:symlink', 'database:migrate');
