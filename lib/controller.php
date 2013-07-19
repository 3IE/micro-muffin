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
  const SESSION_FLASH = '_flash';

  /** @var array $variables */
  protected $variables = array();

  /** @var string $render */
  protected $render = "true";

  /** @var string $render_layout */
  protected $render_layout = "true";

  protected $layout_variables = array();

  /**
   * @param string $name
   * @param mixed $val
   */
  protected function setLayoutVariable($name, $val)
  {
    $this->layout_variables[$name] = $val;
  }

  /**
   * @return array
   */
  public function getLayoutVariables()
  {
    return $this->layout_variables;
  }

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

  /**
   * @return string
   */
  public function getRenderLayout()
  {
    return $this->render_layout;
  }

  /**
   * @param string $url
   * @return void
   */
  public static function redirect($url)
  {
    header("Location: " . $url);
    die();
  }

  public static function setIntented($intented)
  {
    $_SESSION["intented"] = $intented;
  }

  public static function getIntented()
  {
    if (isset($_SESSION["intented"]) == false)
      return "";
    return $_SESSION["intented"];
  }

  public function redirect_intented()
  {
    if (isset($_SESSION["intented"]) == false)
      header("Location: /");
    else
      header("Location: " . $_SESSION["intented"]);
    die();
  }

  /**
   * @param array $params
   * @return void
   */
  public function before_filter($params = array())
  {
  }

  /**
   * @param string $text
   */
  public static function flash($text)
  {
    $_SESSION[self::SESSION_FLASH] = $text;
  }

  /**
   * @return string
   */
  public static function getFlash()
  {
    if (isset($_SESSION[self::SESSION_FLASH]))
      return $_SESSION[self::SESSION_FLASH];
    else
      return null;
  }

  public static function emptyFlash()
  {
    if (isset($_SESSION[self::SESSION_FLASH]))
      unset($_SESSION[self::SESSION_FLASH]);
  }
}