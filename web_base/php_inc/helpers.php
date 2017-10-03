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

function csrf_token()
{
    // do nothing ...
    return '';
}

function storage_path()
{
    return BASEDIR.'/storage';
}
