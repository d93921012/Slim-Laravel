<?php
use App\Models\Cur_data;
use App\Lib\Decorator;

echo '<h2>Decorator test</h2>';
$app = $GLOBALS['app'];
// Decorator::$sess = $app->session;
// Decorator::flash('msg', 'test');

$aa = Request::input('aa');
echo "Request('aa') = {$aa}<br>\n";
// $foo = new Decorator();
Session::flash('msg', 'Decorator test');
Session::put('test', 'Decorator test');

echo Session::get('test');
// Session::stop_session();

echo '<h2>ORM test</h2>';

$cur_code = 'P020';
$cache_key = 'cur_'.$cur_code;
if (Cache::has($cache_key)) {
    echo "Get data from cache. <br>\n";
    $cur_dat = Cache::get($cache_key);
} else {
    echo "Get data from model. <br>\n";
    $cur_dat = Cur_data::find($cur_code);
    Cache::put($cache_key, $cur_dat, 20);
}

dump($cur_dat);
