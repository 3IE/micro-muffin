<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Mathieu
 * Date: 08/06/13
 * Time: 16:51
 * To change this template use File | Settings | File Templates.
 */

namespace Lib;

require_once('tools.php');

class Autoloader
{
  /** @var array */
  private static $include_path = array();

  /**
   * @return Autoloader
   */
  public static function register()
  {
    new Autoloader();
  }

  private function __construct()
  {
    spl_autoload_register(array($this, 'loader'));
  }

  /**
   * @param string $p
   */
  public static function addPath($p)
  {
    self::$include_path[] = $p;
  }

  /**
   * @param string $fullClassName
   */
  private function loader($fullClassName)
  {
    $table     = explode('\\', $fullClassName);
    $className = $table[count($table) - 1];

    $base_dir = __DIR__ . '/../';
    foreach (self::$include_path as $path)
    {
      if (file_exists($base_dir . $path . $className . '.php'))
        require_once $base_dir . $path . $className . '.php';
      else if (file_exists($base_dir . $path . Tools::capitalize($className) . '.php'))
        require_once $base_dir . $path . Tools::capitalize($className) . '.php';
      else if (file_exists($base_dir . $path . strtolower($className) . '.php'))
        require_once $base_dir . $path . strtolower($className) . '.php';
    }
  }
}