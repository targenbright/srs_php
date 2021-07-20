<?php


require_once realpath(__DIR__ . "/../vendor/autoload.php");

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$server = $_ENV['MYSQL_HOST'];
$sqlUsername = $_ENV['MYSQL_USER'];
$sqlPassword = $_ENV['MYSQL_PASS'];
$databaseName = $_ENV['MYSQL_DATABASE'];
