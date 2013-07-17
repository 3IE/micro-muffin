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

class Readable extends Model
{
  /** @var string|null */
  protected static $procstock_find = null;
  /** @var string|null */
  protected static $procstock_all = null;
  /** @var string|null */
  protected static $procstock_count = null;

  /**
   * Find a models with the corresponding id
   *
   * @param int $id
   * @return null|Model
   */
  public static function find($id)
  {
    $class        = get_called_class();
    $classLowered = strtolower($class);

    $stored_procedure = self::$procstock_find != null ? self::$procstock_find : 'get' . $classLowered . 'fromid';

    $pdo = PDOS::getInstance();
    $req = $pdo->prepare('SELECT * FROM ' . $stored_procedure . '(:id)');
    $req->bindValue(':id', $id, \PDO::PARAM_INT);
    $req->execute();

    $result = $req->fetch();

    if (!is_null($result) && !is_null($result['id']))
    {
      $output_object = new $class();
      self::hydrate($output_object, $result);

      return $output_object;
    }
    else
      return null;
  }

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