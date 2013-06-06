<?php
/**
 * Created by JetBrains PhpStorm.
 * User: savy_m
 * Date: 24/05/13
 * Time: 15:49
 * To change this template use File | Settings | File Templates.
 */

namespace Lib;


class Controller
{
  protected $variables = array();

  /** @var string $render */
  protected $render = "true";

  /**
   * @param string $name
   * @param mixed $val
   */
  protected function set($name, $val)
  {
    $this->variables[$name] = $val;
  }

  /**
   * @return array
   */
  public function getVariables()
  {
    return $this->variables;
  }

  /**
   * @return string
   */
  public function getRender()
  {
    return $this->render;
  }
}