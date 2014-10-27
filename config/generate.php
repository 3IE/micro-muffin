<?php
/**
 * User: Mathieu
 * Date: 08/06/13
 * Time: 15:55
 */

require_once('../vendor/autoload.php');
require_once('../config/config.php');

\MicroMuffin\Generator\Generator::setRelativeModelSaveDir(__DIR__ . '/../app/model/');
\MicroMuffin\Generator\Generator::setRelativeTModelSaveDir(__DIR__ . '/../app/t_model/');
\MicroMuffin\Generator\Generator::setRelativeSPModelSaveDir(__DIR__ . '/../app/sp_model/');

\MicroMuffin\Generator\Generator::run();