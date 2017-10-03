<?php
namespace App\Controllers;

use Request;
use View;

class Coucur
{
    public function getIndex()
    {
        $content = 'coucur.index <br>';
        if (Request::is('cou_cur/index')) {
            $content .= 'Yes, it is cou_cur/index';
        } else {
            $content .= 'No, it is '.Request::url();
        }
        echo View::render('template', ['content' => $content]);
    }
}