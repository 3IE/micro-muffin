<?php
/**
 * User: mathieu.savy
 * Date: 06/08/13
 * Time: 15:57
 */

namespace Lib\Router;

use Lib\Controller;

class Router
{
  const SYMBOL_MANDATORY       = "#";
  const SYMBOL_OPTIONAL        = "@";
  const PARAM_MANDATORY_REGEXP = "#^[a-zA-Z0-9]+$#";
  const PARAM_OPTIONAL_REGEXP  = "#^([a-zA-Z0-9-]+)?$#";

  /** @var Route[] */
  private static $routes;

  /** @var Filter[] */
  private static $filters;

  /**
   * @param array $content
   * @throws \Exception
   */
  public static function add(Array $content)
  {
    if (!array_key_exists("url", $content) ||
        !array_key_exists("controller", $content) || !array_key_exists("action", $content)
    )
      throw new \Exception("Invalid route. Either URL, controller or action is messing.");
    else
    {
      $filters = array();
      if (array_key_exists("filters", $content))
        $filters = explode(",", $content['"filters']);

      $route = new Route();
      $route->setAction($content['action']);
      $route->setController($content['controller']);
      $route->setUrl($content['url']);
      $route->setFilters($filters);
      $route->setOptionalParameters(substr_count($content['url'], '@'));

      self::$routes[] = $route;
    }
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

  /**
   * @param string $url
   * @return array
   */
  private static function getUrlChunks($url)
  {
    $chunks = explode("/", $url);

    while (count($chunks) > 0 && $chunks[0] == "")
      array_shift($chunks);

    while (count($chunks) > 0 && $chunks[count($chunks) - 1] == "")
      unset($chunks[count($chunks) - 1]);

    return $chunks;
  }

  /**
   * @param string $chunk
   * @param string $symbol
   * @return bool
   */
  private static function isParameter($chunk, $symbol)
  {
    $pos = strpos($chunk, $symbol);
    return !($pos === false) && $pos == 0;
  }

  /**
   * @param string $url
   * @return \Lib\Router\Route|null
   */
  public static function get($url)
  {
    $url_chunks = self::getUrlChunks($url);

    foreach (self::$routes as $route)
    {
      $route_chunks = self::getUrlChunks($route->getUrl());
      $diff         = count($route_chunks) - count($url_chunks);
      if ($diff <= $route->getOptionalParameters() && $diff >= 0)
      {
        $match      = true;
        $parameters = array();
        for ($i = 0; $match && $i < count($url_chunks) && $i < count($route_chunks); $i++)
        {
          $url_chunk   = $url_chunks[$i];
          $route_chunk = $route_chunks[$i];
          if (self::isParameter($route_chunk, self::SYMBOL_MANDATORY))
          {
            var_dump(self::PARAM_MANDATORY_REGEXP);
            var_dump($url_chunk);
            if (preg_match(self::PARAM_MANDATORY_REGEXP, $url_chunk))
            {
              $parameter_name              = ltrim($route_chunk, self::SYMBOL_MANDATORY);
              $parameters[$parameter_name] = $url_chunk;
            }
            else
              $match = false;
          }
          else if (self::isParameter($route_chunk, self::SYMBOL_OPTIONAL))
          {
            if (preg_match(self::PARAM_OPTIONAL_REGEXP, $url_chunk))
            {
              $parameter_name              = ltrim($route_chunk, self::SYMBOL_OPTIONAL);
              $parameters[$parameter_name] = $url_chunk;
            }
            else
              $match = !empty($url_chunk);
          }
          else //Plain text
          {
            if ($url_chunk != $route_chunk)
              $match = false;
          }
        }
        if ($match)
        {
          foreach ($route->getFilters() as $filter)
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

          $extRoute = clone($route);
          $extRoute->setParameters($parameters);
          return $extRoute;
        }
      }
    }
    return null;
  }
}
