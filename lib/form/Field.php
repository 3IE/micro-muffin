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
}