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
      $author = new Author();
      $author->setName('jesgjwbeghjb');
      $author->newAdd();
      //$author->save();
  }

  public function generate()
  {
    \Lib\Generator\Generator::run();
    $this->render = "false";
  }
}
