<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mathieu
 * Date: 7/8/13
 * Time: 5:14 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Lib\Form;

abstract class Field
{
  const FIELD_REQUIRED = 1;
  const FIELD_OPTIONAL = 0;

  /** @var string */
  protected $name;

  /** @var bool */
  protected $required;

  /** @var string|null */
  protected $label;

  /** @var array */
  protected $errors;

  /** @var bool */
  protected $disable = false;

  /** @var string */
  protected $class = null;

  /**
   * @param $class
   * @return $this
   */
  public function setClass($class)
  {
    $this->class = $class;
    return $this;
  }

  /**
   * @return string
   */
  public function getClass()
  {
    return $this->class;
  }

  /**
   * @return $this
   */
  public function disable()
  {
    $this->disable = true;
    return $this;
  }

  /**
   * @param array $errors
   */
  public function setErrors(Array $errors)
  {
    $this->errors = $errors;
  }

  /**
   * @param string $error
   */
  public function addError($error)
  {
    $this->errors[] = $error;
  }

  /**
   * @return string
   */
  public abstract function toString();

  /**
   * @param string $label
   * @return $this
   */
  public function setLabel($label)
  {
    $this->label = $label;
    return $this;
  }

  /**
   * @return $this
   */
  public function setRequired()
  {
    $this->required = self::FIELD_REQUIRED;
    return $this;
  }

  /**
   * @return bool
   */
  public function isRequired()
  {
    return $this->required == self::FIELD_REQUIRED;
  }

  /**
   * @return string
   */
  public static function requiredStarToString()
  {
    return '<span style="color:red;">*</span>';
  }
}