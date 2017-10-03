<?php 
namespace LAbasic\Statical;

class Redirect
{
    public static function to($url)
    {
        App::redirect(url($url));
    }
}