<?php
namespace LAbasic\Statical;

use LAbasic\Session\Service as SessionService;

// class Session extends \Statical\BaseProxy
class Session extends \Statical\BaseProxy
{
    public static function __callStatic($method_name, $args) 
    {
        // start session when need.
        $app = $GLOBALS['app'];
        if (! isset($app->container['session'])) {
            // echo 'start_session<br>';
            SessionService::start();
        }

        // echo 'Calling method ',$method_name,'<br />';
        return call_user_func_array(
            array($app->container['session'], $method_name),
            $args
        );
    }
}
