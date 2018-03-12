<?php
// 設定 routes
// The first route settings.
// load addittional route settings.
// require_once 'routes_1st.php';
// require_once 'routes_2nd.php';

// IIS 6 沒辦法設 rewrite rule，只好費事一點
Route::get('/', function() use ($app) {
    $req = $app->sl_request;
    dump($req->getRootUri()); 
    if (strpos($_SERVER["REQUEST_URI"], '/index.php') === false) {
        return Redirect::to('index.php/home');
    } else {
        // echo "redirect to ".url('cou_cur'); return;
        return Redirect::to('/home');
    }
});

Route::get('/home', function() use ($app)
{
    $content = View::make('home');
    echo View::make('template', ['content' => $content])->render();
});

Route::get('/session-write', function() use ($app)
{
    $msg = 'This session data was written at time: '.date('H:i:s');
    Session::put('test', $msg);
    Session::flash('msg', "This is a flash message.");
    $content = "Write to session: <br>{$msg}";
    echo View::make('template', ['content' => $content]);
});

Route::get('/session-read', function() use ($app)
{
    $msg = Session::get('test');
    $content = "Get session: {$msg} <br>\n"
            . "Flash: ". Session::get('msg');
    echo View::make('template', ['content' => $content]);
});

Route::controllers([
        'cou_cur' => '\\App\\Controllers\\Coucur',
    ]);