<?php
/*
 * 設定 Laravel 的 container
 * 讀取設定資料
 */

// $la_container: Laravel container
$app->container->singleton('la_container', function () {
    return new Illuminate\Container\Container;
});

$la_container = $app->la_container;

$la_container['config'] = new Illuminate\Config\Repository();

// Load them the configs from the config file 
$app_configs = ['app', 'cache', 'session', 'database'];

foreach ($app_configs as $conf) {
    $conf_file = APPDIR.'/config/'.$conf.'.php';
    if (file_exists($conf_file)) {
        $la_container['config']->set($conf, require $conf_file);
    }
}

/*
 * 建立 Symphony 的 Request 物件
 */
$la_container['request'] = (LAbasic\Http\Request::createFromGlobals());

/*
 * 設定 Database
 */

// Capsule\Manager will overwrite ['config.database.default']
// Save the value and restore it later
$default_conn = $la_container['config']['database.default'];
$capsule = new Illuminate\Database\Capsule\Manager($la_container);
// restore ['config.database.default']
$capsule->getDatabaseManager()->setDefaultConnection($default_conn);
$capsule->setAsGlobal();
$capsule->bootEloquent();

// // 註冊 resource，下列兩種作法皆可
$la_container['db'] = $capsule;

/*
 * 啟始 Laravel View 的設定
 */
$viewPath = BASEDIR.'/app_src/views';
$cachePath = BASEDIR.'/storage/framework/views';

$la_container['config']->set('view.paths', (array)$viewPath);
$la_container['config']->set('view.compiled', $cachePath);

$la_container->bindIf('files', function () {
    return new \Illuminate\Filesystem\Filesystem;
}, true);
$la_container->bindIf('events', function () {
    return new \Illuminate\Events\Dispatcher;
}, true);

(new \Illuminate\View\ViewServiceProvider($la_container))->register();
/*
 * 啟始 Cache service
 * 依設定檔，決定是否啟用
 */
//
if (in_array('cache', $la_container['config']['app.services'])) 
{
    // Get the default cache driver (file in this case)
    $app->container->singleton('cache', function () use ($la_container) {
        $cacheManager = new Illuminate\Cache\CacheManager($la_container);
        return $cacheManager->store();
    });
}

/*
 * 依設定值，啟始 Session service
 */
if ($la_container['config']['session.start_always'] == 'always') {
    // echo 'session start';
    LAbasic\Session\Service::start($app);  
}


/*
$hooks = ['slim.before', 'slim.before.router', 'slim.before.dispatch',
        'slim.after.dispatch', 'slim.after.router', 'slim.after'
    ];
foreach ($hooks as $hk) {
        $app->hook($hk, function () use ($hk) {
            echo 'Hook '.$hk.'<br>';
        });
    }    
*/
$app->hook('slim.after.dispatch', function () {
    LAbasic\Session\Service::stop(); 
});

// Customize error handlers.
$vpath = $app->config('templates.path');
$notFound_handler = $la_container['config']['app.handlers.notFound'];
if ($notFound_handler != '') {
    if (file_exists($vpath.'/'.$notFound_handler)) {
        $app->notFound(function () use ($app, $notFound_handler) {
            $app->render($notFound_handler);
        });
    }
}

if ($app->config('debug') == false) 
{
   $error_handler = $la_container['config']['app.handlers.error'];

   if ($error_handler != '') 
   {
        if (file_exists($vpath.'/'.$error_handler)) 
        {
            $app->error(function (\Exception $e) use ($app, $error_handler) {
                $app->render($error_handler);
            });
        }
   }
}
