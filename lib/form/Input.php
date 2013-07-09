<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mathieu
 * Date: 7/8/13
 * Time: 5:18 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Lib\Form;

class Input extends Field
{
  /** @var string */
  private $type;

  /** @var string */
  private $value;

  /** @var string */
  private $placeholder;

  /** @var array */
  private $recognizedTypes = array(
    'text',
    'hidden',
    'password',
    'color',
    'date',
    'datetime',
    'datetime-local',
    'email',
    'month',
    'number',
    'range',
    'search',
    'tel',
    'time',
    'url',
    'week'
  );

  /**
   * @param string $name
   * @param string $type
   * @param int $required
   */
  public function __construct($name, $type, $required)
  {
    $this->name        = $name;
    $this->required    = $required;
    $this->value       = null;
    $this->placeholder = null;
    $this->label       = null;
    
    $this->setType($type);
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
   * @param string $type
   * @return $this
   */
  public function setType($type)
  {
    if (in_array($type, $this->recognizedTypes))
      $this->type = $type;
    else
      $this->type = 'text';
    return $this;
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
    $str         = '';
    $placeholder = null;
    $nameUp      = $this->name;
    $nameUp[0]   = strtoupper($nameUp[0]);
    $value       = !is_null($this->value) ? ' value="' . $this->value . '" ' : null;

    $str .= '<div class="control-group">';

    if ($this->label != null)
    {
      $labelUp    = $this->label;
      $labelUp[0] = strtoupper($labelUp[0]);
      $str .= '<label for="' . $this->name . '" class="control-label">' . $labelUp . ' :</label>';
    }

    $str .= '<div class="controls" >';
    $str .= '<input type="' . $this->type . '" ' . $this->placeholderToString() . ' name="' . $this->name . '" id="' . $this->name . '"' . $value . ' /> ';
    $str .= $this->required == self::FIELD_REQUIRED ? self::requiredStarToString() : null;
    $str .= '</div>
        </div> ';

    return $str;
  }
}