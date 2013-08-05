<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Mathieu
 * Date: 08/06/13
 * Time: 15:58
 * To change this template use File | Settings | File Templates.
 */

namespace Lib\Models;

use Lib\PDOS;

abstract class Readable extends Model
{
  /** @var string|null */
  protected static $procstock_find = null;
  /** @var string|null */
  protected static $procstock_all = null;
  /** @var string|null */
  protected static $procstock_count = null;
  /** @var array */
  protected static $primary_keys = array();

  /**
   * Find all models in database
   *
   * @param string $order
   * @return Model[]
   */
  public static function all($order = NULL)
  {
    $class = strtolower(get_called_class());
    $proc  = self::$procstock_all != null ? self::$procstock_all : $class . 's';
    $pdo   = PDOS::getInstance();

    if (is_null($order))
      $query = $pdo->prepare('SELECT * FROM getall' . $proc . '()');
    else
      $query = $pdo->prepare('SELECT * FROM getall' . $proc . '() ORDER BY ' . $order);
    $query->execute();

    $datas = $query->fetchAll();

    $outputs = array();
    foreach ($datas as $d)
    {
      $object = new $class();
      self::hydrate($object, $d);
      $outputs[] = $object;
    }
    return $outputs;
  }

  /**
   * @return int
   */
  public static function count()
  {
    $class = strtolower(get_called_class());
    $proc  = self::$procstock_count != null ? self::$procstock_count : 'count' . $class . 's';
    $pdo   = PDOS::getInstance();

    $query = $pdo->prepare('SELECT * FROM ' . $proc . '()');
    $query->execute();

    $result = $query->fetch();

    return intval($result[$proc]);
  }
}