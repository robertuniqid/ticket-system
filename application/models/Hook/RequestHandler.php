<?php
/**
 * Model_Hook_RequestHandler
 *
 * Access Model_Hook_RequestHandler - internal functions
 *
 * @author Robert
 */
class Model_Hook_RequestHandler {

  protected static $_instance;

  /**
   * Retrieve singleton instance
   *
   * @return Model_Hook_RequestHandler
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

  private $_requestHandler = array();
  private $_requestHandlerPath = 'assets/scripts/request_handler/';

  public function __construct(){

  }

  public function queRequestHandler($script_path, $script_run) {
    $this->que($this->_requestHandlerPath . $script_path, $script_run);
  }

  public function que($script_path, $script_run) {
    $this->_requestHandler[$script_path] = $script_run;
  }

  public function getHandlerInformation() {
    return $this->_requestHandler;
  }

}