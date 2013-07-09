<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mathieu
 * Date: 7/8/13
 * Time: 5:05 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Lib\Form;


class FormGenerator
{
  /** @var string */
  private $action;

  /** @var string */
  private $method;

  /** @var Field[] */
  private $fields;

  /** @var string */
  private $legend;

  /** @var bool */
  private $isHorizontal;

  /**
   * @param string $action
   * @param string $method
   */
  public function __construct($action, $method = 'POST')
  {
    $this->method       = $method;
    $this->action       = $action;
    $this->fields       = array();
    $this->legend       = null;
    $this->isHorizontal = true;
  }

  /**
   * @param bool $b
   */
  public function setHorizontal($b)
  {
    $this->isHorizontal = $b;
  }

  /**
   * @param string $legend
   */
  public function setLegend($legend)
  {
    $this->legend = $legend;
  }

  /**
   * @param string $name
   * @param string $type
   * @param int $required
   * @return Input
   */
  public function addInput($name, $type, $required = Field::FIELD_OPTIONAL)
  {
    $input          = new Input($name, $type, $required);
    $this->fields[] = $input;
    return $input;
  }

  /**
   * @param string $name
   * @param string $label
   * @param int $required
   * @return Checkbox
   */
  public function addCheckBox($name, $label, $required = Field::FIELD_OPTIONAL)
  {
    $checkbox = new Checkbox($name, $required);
    $checkbox->setLabel($label);
    $this->fields[] = $checkbox;
    return $checkbox;
  }

  /**
   * @param string $name
   * @param array $options
   * @param int $required
   * @return Select
   */
  public function addSelect($name, Array $options, $required = Field::FIELD_OPTIONAL)
  {
    $select         = new Select($name, $options, $required);
    $this->fields[] = $select;
    return $select;
  }

  /**
   * @param string $name
   * @param int $required
   * @return Textarea
   */
  public function addTextarea($name, $required = Field::FIELD_OPTIONAL)
  {
    $textarea       = new Textarea($name, $required);
    $this->fields[] = $textarea;
    return $textarea;
  }

  /**
   * @param string $name
   * @param array $options
   * @param int $required
   * @return Radio
   */
  public function addRadio($name, Array $options, $required = Field::FIELD_OPTIONAL)
  {
    $radio          = new Radio($name, $options, $required);
    $this->fields[] = $radio;
    return $radio;
  }

  /**
   * @return string
   */
  public function toString()
  {
    $requiredFields = false;
    $str            = '';
    $horizontal     = $this->isHorizontal ? ' class="form-horizontal" ' : null;

    $str .= '<form action="' . $this->action . '" method="' . $this->method . '"' . $horizontal . '>';

    if (!is_null($this->legend))
      $str .= '<fieldset><legend>' . $this->legend . '</legend>';

    foreach ($this->fields as $field)
    {
      $requiredFields = $requiredFields || $field->isRequired();
      $str .= $field->toString();
    }

    if (!is_null($this->legend))
      $str .= '</fieldset>';

    if ($requiredFields)
      $str .= '<div><div class="control-group"><div class="controls">' . Field::requiredStarToString() . ' champs obligatoires</div></div></div>';

    $str .= '</form>';
    return $str;
  }
}