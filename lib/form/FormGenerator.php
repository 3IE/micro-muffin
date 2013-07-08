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

  /** @var array */
  private $fields;

  /** @var bool */
  private $isBootstrap;

  /**
   * @param string $action
   * @param string $method
   */
  public function __construct($action, $method = 'POST')
  {
    $this->method = $method;
    $this->action = $action;
    $this->isBootstrap = true;
    $this->fields = array();
  }

  public function disableBootstrap()
  {
    $this->isBootstrap = false;
  }

  /**
   * @param string $name
   * @param string $type
   * @param int $required
   * @param string $value
   * @return Input
   */
  public function addInput($name, $type, $required = Field::FIELD_OPTIONAL, $value = null)
  {
    switch ($type)
    {
      case 'text':

        break;

      case 'password':

        break;
    }
  }

  /**
   * @param string $name
   * @param int $required
   * @param bool $checked
   */
  public function addCheckBox($name, $required = Field::FIELD_OPTIONAL, $checked = false)
  {

  }

  /**
   * @param string $name
   * @param array $options
   * @param int $required
   * @param string $selected
   * @return Select
   */
  public function addSelect($name, Array $options, $required = Field::FIELD_OPTIONAL, $selected = null)
  {

  }

  /**
   * @param string $name
   * @param int $required
   * @param string $value
   * @return Textarea
   */
  public function addTextarea($name, $required = Field::FIELD_OPTIONAL, $value = null)
  {

  }

  /**
   * @param string $name
   * @param array $options
   * @param int $required
   * @param string $checked
   * @return Radio
   */
  public function addRadio($name, Array $options, $required = Field::FIELD_OPTIONAL, $checked = null)
  {

  }

  /**
   * @return string
   */
  public function toString()
  {

  }
}