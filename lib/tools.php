<?php
/**
 * Created by JetBrains PhpStorm.
 * User: savy_m
 * Date: 24/05/13
 * Time: 15:34
 * To change this template use File | Settings | File Templates.
 */

namespace Lib;


class Tools
{
  /**
   * @param string $name
   * @param null $default
   * @return mixed
   */
  public static function getParam($name, $default = null)
  {
    return isset($_GET[$name]) ? $_GET[$name] : $default;
  }

  /**
   * @param string $str
   * @return string
   */
  public static function capitalize($str)
  {
    $up = $str;
    $up[0] = strtoupper($up[0]);
    return $up;
  }
}