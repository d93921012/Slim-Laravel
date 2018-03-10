<?php
namespace LAbasic\Statical;

class Route extends SlimSugar
{
    public static function map()
    {
        return call_user_func_array(array(static::$slim, 'map'), func_get_args());
    }

    public static function get()
    {
        return call_user_func_array(array(static::$slim, 'get'), func_get_args());
    }

    public static function post()
    {
        return call_user_func_array(array(static::$slim, 'post'), func_get_args());
    }

    public static function put()
    {
        return call_user_func_array(array(static::$slim, 'put'), func_get_args());
    }

    public static function patch()
    {
        return call_user_func_array(array(static::$slim, 'patch'), func_get_args());
    }

    public static function delete()
    {
        return call_user_func_array(array(static::$slim, 'delete'), func_get_args());
    }

    public static function options()
    {
        return call_user_func_array(array(static::$slim, 'options'), func_get_args());
    }

    public static function group()
    {
        return call_user_func_array(array(static::$slim, 'group'), func_get_args());
    }

    public static function any()
    {
        return call_user_func_array(array(static::$slim, 'any'), func_get_args());
    }

    public static function urlFor()
    {
        return call_user_func_array(array(static::$slim, 'urlFor'), func_get_args());
    }

    private static function split_act($act) 
    {
        $methods = ['any', 'get', 'post'];

        $method = null;
        $n_act = null;

        foreach ($methods as $mm) {
            if (strpos($act, $mm) === 0) {
                $n_act = strtolower(substr($act, strlen($mm)));
                $n_act = str_replace('_', '-', $n_act);
                $method = $mm;
            }
        }

        return [$method, $n_act];
    }

    public static function controllers($controllers)
    {
        $app = static::$slim;
        // dump($controllers);
        foreach ($controllers as $cname => $cntrl) {
            $actions = get_class_methods($cntrl);
            // dump($actions); exit;
            foreach ($actions as $act) {
                $c_act = $cntrl.':'.$act;
                list($method, $n_act) = self::split_act($act);
                if ($method == 'any') {
                    $app->map(
                            '/'.$cname.'/'.$n_act.'(/:param+)', $c_act
                        )->via('GET', 'POST');
                    // add default action
                    if ($n_act == 'index') {
                        $app->map(
                            '/'.$cname, $c_act
                        )->via('GET', 'POST');
                    }
                } else if ($method == 'get') {
                    $app->get('/'.$cname.'/'.$n_act, $c_act);
                    // add default action
                    if ($n_act == 'index') {
                        $app->get('/'.$cname, $c_act);
                    }
                } else if ($method == 'post') {
                    $app->post('/'.$cname.'/'.$n_act, $c_act);
                    // add default action
                    if ($n_act == 'index') {
                        $app->post('/'.$cname, $c_act);
                    }
                }
            };
        }
    }
}
