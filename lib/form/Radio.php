<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mathieu
 * Date: 7/8/13
 * Time: 5:22 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Lib\Form;

class Radio extends Field
{
  /** @var array */
  private $options;

  /** @var string */
  private $checked;

  /**
   * @param string $name
   * @param array $options
   * @param int $required
   */
  public function __construct($name, Array $options, $required)
  {
    $this->name     = $name;
    $this->options  = $options;
    $this->required = $required;
    $this->checked  = null;
  }

  /**
   * @param string $name
   * @param string $value
   * @return $this
   */
  public function addOption($name, $value)
  {
    $this->options[$name] = $value;
    return $this;
  }

  /**
   * @param string $name
   * @return $this
   */
  public function setChecked($name)
  {
    $this->checked = $name;
    return $this;
  }

  /**
   * @return string
   */
  public function toString()
  {
    $str = '';
    $str .= '<div class="control-group">';
    $str .= '<div class="controls">';
    foreach ($this->options as $k => $v)
    {
      $str .= '<label class="radio">';
      if ($this->checked == $k)
        $str .= '<input type="radio" name="' . $this->name . '" value="' . $k . '" checked> ' . $v;
      else
        $str .= '<input type="radio" name="' . $this->name . '" value="' . $k . '"> ' . $v;
      $str .= '</label>';
    }
    $str .= '</div></div>';

    return $str;
  }
}