<?php
// $la_container: Laravel container
//$app->container->singleton('la_container', function () {
//    return new Illuminate\Container\Container;
//});

$la_container = $app->container;

//$la_container['config'] = new Illuminate\Config\Repository();

// Load them the configs from the config file 
$app_configs = ['app', 'cache', 'session', 'database'];

foreach ($app_configs as $conf) {
    $conf_file = APPDIR.'/config/'.$conf.'.php';
    if (file_exists($conf_file)) {
        $la_container['config']->set($conf, require $conf_file);
    }
}

// 模仿 Laravel 的 Request
// 避免與 Slim 的 Request 衝突，改名字
//$la_container['la_request'] = (LAbasic\Http\Request::createFromGlobals());

// Capsule\Manager will overwrite 'database.default' and 'database.fetch'
// Save and restore these values later
$default_conn = $la_container['config']['database.default'];
$fetch_style = $la_container['config']['database.fetch'];
$capsule = new Illuminate\Database\Capsule\Manager($la_container);
// restore ['config.database.default']
$capsule->setFetchMode($fetch_style);  // 會被蓋掉
$capsule->getDatabaseManager()->setDefaultConnection($default_conn);
$capsule->setAsGlobal();
$capsule->bootEloquent();

// // 註冊 resource，下列兩種作法皆可
$la_container['db'] = $capsule;

// Cache service is optional.
if (in_array('cache', $la_container['config']['app.services'])) 
{
    if (! isset($la_container['files'])) {
        $la_container['files'] = function () {
            return new \Illuminate\Filesystem\Filesystem();
        };
    }

    // Get the default cache driver (file in this case)
    $app->container->singleton('cache', function () use ($la_container) {
        $cacheManager = new Illuminate\Cache\CacheManager($la_container);
        return $cacheManager->store();
    });
}

if ($app->config['session.start_always'] == 'always') {
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
$app->hook('slim.after.router', function () {
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
