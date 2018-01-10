<?php
/**
 * Created by PhpStorm.
 * Author: ltx
 * Date: 2017/12/21
 * Time: 18:41
 */
namespace Index\Controller;

use qwer\Controller;
//use index\Controller\aaaController;
class EmptyController extends Controller
{
    public function index()
    {
        echo 'empty';

        //(new aaaController())->index();
    }
}