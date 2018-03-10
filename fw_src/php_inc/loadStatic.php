<?php
/*
 為了 port Laravel 的程式，建立一些同名的程式
 注意，不要和 Slim 的 function 搞混了
*/

 use LAbasic\Statical\SlimSugar;
 
// set Slim application for syntactic-sugar proxies
SlimSugar::$slim = $app;

// create a new Manager
$manager = new Statical\Manager();
$static_ns = 'LAbasic\\Statical';

// # Add proxy instances
// 都是呼叫 static function，因此用空的 closure
$fakeInstance = function() {};
$manager->addProxyInstance('DB', 'Illuminate\\Database\\Capsule\\Manager', $fakeInstance);

$myclass = ['Redirect', 'Route', 'Session', 'View'];

foreach ($myclass as $cls) {
    $manager->addProxyInstance($cls, $static_ns.'\\'.$cls, $fakeInstance);
}

$manager->addProxyInstance('App', $static_ns.'\\App', $app);

$manager->addProxyService('Request', $static_ns.'\\Request', array($app->la_container, '__get'), 'request');

// Cache service is optional.
if (isset($app->cache)) {
    $manager->addProxyService('Cache', $static_ns.'\\Cache', array($app->container, '__get'), 'cache');
}
