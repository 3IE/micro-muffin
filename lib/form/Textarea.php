<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mathieu
 * Date: 7/8/13
 * Time: 5:22 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Lib\Form;

class Textarea extends Field
{
  /** @var string */
  private $value;

  /**
   * @param string $name
   * @param int $required
   */
  public function __construct($name, $required)
  {
    $this->name = $name;
    $this->required = $required;
    $this->value = null;
  }

  /**
   * @param string $v
   * @return $this
   */
  public function setValue($v)
  {
    $this->value = $v;
    return $this;
  }

  /**
   * @return string
   */
  public function toString()
  {
    $str       = '';
    $nameUp    = $this->name;
    $nameUp[0] = strtoupper($nameUp[0]);
    $value     = !is_null($this->value) ? ' value="' . $this->value . '" ' : null;

    $str .= '<div class="control-group">';

    if ($this->label != null)
    {
      $labelUp    = $this->label;
      $labelUp[0] = strtoupper($labelUp[0]);
      $str .= '<label for="' . $this->name . '" class="control-label">' . $labelUp . ' :</label>';
    }

    $str .= '<div class="controls" >';
    $str .= '<textarea placeholder="' . $nameUp . '" name="' . $this->name . '" id="' . $this->name . '">' . $value . '</textarea>';
    $str .= '</div>
        </div> ';

    return $str;
  }
}