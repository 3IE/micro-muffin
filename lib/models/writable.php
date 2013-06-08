<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Mathieu
 * Date: 08/06/13
 * Time: 15:58
 * To change this template use File | Settings | File Templates.
 */

namespace Lib\Models;

use Lib\EPO;
use Lib\PDOS;

class Writable extends Readable
{
  /**
   * Add or update the models in database
   *
   * @return void
   */
  public function save()
  {
    $reflection = new \ReflectionClass($this);
    $class      = $reflection->getShortName();
    $table      = self::$table_name != null ? self::$table_name : strtolower($class) . 's';

    $attributes = $this->getAttributes($reflection);

    $fields = '(';
    $values = '(';
    foreach ($attributes as $k => $v)
    {
      if ($k != 'id')
      {
        $fields .= $k . ', ';
        $values .= ':' . $k . ', ';
      }
    }
    $fields = substr($fields, 0, -2) . ')';
    $values = substr($values, 0, -2) . ')';

    $pdo = PDOS::getInstance();

    $pdo->beginTransaction();
    if ($this->_id == 0)
      $this->add($pdo, $table, $fields, $values, $attributes);
    else
      $this->update($pdo, $table, $attributes);
    $pdo->commit();
  }

  /**
   * @param \Lib\EPO $pdo
   * @param $table
   * @param string $fields
   * @param string $values
   * @param array $attributes
   */
  private function add(EPO &$pdo, $table, $fields, $values, Array $attributes)
  {
    $query = 'INSERT INTO ' . $table . ' ' . $fields . ' VALUES ' . $values;

    $query = $pdo->prepare($query);
    foreach ($attributes as $k => $v)
    {
      if ($k != 'id')
        $query->bindValue(':' . $k, $v);
    }
    $query->execute();
    $this->setId($pdo->lastInsertId());
  }

  /**
   * @param EPO $pdo
   * @param $table
   * @param array $attributes
   */
  private function update(EPO &$pdo, $table, Array $attributes)
  {
    $sql = 'UPDATE ' . $table . ' SET ';

    $set = '';
    foreach ($attributes as $k => $v)
    {
      if ($k != 'id')
        $set .= $k . ' = :' . $k . ', ';
    }
    $set = substr($set, 0, -2);
    $sql .= $set . ' WHERE id = :id';

    $query = $pdo->prepare($sql);
    foreach ($attributes as $k => $v)
    {
      $query->bindValue(':' . $k, $v);
    }
    $query->execute();
  }
}