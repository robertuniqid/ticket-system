<?php
/**
 * @depends Model_Helper_Session
 * @depends Model_Operation_Auth
 */
class Model_Operation_BasketStorage {

  protected static $_instance;

  /**
   * Retrieve singleton instance
   *
   * @return Model_Operation_BasketStorage
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

  private $_storage_namespace = 'user_basket';

  /**
   * @var array
   */
  private $_storage;
  /**
   * @var Zend_Session_Namespace
   */
  private $_storage_session;
  /**
   * @var int
   */
  private $_user_id;

  public function __construct(){
    $this->_initStorage();
    $this->_user_id = $this->_getUserId();
  }

  /**
   * @return array
   */
  public function getAll(){
    return $this->_storage;
  }

  public function getTotalItemsCount(){
    return array_sum($this->_storage);
  }

  /**
   * @param $product_id
   * @param int $count
   */
  public function addProduct($product_id, $count = 1){
    $this->_storage[$product_id] = (isset($this->_storage[$product_id]) ? ($this->_storage[$product_id] + $count) : $count);
    $this->_sync();
  }

  /**
   * @param $product_id
   * @return int
   */
  public function getProductCount($product_id){
    return isset($this->_storage[$product_id]) ? $this->_storage[$product_id] : 0;
  }

  /**
   * @param $product_id
   * @param int $count
   * @return int
   */
  public function increaseProductCount($product_id, $count = 1){
    $this->_storage[$product_id] = ((isset($this->_storage[$product_id]) ? $this->_storage[$product_id] : 0) + $count);

    $this->_sync();

    return $this->getProductCount($product_id);
  }

  /**
   * @param $product_id
   * @param int $count
   * @return int
   */
  public function decreaseProductCount($product_id, $count = 1){
    $this->_storage[$product_id] = $this->_storage[$product_id] - $count;

    if($this->_storage[$product_id] < 1)
      $this->_storage[$product_id] = 1;

    $this->_sync();

    return $this->getProductCount($product_id);
  }

  /**
   * @param $product_id
   * @param int $count
   * @return int
   */
  public function setProductCount($product_id, $count = 1){
    $this->_storage[$product_id] = $count;

    $this->_sync();

    return $this->getProductCount($product_id);
  }

  /**
   * @param $product_id
   */
  public function removeProduct($product_id){
    unset($this->_storage[$product_id]);

    $this->_sync();
  }

  /**
   *
   */
  public function emptyBasket(){
    $this->_storage = array();

    $this->_sync();
  }

  /**
   * @depends Model_Helper_Session
   * @return void
   */
  private function _initStorage(){
    $this->_storage_session = Model_Helper_Session::getStorage($this->_storage_namespace);

    $this->_storage = isset($this->_storage_session->product_list) ? $this->_storage_session->product_list : array();
  }

  private function _sync(){
    $this->_storage_session->product_list = $this->_storage;
  }

  /**
   * @depends Model_Operation_Auth
   * @return int
   */
  private function _getUserId(){
    return Model_Operation_Auth::getInstance()->getUserId();
  }

}