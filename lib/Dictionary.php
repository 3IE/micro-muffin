<?php
/**
 * User: mathieu.savy
 * Date: 05/08/13
 * Time: 15:08
 */

namespace Lib;

abstract class Dictionary
{
  /** @var array */
  private $dico = array();

  /**
   * @return array
   */
  protected abstract function getDico();

  public function __construct()
  {
    $this->dico = $this->getDico();
  }

  /**
   * @param string $string
   * @throws \Exception
   * @return string
   */
  public function translate($string)
  {
    if (array_key_exists($string, $this->dico))
      return $this->dico[$string];
    else
    {
      if (ENV == MicroMuffin::ENV_DEV)
        throw new \Exception("No translation for '$string' in dictionary.");
      else
        return null;
    }
  }
}