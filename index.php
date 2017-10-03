<?php
define('BASEDIR', __DIR__);
define('APPDIR', __DIR__.'/app_src');

$app = require_once BASEDIR.'/web_base/php_inc/bootstrap.php';

$app->run();