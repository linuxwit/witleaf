<?php

define('DB_NAME', 'test');
define('DB_USER', 'test');
define('DB_PASSWORD', 'test');
define('DB_HOST', 'test');
define('DB_PORT', 'test');

$yii = dirname(__FILE__) . '/yii/framework/yii.php';
$config = dirname(__FILE__) . '/protected/config/test.php';
defined('YII_DEBUG') or define('YII_DEBUG', true);
require_once($yii);
Yii::createWebApplication($config)->run();
