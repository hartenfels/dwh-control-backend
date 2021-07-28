#!/usr/bin/env php
<?php
// Script to initialize the development docker-compose setup.
// This doesn't start the containers, you gotta do that first,
// e.g. via `docker-compose up -d`. Will purge existing data.

function status($message) {
    echo PHP_EOL, '=> ', $message, PHP_EOL, PHP_EOL;
}

function run($command) {
    status($command);
    passthru($command, $result);
    if ($result !== 0) {
        die("Command failed with code $result" . PHP_EOL);
    }
}

$dir = dirname(__DIR__);
status("Changing directory to '$dir'");
chdir($dir) or die("Can't change directory to '$dir'" . PHP_EOL);

run('docker-compose exec db mariadb --password=use4dwhcontrol -ve ' .
    '"drop database if exists laravel; create database laravel;"');

run('php artisan dwh-control:setup');

run('php artisan dwh-control:seed --weeks=8 --etl_definitions=40 --sla_definitions=10');
