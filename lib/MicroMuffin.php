<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Mathieu
 * Date: 14/07/13
 * Time: 15:18
 * To change this template use File | Settings | File Templates.
 */

namespace Lib;

use Lib\Router\Route;
use Lib\Router\Router;

class MicroMuffin
{
  const ENV_DEV  = 0;
  const ENV_PROD = 1;

  /** @var MicroMuffin */
  private static $instance = null;

  /** @var Route */
  private $route;

  /** @var Controller */
  private $controller;

  /** @var string */
  private $action;

  private function init()
  {
    require_once('../config/config.php');
    require_once('autoloader.php');
    require_once('config.php');

    /*
     * WARNING ! Do not call Autoloader::register before the three includes before
     */
    Autoloader::register();

    require_once('../app/routes.php');
    require_once('../' . VENDORS_DIR . 'Twig/Autoloader.php');

    \Twig_Autoloader::register();
  }

  private function route()
  {
    //Route determination
    $url   = Tools::getParam("url", null);
    $route = Router::get(!is_null($url) ? $url : "");
    if (!is_null($route))
      $this->route = $route;
    else
    {
      $e = new \Error("Page not found", "The page you are looking for doesn't exist.");
      $e->display();
    }
  }

  private function checkRoute()
  {
    $className = $this->route->getController() . 'Controller';
    if (class_exists($className))
    {
      $this->controller = new $className();
      if (method_exists($this->controller, $this->route->getAction()))
        $this->action = $this->route->getAction();
      else
      {
        //Undefined action
        $e = new \Error('Undefined action', 'Action ' . $this->route->getAction() . ' doesn\'t exist on ' . $this->route['controller'] . ' controller.');
        $e->display();
      }
    }
    else
    {
      //Undefined controller
      $e = new \Error('Undefined controller', $this->route->getController() . ' doesn\'t exist.');
      $e->display();
    }
  }

  private function execute()
  {
    if (method_exists($this->controller, 'before_filter'))
      $this->controller->before_filter($this->route->getParameters());

    $action = $this->action;
    $this->controller->$action($this->route->getParameters());

    //View displaying
    if ($this->controller->getRender() != "false")
    {
      Internationalization::init();
      $twig_options = array('cache' => false, 'autoescape' => false, 'strict_variables' => true);

      $loader = new \Twig_Loader_Filesystem('../' . VIEW_DIR . strtolower($this->route->getController()));
      $twig   = new \Twig_Environment($loader, $twig_options);
      $twig->addFilter("tr", new \Twig_Filter_Function("\\Lib\\Internationalization::translate"));

      $page = $twig->render($this->action . ".html.twig", $this->controller->getVariables());

      //Base layout execution and displaying
      if ($this->controller->getRenderLayout() != "false")
      {
        $loader = new \Twig_Loader_Filesystem('../' . VIEW_DIR . 'base');
        $twig   = new \Twig_Environment($loader, $twig_options);
        $twig->addFilter("tr", new \Twig_Filter_Function("\\Lib\\Internationalization::translate"));

        $base = new \BaseController();
        $base->layout();
        $params          = $base->getVariables();
        $params          = array_merge($params, $this->controller->getLayoutVariables());
        $params['_page'] = $page;
        echo $twig->render("layout.html.twig", $params);
      }
      else
        echo $page;
    }
  }

  private function __construct()
  {
    $this->controller = null;
    $this->route      = null;
    $this->action     = null;
  }

  /**
   * @return Route
   */
  public function getRoute()
  {
    return $this->route;
  }

  /**
   * @return MicroMuffin
   */
  public static function getInstance()
  {
    return self::$instance;
  }

  public static function run()
  {
    self::$instance = new MicroMuffin();
    self::$instance->init();
    self::$instance->route();
    self::$instance->checkRoute();
    self::$instance->execute();
  }
}