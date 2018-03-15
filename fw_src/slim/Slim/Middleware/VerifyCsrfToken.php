<?php
namespace Slim\Middleware;

use Request;
use Session;
use Illuminate\Session\TokenMismatchException;

class VerifyCsrfToken extends \Slim\Middleware
{
    public function call()
    {
        // 比對 session 和 submit 的 token
        if (in_array(Request::getMethod(), ['POST', 'PUT', 'DELETE', 'PATCH'])) {
            // 取得 session 的 token
            $token = Session::getToken();
            $submittedToken = Request::input('_token') ?: '' ;
            if ($token != $submittedToken) {
                // throw new \Exception('CSRF Token mismatch');
                throw new TokenMismatchException;
            }
        }
        $this->next->call();
    }

}  // class
