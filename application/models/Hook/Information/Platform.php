<?php

/**
 * Model_Hook_Information_Platform
 *
 * Access Model_Hook_Information_Platform - internal functions
 *
 * @author Robert
 */
class Model_Hook_Information_Platform {

  protected static $_instance;

  /**
   * Retrieve singleton instance
   *
   * @return Model_Hook_Information_Platform
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

  public function __construct() {

  }

  public function platformListAccessTime() {
    return Model_Operation_Array::composeKeyToValue(
      Model_Platform::getAll(), 'id', 'name'
    );
  }

}
