<?php
/**
 * User: mathieu.savy
 * Date: 25/05/13
 * Time: 16:41
 */

namespace Lib\Models;

abstract class Model
{
  /** @var string|null */
  protected static $table_name = null;
  /** @var  int */
  protected $_id = 0;

  /**
   * @return int
   */
  public function getId()
  {
    return $this->_id;
  }

  /**
   * @param int $id
   */
  protected function setId($id)
  {
    $this->_id = $id;
  }

  /**
   * @return string JSON object
   */
  public function toJson()
  {
    $reflection = new \ReflectionClass($this);
    $attributes = $this->getAttributes($reflection);
    return json_encode($attributes);
  }

  /**
   * @param \ReflectionClass $r
   * @return array
   */
  protected function getAttributes(\ReflectionClass $r)
  {
    $attributes       = array();
    $class            = $r->getShortName();
    $attributes['id'] = $this->_id;

    foreach ($r->getProperties() as $att)
    {
      if ($att->class == 'T_' . $class)
      {
        $name = $att->name;
        if ($name[0] == "_" && $name != '_modified')
        {
          $property = $r->getProperty($name);
          $property->setAccessible(true);
          $attributes[substr($name, 1)] = $property->getValue($this);
          $property->setAccessible(false);
        }
      }
    }
    return $attributes;
  }
}