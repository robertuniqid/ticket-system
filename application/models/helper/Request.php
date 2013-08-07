<?php

class Model_Helper_Request {

  private static $_current_page = null;

  public static function getParam($param, $default = null) {
    return isset($_POST[$param]) ? $_POST[$param] : (isset($_GET[$param]) ? $_GET[$param] : $default);
  }

  public static function fetchCurrentPage() {
    if(self::$_current_page == null) {
      $requestName = substr($_SERVER['REQUEST_URI'], strlen(str_replace('index.php', '', $_SERVER['SCRIPT_NAME'])));

      if(strpos($requestName, '?') !== FALSE)
        $requestName = substr($requestName, 0, strpos($requestName, '?'));

      if($requestName === false
        || $requestName == '')
        $requestName = 'index';

      $requestName = str_replace(array('.php', '.html', '.phtml'), '', $requestName);
      $requestName = strtolower($requestName);

      self::$_current_page = $requestName;
    }
    return self::$_current_page;
  }

}