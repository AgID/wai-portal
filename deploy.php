<?php
namespace Deployer;

require 'recipe/common.php';

// Project name
set('application', 'Web Analytics Italia');

// Project repository
set('repository', 'git@github.com:teamdigitale/piwik-onboarding.git');

// Shared files/dirs between deploys
set('shared_files', []);
set('shared_dirs', ['containers/data']);

// Writable dirs by web server
set('writable_dirs', [
    'bootstrap/cache',
    'containers/data',
    'storage',
    'storage/app',
    'storage/app/public',
    'storage/framework',
    'storage/framework/cache',
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/logs',
]);

set('default_stage', 'staging');
set('allow_anonymous_stats', false);
set('git_tty', true);
set('cleanup_use_sudo', true);
set('composer_options', '{{composer_action}} --verbose --prefer-dist --no-progress --no-interaction --optimize-autoloader');

if (file_exists('hosts.yml')) {
    // Hosts
    inventory('hosts.yml');
} else {
    $host_staging = getenv('HOST_STAGING');
    $host_staging_user = getenv('HOST_STAGING_USER');
    $host_staging_deploy_path = getenv('HOST_STAGING_DEPLOY_PATH');
    $host_production = getenv('HOST_PRODUCTION');
    $host_production_user = getenv('HOST_PRODUCTION_USER');
    $host_production_deploy_path = getenv('HOST_PRODUCTION_DEPLOY_PATH');

    // Host staging
    host($host_staging)
        ->user($host_staging_user)
        ->set('http_user', $host_staging_user)
        ->set('env', [
            'APP_ENV' => 'staging',
        ])
        ->set('deploy_path', $host_staging_deploy_path)
        ->forwardAgent(true)
        ->multiplexing(true)
        ->addSshOption('UserKnownHostsFile', '/dev/null')
        ->addSshOption('StrictHostKeyChecking', 'no')
        ->stage('staging');

    // Host production
    host($host_production)
        ->user($host_production_user)
        ->set('http_user', $host_production_user)
        ->set('env', [
            'APP_ENV' => 'production',
        ])
        ->set('deploy_path', $host_production_deploy_path)
        ->forwardAgent(true)
        ->multiplexing(true)
        ->addSshOption('UserKnownHostsFile', '/dev/null')
        ->addSshOption('StrictHostKeyChecking', 'no')
        ->stage('production');
}

/**
 * Copy build.properties file tasks
 */
desc('Copy build.properties file');
task('deploy:copy_properties', function () {
    $output = run('cp -v {{deploy_path}}/properties/build.properties {{deploy_path}}/current/env/build.properties');
    writeln('<info>' . $output . '</info>');
});

/**
 * Copy nginx conf file tasks
 */
desc('Copy nginx conf file');
task('deploy:copy_nginx_application_conf', function () {
    $output = run('cp -v {{deploy_path}}/confs/nginx_application.conf {{deploy_path}}/current/containers/nginx/conf/application.conf');
    writeln('<info>' . $output . '</info>');
    $output = run('cp -v {{deploy_path}}/confs/nginx_matomo.conf {{deploy_path}}/current/containers/nginx/conf/matomo.conf');
    writeln('<info>' . $output . '</info>');
});

/**
 * Build tasks
 */
desc('Build app');
task('deploy:build', function () {
    $output = run('if [ -f {{deploy_path}}/current/bin/phing ]; then cd {{deploy_path}}/current; bin/phing build; fi', ['tty' => true]);
    writeln('<info>' . $output . '</info>');
});

// Tasks

desc('Deploy Web Analytics Italia');
task('deploy', [
    'deploy:info',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'deploy:writable',
    'deploy:vendors',
    'deploy:clear_paths',
    'deploy:symlink',
    'deploy:copy_properties',
    'deploy:copy_nginx_application_conf',
    'deploy:build',
    'deploy:unlock',
    'cleanup',
    'success'
]);

// [Optional] If deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');
