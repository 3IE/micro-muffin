<?php
/**
 * Created by JetBrains PhpStorm.
 * User: savy_m
 * Date: 24/05/13
 * Time: 15:41
 * To change this template use File | Settings | File Templates.
 */

/* Database configuration */
define('DBHOST', 'localhost');
define('DBNAME', 'micro');
define('DBUSER', 'micro');
define('DBPASS', 'micro');
define('DISPLAY_SQL_ERROR', true);

define('BASE_DIR', '');
define('CONTROLLER_DIR', BASE_DIR . 'app/controller/');
define('VIEW_DIR', BASE_DIR . 'app/view/');
define('MODEL_DIR', BASE_DIR . 'app/model/');
define('CONFIG_DIR', BASE_DIR . 'config/');
define('VENDORS_DIR', BASE_DIR . 'vendors/');
define('LIB_DIR', BASE_DIR . 'lib/');

//Autoload
set_include_path(get_include_path() . PATH_SEPARATOR . CONTROLLER_DIR .
PATH_SEPARATOR . MODEL_DIR .
PATH_SEPARATOR . CONFIG_DIR .
PATH_SEPARATOR . LIB_DIR);
spl_autoload_extensions('.php');
spl_autoload_register();