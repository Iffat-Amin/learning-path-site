<?php
define('CONFIG_PATH', realpath('app/core/config.php'));
include CONFIG_PATH;

$app = new Application('homeController');
$app->run();