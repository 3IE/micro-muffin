<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mathieu
 * Date: 7/8/13
 * Time: 3:54 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Lib;

define('BASE_DIR', '');
define('CONTROLLER_DIR', BASE_DIR . 'app/controller/');
define('VIEW_DIR', BASE_DIR . 'app/view/');
define('MODEL_DIR', BASE_DIR . 'app/model/');
define('TMODEL_DIR', BASE_DIR . 'app/t_model/');
define('SPMODEL_DIR', BASE_DIR . 'app/sp_model/');
define('CONFIG_DIR', BASE_DIR . 'config/');
define('VENDORS_DIR', BASE_DIR . 'vendors/');
define('LIB_DIR', BASE_DIR . 'lib/');
define('LIBMODEL_DIR', BASE_DIR . 'lib/models/');
define('LIBFORM_DIR', BASE_DIR . 'lib/form/');
define('DICO_DIR', BASE_DIR . 'app/i18n/');

if (!defined('NOAUTOLOAD') || !NOAUTOLOAD)
{
  Autoloader::addPath(CONTROLLER_DIR);
  Autoloader::addPath(VIEW_DIR);
  Autoloader::addPath(MODEL_DIR);
  Autoloader::addPath(TMODEL_DIR);
  Autoloader::addPath(SPMODEL_DIR);
  Autoloader::addPath(CONFIG_DIR);
  Autoloader::addPath(VENDORS_DIR);
  Autoloader::addPath(LIB_DIR);
  Autoloader::addPath(LIBMODEL_DIR);
  Autoloader::addPath(LIBFORM_DIR);
  Autoloader::addPath(DICO_DIR);
}