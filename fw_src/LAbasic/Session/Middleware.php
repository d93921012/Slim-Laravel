<?php
namespace LAbasic\Session;

use App;

class Middleware extends \Slim\Middleware
{
    /**
     * Uses Slim's 'slim.before.router' hook to check for user authorization.
     * Will redirect to named login route if user is unauthorized
     *
     * @throws \RuntimeException if there isn't a named 'login' route
     */
    public function call()
    {
        echo 'Middleware, before next<br>';
        $this->next->call();
        echo 'Middleware, after next<br><hr>';
        // echo App::response()->getBody();
        echo '<hr>';
    }
}
