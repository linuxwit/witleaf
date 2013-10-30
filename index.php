<?php

$services_json = json_decode(getenv('VCAP_SERVICES'),true);
$mysql_config = $services_json['mysql-5.1'][0]['credentials'];
define('DB_NAME', $mysql_config['name']);
define('DB_USER', $mysql_config['user']);
define('DB_PASSWORD', $mysql_config['password']);
define('DB_HOST', $mysql_config['hostname']);
define('DB_PORT', $mysql_config['port']);

// change the following paths if necessary
$yii=dirname(__FILE__).'/yii/framework/yii.php';
$config=dirname(__FILE__).'/protected/config/main.php';
// remove the following line when in production mode
 defined('YII_DEBUG') or define('YII_DEBUG',true);
require_once($yii);
Yii::createWebApplication($config)->run();
