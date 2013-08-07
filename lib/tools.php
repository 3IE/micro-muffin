<?php
/**
 * Created by JetBrains PhpStorm.
 * User: savy_m
 * Date: 24/05/13
 * Time: 15:34
 * To change this template use File | Settings | File Templates.
 */

namespace Lib;


class Tools
{
  /**
   * @param string $name
   * @param null $default
   * @return mixed
   */
  public static function getParam($name, $default = null)
  {
    return isset($_GET[$name]) ? $_GET[$name] : $default;
  }

  /**
   * @param string $str
   * @return string
   */
  public static function capitalize($str)
  {
    $up    = $str;
    $up[0] = strtoupper($up[0]);
    return $up;
  }

  /**
   * @param string $sStr
   * @return string
   */
  public function sanitizeForUrl($sStr)
  {
    $ISOTransChar = array("'"      => '-', ' "' => '-',
                          'áŕĺäâă' => 'a', 'ÁŔĹÄÂĂ' => 'A', 'éčëę' => 'e', 'ÉČËĘ' => 'E',
                          'íěďîĄ'  => 'i', 'ÍĚĎÎ' => 'I', 'óňöôőđ' => 'o', 'ř' => '0', 'ÓŇÖÔŐŘ' => 'O',
                          'ľúůüű'  => 'u', 'ÚŮÜŰ' => 'U', 'ý˙' => 'y', 'Ý' => 'Y',
                          'ć'      => 'ae', 'Ć' => 'AE', '' => 'oe', '' => 'OE',
                          'ß'      => 'B', 'ç' => 'c', 'Ç' => 'C', 'Đ' => 'D', 'ń' => 'n', 'Ń' => 'N',
                          'Ţ'      => 'p', 'ţ' => 'P', '' => 's', '' => 'S');

    $tmp = array();
    for ($c = 0; $c < strlen($sStr); $c++)
    {
      $carac = $sStr{$c};
      foreach ($ISOTransChar as $chars => $r)
      {
        if (strpos($chars, $sStr{$c}) > -1 || strpos(utf8_decode($chars), $sStr{$c}) > -1)
        {
          $carac = $r;
          break;
        }
      }
      $tmp[] = $carac;
    }

    $newStr = implode("", $tmp);
    $newStr = preg_replace('/--+/', '-', $newStr);
    $newStr = preg_replace('/([^a-z0-9_-])/i', '', $newStr);
    $newStr = preg_replace('/([-])$/', '', $newStr);
    $newStr = strtolower($newStr);

    return $newStr;
  }
}