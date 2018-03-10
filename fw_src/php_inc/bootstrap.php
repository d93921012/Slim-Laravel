<?php
use Tracy\Debugger;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

use LAbasic\MyStatic;

require_once BASEDIR.'/web_base/vendor/autoload.php';
require_once BASEDIR.'/web_base/php_inc/helpers.php';

ini_set('date.timezone', 'Asia/Taipei');

$dotenv = new Dotenv\Dotenv(APPDIR);
$dotenv->load();

$app = new \Slim\Slim([
            'debug' => env('APP_DEBUG'),
            'log.writer' => new \Slim\Logger\DateTimeFileWriter(['path' => BASEDIR.'/storage/logs']),
            'templates.path' => APPDIR.'/views',
            'settings' => [
                'displayErrorDetails' => true,
                // You need to tell Slim not to add Content-Length with incorrect value.
                'addContentLengthHeader' => false,
            ]
        ]);


if (env('APP_DEBUG') == true) 
{
    Debugger::enable(Debugger::DEVELOPMENT, BASEDIR.'/storage/logs');
    Debugger::$strictMode = true;
}

$GLOBAL['app'] = $app;  // 設成 global variable，方便叫用

require_once 'init_service.php';
require_once 'loadStatic.php';
require_once APPDIR.'/routes/routes_0.php';

return $app;