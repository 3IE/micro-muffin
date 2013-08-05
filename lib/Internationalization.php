<?php
/**
 * User: mathieu.savy
 * Date: 05/08/13
 * Time: 15:02
 */

namespace Lib;

class Internationalization
{
  /** @var Dictionary */
  private static $dico = null;

  /**
   * @throws \Exception
   */
  public static function init()
  {
    $default_locale = DEFAULT_LOCALE;
    $locale         = LOCALE;

    if (class_exists($locale))
      self::$dico = new $locale();
    else
    {
      $tab = explode("_", $locale);
      if (class_exists($tab[0]))
        self::$dico = new $tab[0]();
      else if (class_exists($default_locale))
        self::$dico = new $default_locale();
      else
      {
        if (ENV == MicroMuffin::ENV_DEV)
          throw new \Exception("No dictionary found");
      }
    }
  }

  /**
   * @param $string
   * @return null
   */
  public static function translate($string)
  {
    if (is_null(self::$dico) && ENV == MicroMuffin::ENV_PROD)
      return null;
    else
      echo self::$dico->translate($string);
  }
}
