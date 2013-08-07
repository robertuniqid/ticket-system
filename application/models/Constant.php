<?php

class Model_Constant {

  const SCRIPT_NAME = 'Ticket System';

  public static $_menu_information = array(
    array(
      'name'    =>  'Home',
      'url'     =>  'index',
      'class'   =>  'icon-home icon-white'
    ),
  );

  protected static $_instance;

  /**
   * Retrieve singleton instance
   *
   * @return Model_Constant
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

  public $flag_system_colors = array(
    0 =>  'None',
    1 =>  'Green',
    2 =>  'Blue',
    3 =>  'Yellow',
    4 =>  'Red'
  );

  public $flag_system_table_class = array(
    0 =>  '',
    1 =>  'success',
    2 =>  'info',
    3 =>  'warning',
    4 =>  'error'
  );

}