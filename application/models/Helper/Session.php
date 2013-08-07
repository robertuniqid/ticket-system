<?php

/**
 * Model_Helper_Session
 *
 * Access Model_Helper_Session - internal functions
 *
 * @author Robert
 */
class Model_Helper_Session {

  protected static $_session = null;
  protected static $_storage = array();

  public static function get(){
    if(is_null(self::$_session))
      self::$_session = new Zend_Session_Namespace(Model_Constant::SESSION_NAMESPACE);

    return self::$_session;
  }

  public static function getStorage($namespace){
    if(!isset(self::$_storage[$namespace]))
      self::$_storage[$namespace] = new Zend_Session_Namespace($namespace);

    return self::$_storage[$namespace];
  }

}
