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
    var_dump(Mod::all());

    $u = User::find(12);
    $a = Article::find(2);

    var_dump($u);
    var_dump($a);
    $m = new Mod();
    $m->setUser($u);
    $m->setArticle($a);
  }
}
