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

  /** @var int */
  private $rows;

  /** @var bool */
  private $tinyMce;

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
    $this->rows        = 3;
    $this->tinyMce     = false;
  }

  /**
   * @return $this
   */
  public function enableTinyMce()
  {
    $this->tinyMce = true;
    return $this;
  }

  /**
   * @param int $n
   * @return $this
   */
  public function setRows($n)
  {
    $this->rows = $n;
    return $this;
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

    if (count($this->errors) > 0)
      $str .= '<div class="control-group error">';
    else
      $str .= '<div class="control-group">';

    if ($this->label != null)
    {
      $labelUp    = $this->label;
      $labelUp[0] = strtoupper($labelUp[0]);
      $str .= '<label for="' . $this->name . '" class="control-label">' . $labelUp . ' :</label>';
    }

    $str .= '<div class="controls" >';
    $str .= '<textarea ' . (!is_null($this->class) ? 'class="' . $this->class . '"' : null) . ' rows="' . $this->rows . '" ' . $this->placeholderToString() . ' name="' . $this->name . '" id="' . $this->name . '">' . $this->value . '</textarea> ';
    $str .= $this->required == self::FIELD_REQUIRED ? self::requiredStarToString() : null;

    $str .= '<script type="text/javascript"> tinymce.init({ selector: "textarea#' . $this->name . '", plugins: ["code"] });</script>';

    if (count($this->errors) > 0)
    {
      $str .= '<span class="help-inline"> ';
      foreach ($this->errors as $e)
        $str .= $e . ', ';
      $str = substr($str, 0, -2);
      $str .= '</span>';
    }

    $str .= '</div>
        </div> ';

    return $str;
  }
}