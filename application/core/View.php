<?php

class Application_View {

  protected static $_instance;

  /**
   * Retrieve singleton instance
   *
   * @return Application_View
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

  private $_partial_directory = 'layout/partials/';
  private $_layout_directory = 'layout/';
  private $_view_directory = 'views/';

  private $_partial_path = '';
  private $_layout_path  = '';
  private $_view_path    = '';

  private $_layout = 'layout.phtml';

  public function __construct() {

    $this->_partial_path = APPLICATION_PATH . $this->_partial_directory;
    $this->_layout_path = APPLICATION_PATH . $this->_layout_directory;
    $this->_view_path = APPLICATION_PATH . $this->_view_directory;

  }

  public function setLayout() {

  }

  public function loadView($file, $variables = array()) {
    $layout_variables = $variables;
    $layout_variables['layout_main_content'] = $this->_view($file, $variables);

    echo $this->_layout($this->_layout, $layout_variables);
  }

  public function partial($file, $variables = array()){
    if(!is_array($variables))
      throw new Exception("Partial included variables must be an array");

    extract($variables);
    ob_start();

    require($this->_partial_path . $file);

    $return_html = ob_get_contents();
    ob_end_clean();
    return $return_html;
  }

  private function _layout($file, $variables = array()){
    if(!is_array($variables))
      throw new Exception("Layout included variables must be an array");

    extract($variables);
    ob_start();

    require($this->_layout_path . $file);

    $return_html = ob_get_contents();
    ob_end_clean();
    return $return_html;
  }

  private function _view($file, $variables = array()){
    if(!is_array($variables))
      throw new Exception("View included variables must be an array");

    extract($variables);
    ob_start();

    require_once($this->_view_path . $file);

    $return_html = ob_get_contents();
    ob_end_clean();
    return $return_html;
  }



}