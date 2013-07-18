<?php
/**
 * User: mathieu.savy
 * Date: 25/05/13
 * Time: 11:58
 */

namespace Lib;


class Router
{
  /** @var array */
  private static $routes = array();

  /** @var Filter[] */
  private static $filters = array();

  /**
   * @param array $content
   */
  public static function add(Array $content)
  {
    if (!array_key_exists("url", $content) ||
        !array_key_exists("controller", $content) || !array_key_exists("action", $content)
    )
    {
      $e = new \Error("Routing error", "A route must contain a url, a controller and an action");
      $e->display();
    }
    else
    {
      $filters = array();
      if (array_key_exists("filters", $content))
      {
        $filters = explode(",", $content["filters"]);
      }

      $route          = array(
        'url'        => self::getURL($content['url']),
        'controller' => $content['controller'],
        'action'     => $content['action'],
        'filters'    => $filters
      );
      self::$routes[] = $route;
    }
  }

  /**
   * @param string $url
   * @return array
   */
  private static function getURL($url)
  {
    $res_url = array();

    $url_array = explode("/", $url);
    while (count($url_array) > 0 && $url_array[0] == "")
      array_shift($url_array);

    foreach ($url_array as $part)
    {
      $pos = strpos($part, "#");
      if (!($pos === false) && $pos == 0)
      {
        $part      = ltrim($part, "#");
        $res_url[] = array('name' => $part, 'val' => "[a-zA-Z0-9\32-\151]+");
      }
      else
      {
        $res_url[] = $part;
      }
    }

    return $res_url;
  }

  /**
   * @param string $url
   * @return array
   */
  public static function get($url)
  {
    $params    = array();
    $url_array = explode("/", $url);
    while (count($url_array) > 0 && $url_array[0] == "")
      array_shift($url_array);

    foreach (self::$routes as $route)
    {
      $route_url = $route['url'];

      //Matching controller and action
      if (count($route_url) == count($url_array))
      {
        if ($route_url == array() || (array_key_exists(0, $route_url) && $route_url[0] == $url_array[0] &&
                (!array_key_exists(1, $route_url) || $route_url[1] == $url_array[1])
            )
        )
        {
          $match = true;
          //Matching parameters
          for ($i = 0; array_key_exists($i, $route_url); $i++)
          {
            if (is_aray($route_url[$i]))
            {
              if (preg_match("#" . $route_url[$i]['val'] . "#", $url_array[$i]))
              {
                $params[$route_url[$i]['name']] = $url_array[$i];
              }
              else
              {
                $match = false;
                break;
              }
            }
          }
          if (!$match)
            continue;
          else
          {
            foreach ($route["filters"] as $filter)
            {
              foreach (self::$filters as $filter_ref)
              {
                if ($filter_ref->name == $filter)
                {
                  $result = $filter_ref->exec();
                  if ($result != null)
                  {
                    if ($filter_ref->name == "login")
                      Controller::setIntented("/" . $url);
                    Controller::redirect($result);
                  }
                }
              }
            }

            return array(
              'controller' => $route['controller'],
              'action'     => $route['action'],
              'params'     => $params
            );
          }
        }
      }
    }
    return null;
  }

  /**
   * @param string $name
   * @param string $callback (Closure object)
   * Add a filter to the router
   */
  public static function filter($name, $callback)
  {
    $filter          = new Filter($name, $callback);
    self::$filters[] = $filter;
  }
}
