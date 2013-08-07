<?php

class Model_Operation_UserPreferences {

  protected static $_instance;

  /**
   * Retrieve singleton instance
   *
   * @return Model_Operation_UserPreferences
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

  public $display_currency_id = 3;

  public function __construct(){

    if(isset(Model_Helper_Session::get()->user_display_currency_id))
      $this->display_currency_id = Model_Helper_Session::get()->user_display_currency_id;

  }

}