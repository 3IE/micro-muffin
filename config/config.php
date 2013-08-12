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
define('DBNAME', 'micro-muffin');
define('DBSCHEMA', 'public');
define('DBUSER', 'postgres');
define('DBPASS', 'root');
define('DISPLAY_SQL_ERROR', true);

/* Application configuration */
define('ENV', \Lib\MicroMuffin::ENV_DEV);
define('DEFAULT_LOCALE', 'en_US');
define('LOCALE', 'en_US');

/*
 * Put your own directories with classes you wanted to be autoloaded
 * Example : \Lib\Autoloader::addPath('app/classes/')
 */
