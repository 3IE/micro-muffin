<?php
/**
 * User: mathieu.savy
 * Date: 25/05/13
 * Time: 10:23
 */

class BaseController extends \MicroMuffin\Controller
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
    $flash = \MicroMuffin\Controller::getFlash();
    $this->set('isFlash', false);
    if (!is_null($flash))
    {
      $this->set('isFlash', true);
      $this->set('flash', $flash);
      \MicroMuffin\Controller::emptyFlash();
    }
  }
}