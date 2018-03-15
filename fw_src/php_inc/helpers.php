<?php

if ( ! function_exists('env'))
{
    /**
     * Gets the value of an environment variable. Supports boolean, empty and null.
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    function env($key, $default = null)
    {
        $value = getenv($key);

        if ($value === false) return value($default);

        switch (strtolower($value))
        {
            case 'true':
            case '(true)':
                return true;

            case 'false':
            case '(false)':
                return false;

            case 'empty':
            case '(empty)':
                return '';

            case 'null':
            case '(null)':
                return;
        }

        if (substr($value, 0, 1) == '"' && substr($value, -1) == '"')
        {
            return substr($value, 1, -1);
        }

        return $value;
    }
}

function app()
{
    return $GLOBALS['app'];
}

function base_url()
{
    return rtrim(App::request()->getRootUri(), '/');
}

function url($path)
{
    return trim(base_url().'/'. trim($path, '/')); 
}

function asset($path)
{
    $base = str_replace('/index.php', '', base_url());
    return trim($base.'/'. trim($path, '/')); 
}

if ( ! function_exists('csrf_token'))
{
    /**
     * Get the CSRF token value.
     *
     * @return string
     *
     * @throws RuntimeException
     */
    function csrf_token()
    {
        return Session::getToken();
    }
}

function storage_path()
{
    return BASEDIR.'/storage';
}

function session($dat) 
{
    if (is_array($dat)) {
        foreach ($dat as $key => $value) {
            Session::put($key, $value);
        }
    } else {
        return Session::get($dat);
    }
}

if ( ! function_exists('view')) {
    function view($view = null, $data = array(), $mergeData = array())
    {
        $app = $GLOBALS['app'];
        $xview = $app->container['view'];

        if (func_num_args() === 0) {
            return $xview;
        }

        return $xview->make($view, $data, $mergeData);
    }
}