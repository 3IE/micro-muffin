<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Mathieu
 * Date: 08/06/13
 * Time: 16:51
 * To change this template use File | Settings | File Templates.
 */

class Autoloader
{

  private static $include_path = array(
    CONTROLLER_DIR, MODEL_DIR, TMODEL_DIR, SPMODEL_DIR, LIB_DIR, LIBMODEL_DIR
  );

  public function __construct()
  {
    spl_autoload_register(array($this, 'loader'));
  }

  private function loader($fullClassName)
  {
    $table     = explode('\\', $fullClassName);
    $className = $table[count($table) - 1];

    $base_dir = __DIR__ . '/../';
    foreach (self::$include_path as $path)
    {
      if (file_exists($base_dir . $path . $className . '.php'))
        require_once $base_dir . $path . $className . '.php';
      else if (file_exists($base_dir . $path . strtolower($className) . '.php'))
        require_once $base_dir . $path . strtolower($className) . '.php';
    }
  }
}