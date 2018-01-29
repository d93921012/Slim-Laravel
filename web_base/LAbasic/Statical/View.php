<?php
namespace LAbasic\Statical;

class View extends \Statical\BaseProxy
{
    /**
     * Render shortcut.
     *
     * @param  string $view
     * @param  array  $data
     * @param  array  $mergeData
     *
     * @return string
     */
    public static function render($view, $data = [], $mergeData = [])
    {
        $app = $GLOBALS['app'];

        return $app->la_container['view']->make($view, $data, $mergeData)->render();
    }

    public static function __callStatic($method_name, $args)
    {
        $app = $GLOBALS['app'];

        // echo 'Calling method ',$method_name,'<br />';
        return call_user_func_array(
            array($app->la_container['view'], $method_name),
            $args
        );
    }
}
