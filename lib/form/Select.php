<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mathieu
 * Date: 7/8/13
 * Time: 5:19 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Lib\Form;

class Select extends Field
{
  /** @var array */
  private $options;

  /** @var string|null */
  private $selected;

  public function __construct($name, Array $options, $required)
  {
    $this->name     = $name;
    $this->options  = $options;
    $this->required = $required;
  }

  /**
   * @param string $name
   * @param mixed $value
   * @return $this
   */
  public function addOption($name, $value)
  {
    $this->options[$value] = $name;
    return $this;
  }

  /**
   * @param string $name
   * @return $this
   */
  public function setSelected($name)
  {
    $this->selected = $name;
    return $this;
  }

  /**
   * @return string
   */
  public function toString()
  {
    $str = '';
    $str .= '<div class="control-group">';

    if ($this->label != null)
    {
      $labelUp    = $this->label;
      $labelUp[0] = strtoupper($labelUp[0]);
      $str .= '<label for="' . $this->name . '" class="control-label">' . $labelUp . ' :</label>';
    }

    $str .= '<div class="controls">';
    $str .= '<select>';
    foreach ($this->options as $k => $v)
    {
      if ($this->selected == $k)
        $str .= '<option value="' . $k . '" selected="selected">' . $v . '</option>';
      else
        $str .= '<option value="' . $k . '">' . $v . '</option>';
    }
    $str .= '</select> ';
    $str .= $this->required == self::FIELD_REQUIRED ? self::requiredStarToString() : null;
    $str .= '</div></div>';

    return $str;
  }
}