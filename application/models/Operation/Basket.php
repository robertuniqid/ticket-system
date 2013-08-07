<?php

class Model_Operation_Basket {

  protected static $_instance;

  /**
   * Retrieve singleton instance
   *
   * @return Model_Operation_Basket
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

  /**
   * @var Model_Operation_BasketStorage
   */
  public $storage = null;

  private $_shipping_type_id = null;
  private $_payment_type_id  = null;

  private $_current_basket_products = array();
  private $_current_basket_total_cost = 0;
  private $_current_basket_total_cost_currency = null;

  public function __construct(){
    $this->storage = Model_Operation_BasketStorage::getInstance();
    $this->_updateCurrentUserBasketProducts();
  }

  public function getCurrentUserBasketProductList($sync = false){
    if($sync)
      $this->_updateCurrentUserBasketProducts();

    return $this->_current_basket_products;
  }

  public function sync(){
    $this->_updateCurrentUserBasketProducts();
  }

  private function _updateCurrentUserBasketProducts(){
    $this->_current_basket_products = Model_Hook_Information_Product::getBasketProductInformation($this->storage->getAll());

    $this->_current_basket_products = Model_Operation_Array::mapByParam($this->_current_basket_products, 'id');

    $this->_current_basket_total_cost = 0;

    foreach($this->_current_basket_products as $product)
      $this->_current_basket_total_cost += $product['quantity'] * $product['price'];

    if(isset($product) && is_array($product))
      $this->_current_basket_total_cost_currency = $product['currency_name'];
  }

  public function getCurrentBasketTotalCost(){
    return $this->_current_basket_total_cost;
  }

  public function getCurrentBasketTotalCostCurrency(){
    return $this->_current_basket_total_cost_currency;
  }

  public function getAvailableShippingTypeList(){
    $shipping_type_ids = Model_ProductHasShippingType::fetchAllShippingTypeIdsByProductIds(array_keys($this->storage->getAll()));

    if(empty($shipping_type_ids))
      return array();

    $shipping_type_list = Model_ShippingType::getAll($shipping_type_ids,
                                                     Model_Constant::ITEM_VISIBLE,
                                                     Model_Constant::ITEM_NOT_DELETED);

    foreach($shipping_type_list as $key =>  $shipping_type) {
      $shipping_type_list[$key]['extra_cost'] = Model_ShippingTypePrice::getByShippingTypeIdAndCurrencyId($shipping_type['id'], Model_Operation_UserPreferences::getInstance()->display_currency_id);
      $shipping_type_list[$key]['extra_cost']['currency_name']  =  $this->_current_basket_total_cost_currency;
    }

    return $shipping_type_list;
  }

  public function setShippingType($shipping_type_id){
    $this->_shipping_type_id = $shipping_type_id;
  }

  public function setPaymentType($payment_type_id){
    $this->_payment_type_id = $payment_type_id;
  }

  public function hasVirtualProducts(){
    $return = false;

    foreach($this->getCurrentUserBasketProductList() as $product) {
      if($product['is_virtual'] == 1)
        $return = true;
    }

    return $return;
  }

  public function hasOnlyVirtualProducts(){
    $return = true;

    foreach($this->getCurrentUserBasketProductList() as $product) {
      if($product['is_virtual'] == 0)
        $return = false;
    }

    return $return;
  }

}