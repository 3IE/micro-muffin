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

  /** @var array */
  private $recognizedTypes = array(
    'text',
    'hidden',
    'password',
    'mail',
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
   * @param bool $required
   * @return $this
   */
  public function __construct($name, $type, $required)
  {
    $this->name     = $name;
    $this->type     = $type;
    $this->required = $required;
    $this->value    = null;
    return $this;
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
    $str .= '<input type="' . $this->type . '" placeholder="' . $nameUp . '" name="' . $this->name . '" id="' . $this->name . '"' . $value . ' />';
    $str .= '</div>
        </div> ';

    return $str;
  }
}