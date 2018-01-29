<?php
/*
 * 使用 Laravel 的 View
 * 參考
 * A standalone version of Laravel's Blade templating engine for use outside of Laravel. https://jenssegers.com
 * https://github.com/jenssegers/blade
 */
use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\ViewServiceProvider;

function view_call($container, $method, $params)
{
    return call_user_func_array([$container['view'], $method], $params);
}

define('BASEDIR', __DIR__.'/..');
require_once BASEDIR.'/web_base/vendor/autoload.php';

$la_container = new Container;
$la_container['config'] = new Illuminate\Config\Repository();

$viewPath = __DIR__;
$cachePath = BASEDIR.'/storage/framework/views';

$la_container['config']->set('view.paths', (array)$viewPath);
$la_container['config']->set('view.compiled', $cachePath);

$la_container->bindIf('files', function () {
    return new Filesystem;
}, true);
$la_container->bindIf('events', function () {
    return new Dispatcher;
}, true);

(new ViewServiceProvider($la_container))->register();

//dump($la_container['view']);
echo view_call($la_container, 'make',
    ['test',['name' => 'Ajax']])->render();