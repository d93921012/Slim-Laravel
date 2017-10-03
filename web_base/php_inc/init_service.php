<?php
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

$la_container['request'] = (LAbasic\Http\Request::createFromGlobals());

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
