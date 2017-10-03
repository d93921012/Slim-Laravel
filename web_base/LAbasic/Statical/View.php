<?php
namespace LAbasic\Statical;

class View
{
    public static function render($template, $data = null)
    {
        // 改一下檔名，與 Laravel 相容
        if (substr($template, -4) == '.php') {
            $template = substr($template, 0, strlen($template)-4);
        }
        
        $template = str_replace('.', '/', $template).'.php';

        // Slim\View::render() 是 protected method，不能呼叫
        return App::view()->fetch($template, $data);
    }
}