<?php
use Tracy\Debugger;
use LAbasic\MyStatic;

require_once BASEDIR.'/fw_src/vendor/autoload.php';
require_once BASEDIR.'/fw_src/vendor/illuminate/support/helpers.php';
require_once BASEDIR.'/fw_src/php_inc/helpers.php';

ini_set('date.timezone', 'Asia/Taipei');

$dotenv = new Dotenv\Dotenv(APPDIR);
$dotenv->load();

// 設定 smtp，供 Tracy\Debugger 寄錯誤通知
ini_set('SMTP', env('MAIL_HOST'));
ini_set('smtp_port' , env('MAIL_PORT'));

Debugger::enable(
    env('APP_DEBUG') == true ?
            Debugger::DEVELOPMENT : Debugger::PRODUCTION,
    BASEDIR.'/storage/logs',
    "d93921012@gmail.com"
);
Debugger::$strictMode = true;
// 自訂錯誤畫面
Debugger::$errorTemplate = APPDIR.'/views/errors/500.php';

$viewPath = APPDIR.'/views';
$cachePath = BASEDIR.'/storage/framework/views';

$app = new \Slim\Slim([
    'debug' => false, //  env('APP_DEBUG'),
    'ext.errHandler' => true,
    'log.enabled' => false, // 使用 Tracy\Debugger
    'view' => [
        'paths' => (array)$viewPath,
        'compiled' => $cachePath
    ],
    'csrfCheck' => true,
]);

// ref -- https://reinir.github.io/articles/http-slim-and-apachebench.html
// Send "Connection: close" to all HTTP/1.0 requests with no Connection header
if($_SERVER['SERVER_PROTOCOL']=='HTTP/1.0' && empty($_SERVER['HTTP_CONNECTION'])) {
    // header('Connection: close');
    $app->response->headers->set('Connection', 'close');
}


/*

$old_error_handler = set_error_handler(
function($errno ,  $errstr, $errfile, $errline) use ($app)
{
    echo "error"; exit;
    $e = new \ErrorException($errstr, $errno, 0, $errfile, $errline);
    ob_clean();
    // $app->response->status(500);
    header("HTTP/1.0 500 Internal Server Error");
    echo $app->callErrorHandler($e);
    $app->stop();
});
*/

$GLOBAL['app'] = $app;  // 設成 global variable，方便叫用

require_once 'init_service.php';
require_once 'loadStatic.php';

// load all routes
foreach(glob(APPDIR.'/routes/*.php') as $file){
    require $file;
}

return $app;