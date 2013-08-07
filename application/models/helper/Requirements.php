<?php

class Model_Helper_Requirements {

  protected static $_instance;

  /**
   * Retrieve singleton instance
   *
   * @return Model_Helper_Requirements
   */
  public static function getInstance()
  {
    if (null === self::$_instance) {
      self::$_instance = new self();
    }
    return self::$_instance;
  }

  /**
   * Reset the singleton instance
   *
   * @return void
   */
  public static function resetInstance()
  {
    self::$_instance = null;
  }

  private $_apache_modules = array();

  public function __construct() {
    $this->_apache_modules = apache_get_modules();
  }

  public function hasApacheModule($name) {
    return in_array($name, $this->_apache_modules);
  }

}