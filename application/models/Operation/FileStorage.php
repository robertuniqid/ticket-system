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

  public function __construct(){
    $this->_store_location = ROOT_PATH.DIRECTORY_SEPARATOR.'file_storage'.DIRECTORY_SEPARATOR;

    if(!is_dir($this->_store_location))
      mkdir($this->_store_location, 0755);
  }

  public function storeExists($storage_name) {
    return file_exists($this->_store_location.$storage_name.$this->_store_file_suffix);
  }

  public function storeInformation($storage_name, $storage_information) {
    file_put_contents($this->_store_location.$storage_name.$this->_store_file_suffix, Model_Helper_Encoding::safeSerialize($storage_information));
  }

  public function fetchInformation($storage_name) {
    $file_content = file_get_contents($this->_store_location.$storage_name.$this->_store_file_suffix);

    $return_content = Model_Helper_Encoding::safeUnSerialize($file_content);

    return $return_content;
  }


}