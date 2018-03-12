<?php
// use Illuminate\Support\Facades\Cache;
use App\Models\Cur_data;
use App\Lib\Decorator;

echo '<h2>Decorator test</h2>';
$app = $GLOBALS['app'];

$aa = Request::input('aa');
echo "Request('aa') = {$aa}<br>\n";

$now = date('H:i:s');

if (Session::has('redirect_msg')) {
    echo Session::get('redirect_msg')."<br>\n";
}

$flash_msg = 'Flash session set at '.$now;
$sess_msg = 'Session set at '.$now;
Session::flash('msg', $flash_msg);
Session::put('test', $sess_msg);
echo "Session flash('msg', '{$flash_msg}')<br>\n";
echo "Session put(test, '{$sess_msg}') ";

echo '<h2>DB test</h2>';
$rs = DB::select("select @@version as version;");
dump($rs);

echo '<h2>ORM test</h2>';

$cur_code = 'P020';
$cache_key = 'cur_'.$cur_code;

if (Cache::has($cache_key)) {
    $che_dat = Cache::get($cache_key);
    echo "Get data from cache. (update: {$che_dat['update']})<br>\n";
    $cur_dat = $che_dat['dat'];
} else {
    echo "Get data from model. ({$now})  <br>\n";
    $cur_dat = Cur_data::find($cur_code);
    Cache::put($cache_key,
        ['dat' => $cur_dat, 'update' => $now],
        1
    );
}

dump($cur_dat);
