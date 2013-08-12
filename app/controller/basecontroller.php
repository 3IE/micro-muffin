<?php
/**
 * User: mathieu.savy
 * Date: 25/05/13
 * Time: 10:23
 */

class BaseController extends \Lib\Controller
{
  public function layout($params = array())
  {
    $this->handleFlash();
  }

  /*
   * Flash window handling
   */
  private function handleFlash()
  {
    $flash = \Lib\Controller::getFlash();
    $this->set('isFlash', false);
    if (!is_null($flash))
    {
      $this->set('isFlash', true);
      $this->set('flash', $flash);
      \Lib\Controller::emptyFlash();
    }
  }
}