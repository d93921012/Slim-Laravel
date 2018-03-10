<?php 
namespace LAbasic\Statical;

class Redirect
{
    public static function to($url)
    {
        $app = $GLOBALS['app'];
        
		// ref: https://akrabat.com/redirecting-in-slim-2-middleware/
        // cannot call $app->redirect()
        // This is because the app's redirect() method calls through to stop() which throws a Stop exception which is intended to be caught by the app's call() method. 
        $app->response->redirect(url($url));
    }
}