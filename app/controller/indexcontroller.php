<?php
/**
 * Created by JetBrains PhpStorm.
 * User: savy_m
 * Date: 24/05/13
 * Time: 15:59
 * To change this template use File | Settings | File Templates.
 */

class IndexController extends \Lib\Controller
{
  public function index($params = array())
  {
    /*$a = Article::find(1);
    $a->getUser();
    var_dump($a);*/
    var_dump(SP_Users::execute());
  }
}
