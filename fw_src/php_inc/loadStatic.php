<?php
/*
 為了 port Laravel 的程式，建立一些同名的程式
 注意，不要和 Slim 的 function 搞混了
*/

use LAbasic\Statical\SlimSugar;
 
// set Slim application for syntactic-sugar proxies
SlimSugar::$slim = $app;

// create a new Manager
$manager = new \Statical\Manager();
$static_ns = 'LAbasic\\Statical';

// # Add proxy instances
// 都是呼叫 static function，因此用空的 closure
$fakeInstance = function() {};

$manager->addProxyInstance('DB', 'Illuminate\\Database\\Capsule\\Manager', $fakeInstance);

// 不行用這個
// $manager->addProxyInstance('View', 'Illuminate\Support\Facades\DB', $fakeInstance);

$facades = ['Redirect', 'View', 'Request', 'Route'];

// Cache service is optional.
if (isset($app->cache)) {
    $facades[] = 'Cache';
}

foreach ($facades as $ff) {
    $manager->addProxyInstance($ff, "Illuminate\\Support\\Facades\\{$ff}", $fakeInstance);
}

$manager->addProxyInstance('Session', $static_ns.'\\Session', $fakeInstance);

$manager->addProxyInstance('App', $static_ns.'\\App', $app);

