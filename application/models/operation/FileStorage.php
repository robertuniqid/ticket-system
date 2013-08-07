<?php
  /**
   * Model_Operation_FileStorage
   *
   * Access Model_Operation_FileStorage - internal functions
   *
   * @author Robert
   */
class Model_Operation_FileStorage {

  protected static $_instance;

  /**
   * Retrieve singleton instance
   *
   * @return Model_Operation_FileStorage
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

  private $_store_location = '';
  private $_store_file_suffix = '.txt';
  private $_allowed_extensions = array('txt', 'html', 'phtml', 'php');

  public function __construct(){
    $this->_store_location = BASE_PATH.'file_storage'.DIRECTORY_SEPARATOR;

    if(!is_dir($this->_store_location))
      mkdir($this->_store_location, 0755);
  }

  public function storeExists($storage_name) {
    $location = in_array($this->getFileExtension($storage_name), $this->_allowed_extensions) ?
                              $this->_store_location.$storage_name
                              : $this->_store_location.$storage_name.$this->_store_file_suffix;

    return file_exists($location);
  }

  public function storeInformation($storage_name, $storage_information) {

    $location = in_array($this->getFileExtension($storage_name), $this->_allowed_extensions) ?
                $this->_store_location.$storage_name
              : $this->_store_location.$storage_name.$this->_store_file_suffix;

    file_put_contents($location, Model_Helper_Encoding::safeSerialize($storage_information));
  }

  public function fetchInformation($storage_name) {
    $location = in_array($this->getFileExtension($storage_name), $this->_allowed_extensions) ?
      $this->_store_location.$storage_name
      : $this->_store_location.$storage_name.$this->_store_file_suffix;

    if(!$this->storeExists($storage_name))
      return array();

    $file_content = file_get_contents($location);

    $return_content = Model_Helper_Encoding::safeUnSerialize($file_content);

    return $return_content;
  }

  public function getFileExtension($filename, $comma = false) {
    if (strrpos($filename, '.')){
      $extension = substr($filename, strrpos($filename, '.'));

      return strtolower(($comma ? $extension : substr($extension, 1)));
    } else
      return false;

  }


}