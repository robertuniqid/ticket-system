<?php

class Model_CustomFields {

  protected static $_instance;

  /**
   * Retrieve singleton instance
   *
   * @return Model_CustomFields
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

  private $_storage_information = array();
  private $_storage_name = 'custom_fields';
  private $_next_id = 0;
  private $_entries = array();

  public function __construct() {
    $this->_storage_information = Model_Operation_FileStorage::getInstance()->fetchInformation($this->_storage_name);

    if($this->_storage_information == false || empty($this->_storage_information))
      $this->_setDefaultSettings();
    else
      $this->_handleSettings();
  }

  public function getAll() {
    return $this->_entries;
  }

  public function getById($id) {
    return isset($this->_entries[$id]) ? $this->_entries[$id] : array();
  }

  public function deleteRecord($id) {
    if(!isset($this->_entries[$id]))
      return false;

    unset($this->_entries[$id]);
    $this->sync();
    return true;
  }

  public function insert($information) {
    $this->_entries[$this->_next_id] = $information;
    $this->_entries[$this->_next_id]['id'] = $this->_next_id;

    $this->_next_id++;

    $this->sync();

    return $this->_next_id - 1;
  }

  /**
   * @param $id
   * @param array $information
   * @return bool|int
   */
  public function updateRecord($id, array $information = array()) {
    if(!isset($this->_entries[$id]))
      return false;

    $this->_entries[$id] = array_merge($this->_entries[$id], $information);
    $this->_entries[$id]['id'] = $id;

    $this->sync();

    return $id;
  }

  public function sync() {
    Model_Operation_FileStorage::getInstance()->storeInformation($this->_storage_name, array(
      'next_id' =>  $this->_next_id,
      'entries' =>  $this->_entries
    ));
  }

  private function _setDefaultSettings() {
    $this->_next_id = 1;
    $this->_entries = array();
  }

  private function _handleSettings() {
    $this->_next_id = $this->_storage_information['next_id'];
    $this->_entries = $this->_storage_information['entries'];
  }

}