<?php
/**
 * Created by JetBrains PhpStorm.
 * User: savy_m
 * Date: 24/05/13
 * Time: 15:41
 * To change this template use File | Settings | File Templates.
 */

/* Database configuration */
define('DBDRIVER', \MicroMuffin\Generator\DriverType::POSTGRESQL);
define('DBHOST', 'localhost');
define('DBNAME', 'micro-muffin');
define('DBSCHEMA', 'public');
define('DBUSER', 'postgres');
define('DBPASS', 'root');
define('DISPLAY_SQL_ERROR', true);

/* Application configuration */
define('ENV', \MicroMuffin\MicroMuffin::ENV_DEV);
define('DEFAULT_LOCALE', 'en_US');
define('LOCALE', 'en_US');

\MicroMuffin\Log::setLogDirectory(__DIR__ . '/../log/');

/*
 * Put your own directories with classes you wanted to be autoloaded
 */
\MicroMuffin\ClassLoader::addDirectories([
    __DIR__ . '/../app/controller',
    __DIR__ . '/../app/t_model',
    __DIR__ . '/../app/l10n',
    __DIR__ . '/../app/model',
    __DIR__ . '/../app/sp_model',
]);