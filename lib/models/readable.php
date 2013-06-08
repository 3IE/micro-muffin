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

  /**
   * Find a models with the corresponding id
   *
   * @param int $id
   * @return null|object
   */
  public static function find($id)
  {
    $class        = get_called_class();
    $classLowered = strtolower($class);

    $stored_procedure = self::$procstock_find != null ? self::$procstock_find : 'get' . $classLowered . 'fromid';

    $pdo = PDOS::getInstance();
    $req = $pdo->prepare('SELECT ' . $stored_procedure . '(:id)');
    $req->bindValue(':id', $id, \PDO::PARAM_INT);
    $req->execute();

    $json_object = json_decode($req->fetch(\PDO::FETCH_COLUMN));
    if (!is_null($json_object))
    {
      $output_object = new $class();

      self::hydrate($output_object, $json_object);

      return $output_object;
    }
    else
      return null;
  }

  /**
   * @param Model $object
   * @param $data
   * @return void
   */
  private static function hydrate(Model &$object, $data)
  {
    foreach (get_object_vars($data) as $k => $v)
    {
      $k[0]   = strtoupper($k[0]);
      $method = "set" . $k;
      $object->$method($v);
    }
  }

  /**
   * Find all models in database
   *
   * @return Model[]
   */
  public static function all()
  {
    $class = strtolower(get_called_class());
    $proc  = self::$procstock_all != null ? self::$procstock_all : $class . 's';
    $pdo   = PDOS::getInstance();

    $query = $pdo->prepare('SELECT getall' . $proc . '()');
    $query->execute();

    $datas = $query->fetchAll(\PDO::FETCH_COLUMN);

    $outputs = array();
    foreach ($datas as $d)
    {
      $json_object = json_decode($d);
      $object      = new $class();
      self::hydrate($object, $json_object);
      $outputs[] = $object;
    }
    return $outputs;
  }
}