<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mathieu
 * Date: 7/8/13
 * Time: 10:41 AM
 * To change this template use File | Settings | File Templates.
 */

namespace Lib\Form;

class FormValidator
{
  /** @var array */
  private $source;

  /** @var Rule[] */
  private $rules;

  /** @var array */
  private $messages;

  /**
   * @param array $source
   */
  public function __construct(Array $source)
  {
    $this->rules    = array();
    $this->messages = array();
    $this->source   = $source;
  }

  /**
   * @param string $name
   * @param array $constraints
   */
  public function addRule($name, Array $constraints)
  {
    $this->rules[] = new Rule($this->source, $name, $constraints);
  }

  /**
   * @return bool
   */
  public function check()
  {
    $pass = true;
    foreach ($this->rules as $rule) {
      $ret = $rule->check($this->messages);
      if (!is_bool($ret) || !$ret)
        $pass = false;
    }
    return $pass;
  }

  /**
   * @return array
   */
  public function getMessages()
  {
    return $this->messages;
  }
}