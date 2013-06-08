<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Mathieu
 * Date: 08/06/13
 * Time: 15:59
 * To change this template use File | Settings | File Templates.
 */

namespace Lib\Models;

use Lib\PDOS;

class Deletable extends Writable
{
  /**
   * Delete the models from the database
   *
   * @param int $id
   * @return void
   */
  public static function delete($id)
  {
    $class = strtolower(get_called_class());
    $table = self::$table_name != null ? self::$table_name : $class . 's';

    $pdo = PDOS::getInstance();

    $query = $pdo->prepare('DELETE FROM ' . $table . ' WHERE id = :id');
    $query->bindParam(':id', $id, \PDO::PARAM_INT);
    $query->execute();
  }
}