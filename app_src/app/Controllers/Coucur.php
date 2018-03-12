<?php
namespace App\Controllers;

use Redirect;
use Request;
use Session;
use View;

class Coucur
{
    public function getIndex()
    {
        $content = 'coucur.index <br>'
            ."Is this request 'cou_cur/index' ? <br>\n";
        if (Request::is('cou_cur/index')) {
            $content .= 'Yes, it is cou_cur/index';
        } else {
            $content .= 'No, it is '.Request::url();
        }

        $content .= "<br><a href='".url('cou_cur/redirect-tst')."'>Redirect Test</a>";
        echo View::make('template', ['content' => $content]);
    }

    public function anyForm_tst()
    {
        $content = View::make('form_tst');
        echo View::make('template', ['content' => $content]);
    }

    public function anyRedirect_tst($flg=false)
    {
        if (Session::has('redirect_msg')) {
            echo Session::get('redirect_msg').'<br>';
        }

        Session::flash('redirect_msg', 'Redirect from cou_cur at '.date('h:m:s'));
        if ($flg == true) {
            return Redirect::to('/home');
        }
        return "Not redirect <br>\n"
            ."<a href='".url('cou_cur/redirect-tst/true')
            ."'>Redirect to home</a>";
    }
}