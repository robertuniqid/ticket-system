<?php

set_time_limit(20000);

define("BASE_PATH", dirname(__FILE__).DIRECTORY_SEPARATOR);
define("APPLICATION_PATH", dirname(__FILE__).DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR);

require_once(BASE_PATH . 'application/core/Loader.php');
require_once(BASE_PATH . 'application/core/View.php');
require_once(BASE_PATH . 'application/core/Handler.php');

Application_Loader::getInstance()->load(APPLICATION_PATH . 'models' . DIRECTORY_SEPARATOR);

$actionName = Model_Helper_Request::fetchCurrentPage() . 'Action';

if(method_exists(Application_Handler::getInstance(), $actionName))
  Application_Handler::getInstance()->$actionName();
else
  Application_Handler::getInstance()->errorAction();