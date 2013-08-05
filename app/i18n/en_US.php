<?php
/**
 * User: mathieu.savy
 * Date: 05/08/13
 * Time: 15:10
 */

class en_US extends \Lib\Dictionary
{
  /**
   * @return array
   */
  protected function getDico()
  {
    return array(
      "hello"        => "Hello",
      "welcome_site" => "Welcome to micro-muffin !"
    );
  }
}
