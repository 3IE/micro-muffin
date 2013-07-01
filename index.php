<?phpsession_start();require_once('config/config.php');require_once('config/autoloader.php');$autoloader = new Autoloader();require_once('app/routes.php');require_once(VENDORS_DIR . 'Twig/Autoloader.php');Twig_Autoloader::register();$controller = 'indexController';$action     = 'index';//Détermination de la route$url = \Lib\Tools::getParam("url", null);if ($url == null)  $url = "";$route = null;if ($url !== null){  $route = \Lib\Router::get($url);  if ($route)  {    $controller = $route['controller'] . 'Controller';    $action     = $route['action'];  }  else  {    $e = new Error("Page not found", "The page you are looking for doesn't exist.");    $e->display();  }}//Determination controllerif (class_exists($controller)){  /** @var $cont \Lib\Controller */  $cont = new $controller();  //Determination de l'action  if (method_exists($cont, $action))  {    $cont->$action($route['params']);    //Affichage de la vue    if ($cont->getRender() != "false")    {      $loader = new Twig_Loader_Filesystem(VIEW_DIR . substr($controller, 0, -10));      $twig   = new Twig_Environment($loader, array('cache' => false));      $page = $twig->render($action . ".html.twig", $cont->getVariables());      //Chargement du layout de base      $loader = new Twig_Loader_Filesystem(VIEW_DIR . 'base');      $twig   = new Twig_Environment($loader, array('cache' => false, 'autoescape' => false));      if ($cont->getRenderLayout() != "false")      {        $base = new BaseController();        $base->layout();        $params          = $base->getVariables();        $params['_page'] = $page;        echo $twig->render("layout.html.twig", $params);      }      else        echo $page;    }  }  else  {    //Action inexistante    $e = new Error('Undefined action', 'Action ' . $action . ' doesn\'t exist on ' . $controller . '.');    $e->display();  }}else{  //Controller inexistant  $e = new Error('Undefined controller', $controller . ' doesn\'t exist.');  $e->display();}