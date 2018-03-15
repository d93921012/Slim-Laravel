<?php
use Tracy\Debugger;
use LAbasic\MyStatic;

require_once BASEDIR.'/fw_src/vendor/autoload.php';
require BASEDIR.'/fw_src/vendor/illuminate/support/helpers.php';

$controllersDirectory = APPDIR . '/Controllers';
// register the autoloader and add directories
Illuminate\Support\ClassLoader::register();
Illuminate\Support\ClassLoader::addDirectories(array(
    $controllersDirectory
));

require_once BASEDIR.'/fw_src/php_inc/helpers.php';

ini_set('date.timezone', 'Asia/Taipei');

$dotenv = new Dotenv\Dotenv(APPDIR);
$dotenv->load();

$logger = new \Slim\Logger\MonologWriter(array(
    'name' => 'slim',
    'handlers' => [
        new \Monolog\Handler\ChromePHPHandler(\Monolog\Logger::DEBUG),
        new \Monolog\Handler\RotatingFileHandler(BASEDIR."/storage/logs/slim.log", 3, \Monolog\Logger::DEBUG)
    ],
    'processors' => [
        new Monolog\Processor\WebProcessor(),
        new Monolog\Processor\UidProcessor()
    ]
));

$viewPath = APPDIR.'/views';
$cachePath = BASEDIR.'/storage/framework/views';

$app = new \Slim\Slim([
    'debug' => env('APP_DEBUG'),
    'log.writer' => $logger,
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

if (env('APP_DEBUG') == true) 
{
    Debugger::enable(Debugger::DEVELOPMENT, BASEDIR.'/storage/logs');
    Debugger::$strictMode = true;
} else {
    // 在 Include router 或其他 php file 時，可能出錯
    // 所以，先設定 handler function
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
}

$GLOBAL['app'] = $app;  // 設成 global variable，方便叫用

require_once 'init_service.php';
require_once 'loadStatic.php';

// load all routes
foreach(glob(APPDIR.'/routes/*.php') as $file){
    require $file;
}

return $app;