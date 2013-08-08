<?php

/**
 * Model_Helper_Request
 *
 * Access Model_Helper_Request - internal functions
 *
 * @author Robert
 */
class Model_Helper_Request {

  private static $_request = null;
  private static $_current_page = null;

  public static function getCurrentUrl(){
    return self::getCurrentModule().'/'.self::getCurrentController().'/'.self::getCurrentAction();
  }

  public static function getCurrentUrlFromRequest(){
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

  /**
   * @param $param
   * @param null $default
   * @return mixed
   */
  public static function getParam($param, $default = null){
    $request = self::getCurrentRequest();
    return $request->getParam($param , $default);
  }

  /**
   * @static
   * @return string
   */
  public static function getCurrentModule(){
    $request = self::getCurrentRequest();
    return $request->getModuleName();
    //for non-static inside controller : $this->getRequest()->getModuleName();
  }

  /**
   * @static
   * @return string
   */
  public static function getCurrentController(){
    $controller = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();

    return $controller == null ? self::getCurrentRequest()->getControllerName() : $controller;
  }

  /**
   * @static
   * @return string
   */
  public static function getCurrentAction(){
    $action = Zend_Controller_Front::getInstance()->getRequest()->getActionName();

    return $action == null ? self::getCurrentRequest()->getActionName() : $action;
  }

  private static $_straight_url;

  public static function getCurrentStraightUrl(){
    if(is_null(self::$_straight_url))
      self::$_straight_url = 'http'.(empty($_SERVER['HTTPS'])?'':'s').'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];

    return self::$_straight_url;
  }

  public static function getBaseUrl() {
    $config = Zend_Registry::get('config');
    return $config['url']['base'];
  }

  /**
   * @static
   * @return Zend_Controller_Request_Http
   */
  public static function getCurrentRequest(){
    if(is_null(self::$_request)){
      $router = new Zend_Controller_Router_Rewrite();
      self::$_request =  new Zend_Controller_Request_Http();
      $router->route(self::$_request);
    }
    return self::$_request;
  }

}
