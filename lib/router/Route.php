<?php
/**
 * User: mathieu.savy
 * Date: 06/08/13
 * Time: 15:57
 */

namespace Lib\Router;


class Route
{
  /** @var string */
  private $url;

  /** @var string */
  private $controller;

  /** @var string */
  private $action;

  /** @var array */
  private $filters;

  /** @var array */
  private $parameters;

  /** @var int */
  private $optional_parameters;

  public function __construct()
  {
    $this->url                 = null;
    $this->controller          = null;
    $this->action              = null;
    $this->filters             = array();
    $this->parameters          = array();
    $this->optional_parameters = 0;
  }

  /**
   * @param int $optional_parameters
   */
  public function setOptionalParameters($optional_parameters)
  {
    $this->optional_parameters = $optional_parameters;
  }

  /**
   * @return int
   */
  public function getOptionalParameters()
  {
    return $this->optional_parameters;
  }

  /**
   * @param string $action
   */
  public function setAction($action)
  {
    $this->action = $action;
  }

  /**
   * @return string
   */
  public function getAction()
  {
    return $this->action;
  }

  /**
   * @param string $controller
   */
  public function setController($controller)
  {
    $this->controller = $controller;
  }

  /**
   * @return string
   */
  public function getController()
  {
    return $this->controller;
  }

  /**
   * @param array $filters
   */
  public function setFilters(Array $filters)
  {
    $this->filters = $filters;
  }

  /**
   * @return array
   */
  public function getFilters()
  {
    return $this->filters;
  }

  /**
   * @param string $url
   */
  public function setUrl($url)
  {
    $this->url = $url;
  }

  /**
   * @return string
   */
  public function getUrl()
  {
    return $this->url;
  }

  /**
   * @param array $parameters
   */
  public function setParameters($parameters)
  {
    $this->parameters = $parameters;
  }

  /**
   * @return array
   */
  public function getParameters()
  {
    return $this->parameters;
  }
}