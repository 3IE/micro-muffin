<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mathieu
 * Date: 7/8/13
 * Time: 5:18 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Lib\Form;

class Checkbox extends Field
{
  /** @var bool */
  private $checked;

  /**
   * @param string $name
   * @param int $required
   */
  public function __construct($name, $required)
  {
    $this->name     = $name;
    $this->required = $required;
  }

  /**
   * @param bool $b
   * @return $this
   */
  public function setChecked($b)
  {
    $this->checked = $b;
    return $this;
  }

  /**
   * @return string
   */
  public function toString()
  {
    $str = '';

    $str .= '<div class="control-group"><div class="controls">';
    $str .= '<label class="checkbox">';
    if ($this->checked == true)
      $str .= '<input name="' . $this->name . '" checked="checked" type="checkbox"> ' . $this->label;
    else
      $str .= '<input name="' . $this->name . '" type="checkbox"> ' . $this->label;
    $str .= '</label></div></div>';

    return $str;
  }
}