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

  /** @var string */
  private $placeholder;

  /**
   * @param string $name
   * @param int $required
   */
  public function __construct($name, $required)
  {
    $this->name        = $name;
    $this->required    = $required;
    $this->value       = null;
    $this->placeholder = null;
  }

  /**
   * @param string $s
   */
  public function setPlaceholder($s)
  {
    $this->placeholder = $s;
  }

  /**
   * @return null|string
   */
  private function getPlaceholder()
  {
    if (!is_null($this->placeholder))
      return $this->placeholder;
    else if (!is_null($this->label))
      return $this->label;
    else
      return null;
  }

  /**
   * @return null|string
   */
  private function placeholderToString()
  {
    $placeholder = $this->getPlaceholder();
    if (!is_null($placeholder))
      return 'placeholder="' . $placeholder . '"';
    else
      return null;
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
    $str .= '<textarea ' . $this->placeholderToString() . ' name="' . $this->name . '" id="' . $this->name . '">' . $value . '</textarea> ';
    $str .= $this->required == self::FIELD_REQUIRED ? self::requiredStarToString() : null;
    $str .= '</div>
        </div> ';

    return $str;
  }
}