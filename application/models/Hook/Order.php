<?php

/**
 * Model_Hook_Order
 *
 * Access Model_Hook_Order - internal functions
 *
 * @author Robert
 */
class Model_Hook_Order {

  private static $_last_order_id = 0;

  public static function getLastInsertedOrderId() {
    return self::$_last_order_id;
  }

  /**
   * @TODO Implement more security options in case somebody is being retarded
   * @param array $product_list
   * @param $user_id
   * @param array $shipping_information
   * @param array $billing_information
   * @param $currency_id
   * @param $shipping_type_id
   * @param $payment_type_id
   * @return array
   */
  public static function saveOrder(array $product_list = array(),
                                   $user_id,
                                   array $shipping_information = array(),
                                   array $billing_information = array(),
                                   $currency_id,
                                   $shipping_type_id,
                                   $payment_type_id){

    if(empty($product_list))
      return array(
        Model_Interface_StringsShop::getInterfaceString('ordering_empty_product_list')
      );



    $shipping_information   = Model_Operation_Validation::cleanInformation($shipping_information);
    $billing_information    = Model_Operation_Validation::cleanInformation($billing_information);

    $product_information = self::_getProductInformation($product_list, $currency_id);

    $shipping_type_status_id = self::_getShippingTypeStatusIdAccordingToProductInformation($product_information);
    $payment_type_status_id  = self::_getPaymentTypeStatusIdAccordingToProductInformationAndShippingTypeId($product_information, $shipping_type_id);



    $order_id = self::_saveOrderSheet($shipping_type_id,
                                      $payment_type_id,
                                      $shipping_type_status_id,
                                      $payment_type_status_id,
                                      self::_calculateProductInformationTotalPrice($product_information),
                                      $currency_id,
                                      $shipping_information,
                                      $billing_information,
                                      $user_id,
                                      $product_information);

    self::$_last_order_id = $order_id;

    self::_insertOrderProducts($product_information, $order_id);

    if($payment_type_status_id != 0)
      Model_Hook_OrderProcessing::updateOrderPaymentStatus($order_id, $payment_type_status_id);
    if($payment_type_id != 0)
      Model_Hook_OrderProcessing::paymentTypeSpecificActions($order_id);
    if($shipping_type_id != 0)
      Model_Hook_OrderProcessing::shippingTypeSpecificActions($order_id);

    return array();
  }

  /**
   * @param $shipping_type_id
   * @param $payment_type_id
   * @param $shipping_type_status_id
   * @param $payment_type_status_id
   * @param $product_price
   * @param $currency_id
   * @param $shipping_information
   * @param $billing_information
   * @param $user_id
   * @param $ordered_products
   * @return array
   */
  private static function _saveOrderSheet($shipping_type_id,
                                          $payment_type_id,
                                          $shipping_type_status_id,
                                          $payment_type_status_id,
                                          $product_price,
                                          $currency_id,
                                          $shipping_information,
                                          $billing_information,
                                          $user_id,
                                          $ordered_products = array()){

    $shipping_price = self::_getShippingTypeCost($shipping_type_id, $currency_id, $product_price);
    $payment_price = self::_getPaymentTypeCost($payment_type_id, $currency_id, $product_price);

    $allow_affiliate = true;

    if(!empty($ordered_products))
      foreach($ordered_products as $ordered_product) {
        $product_earnings = Model_ProductPercent::getAllByProductId($ordered_product['product_id']);
        $product_earnings_composed = Model_Operation_Array::composeKeyToValue($product_earnings, 'user_id', 'percent');


        if(isset($product_earnings_composed[0]) && $product_earnings_composed[0] == 0)
          $allow_affiliate = false;
        elseif(!isset($product_earnings_composed[0]))
          $allow_affiliate = false;
      }

    $referral_user_id = $allow_affiliate ? self::_getCurrentReferralID($user_id) : 0;
    $refer_a_friend_referral_order_id = self::_getCurrentReferAFriendOrderID($ordered_products);

    $order_information = array(
      'user_id'                   =>  $user_id,

      'referral_id'                       =>  $referral_user_id,
      'refer_a_friend_referral_order_id'  =>  $refer_a_friend_referral_order_id,

      'shipping_type_status_id'   =>  $shipping_type_status_id,
      'payment_type_status_id'    =>  $payment_type_status_id,

      'total_price'               =>  ($product_price + $shipping_price + $payment_price),
      'product_price'             =>  $product_price,
      'shipping_price'            =>  $shipping_price,
      'payment_price'             =>  $payment_price,

      'currency_id'               =>  $currency_id,

      'payment_type_id'           =>  $payment_type_id,
      'payment_first_name'        =>  isset($billing_information['first_name']) ? $billing_information['first_name'] : '-',
      'payment_last_name'         =>  isset($billing_information['last_name']) ? $billing_information['last_name'] : '-',
      'payment_phone_number'      =>  isset($billing_information['phone_number']) ? $billing_information['phone_number'] : '-',
      'payment_company'           =>  isset($billing_information['company']) ? $billing_information['company'] : '-',
      'payment_company_address'                    =>  isset($billing_information['company_address']) ? $billing_information['company_address'] : '-',
      'payment_company_fiscal_code'                =>  isset($billing_information['company_fiscal_code']) ? $billing_information['company_fiscal_code'] : '-',
      'payment_company_economic_registry_number'   =>  isset($billing_information['company_economic_registry_number']) ? $billing_information['company_economic_registry_number'] : '-',
      'payment_company_iban_code'                  =>  isset($billing_information['company_iban_code']) ? $billing_information['company_iban_code'] : '-',
      'payment_company_bank'                       =>  isset($billing_information['company_bank']) ? $billing_information['company_bank'] : '-',
      'payment_street'            =>  isset($billing_information['street']) ? $billing_information['street'] : '-',
      'payment_street_number'     =>  isset($billing_information['street_number']) ? $billing_information['street_number'] : '-',
      'payment_flat_block'        =>  isset($billing_information['flat_block']) ? $billing_information['flat_block'] : '-',
      'payment_flat_scale'        =>  isset($billing_information['flat_scale']) ? $billing_information['flat_scale'] : '-',
      'payment_flat_floor'        =>  isset($billing_information['flat_floor']) ? $billing_information['flat_floor'] : '-',
      'payment_flat_apartment'    =>  isset($billing_information['flat_apartment']) ? $billing_information['flat_apartment'] : '-',
      'payment_city'              =>  isset($billing_information['city']) ? $billing_information['city'] : '-',
      'payment_region'            =>  isset($billing_information['region']) ? $billing_information['region'] : '-',
      'payment_country'           =>  isset($billing_information['country']) ? $billing_information['country'] : '-',
      'payment_zip_code'          =>  isset($billing_information['zip_code']) ? $billing_information['zip_code'] : '-',
      'payment_pin'               =>  isset($billing_information['pin']) ? $billing_information['pin'] : '-',
      'shipping_type_id'          =>  $shipping_type_id,
      'shipping_first_name'       =>  isset($shipping_information['first_name']) ? $shipping_information['first_name'] : '-',
      'shipping_last_name'        =>  isset($shipping_information['last_name']) ? $shipping_information['last_name'] : '-',
      'shipping_phone_number'     =>  isset($shipping_information['phone_number']) ? $shipping_information['phone_number'] : '-',
      'shipping_company'          =>  isset($shipping_information['company']) ? $shipping_information['company'] : '-',
      'shipping_street'           =>  isset($shipping_information['street']) ? $shipping_information['street'] : '-',
      'shipping_street_number'    =>  isset($shipping_information['street_number']) ? $shipping_information['street_number'] : '-',
      'shipping_flat_block'       =>  isset($shipping_information['flat_block']) ? $shipping_information['flat_block'] : '-',
      'shipping_flat_scale'       =>  isset($shipping_information['flat_scale']) ? $shipping_information['flat_scale'] : '-',
      'shipping_flat_floor'       =>  isset($shipping_information['flat_floor']) ? $shipping_information['flat_floor'] : '-',
      'shipping_flat_apartment'   =>  isset($shipping_information['flat_apartment']) ? $shipping_information['flat_apartment'] : '-',
      'shipping_city'             =>  isset($shipping_information['city']) ? $shipping_information['city'] : '-',
      'shipping_region'           =>  isset($shipping_information['region']) ? $shipping_information['region'] : '-',
      'shipping_country'          =>  isset($shipping_information['country']) ? $shipping_information['country'] : '-',
      'shipping_zip_code'         =>  isset($shipping_information['zip_code']) ? $shipping_information['zip_code'] : '-',
      'shipping_pin'              =>  isset($shipping_information['pin']) ? $shipping_information['pin'] : '-',

      'ip_address'                =>  $_SERVER['REMOTE_ADDR']
    );

    $order_id = Model_Order::insertRecord($order_information);

    return $order_id;
  }

  private static function _getShippingTypeStatusIdAccordingToProductInformation($product_information){
    $count = 0;

    foreach($product_information as $current_product_information)
      if($current_product_information['is_virtual'] == 1)
        $count++;

    if($count == count($product_information)) {
      return 0;
    } else {
      $shipping_type_order_status = Model_ShippingTypeOrderStatus::getAll(array(), 'order ASC');
      return $shipping_type_order_status[0]['id'];
    }
  }

  private static function _getPaymentTypeStatusIdAccordingToProductInformationAndShippingTypeId($product_information, $shipping_type_id){
    $shipping = Model_ShippingType::getById($shipping_type_id);

    foreach($product_information as $current_product_information)
      if($current_product_information['is_virtual'] == 1)
        return Model_Constant::PAYMENT_TYPES_STATUS_PENDING_ID;

    if($shipping['pay_on_delivery'] == 1)
      return Model_Constant::PAYMENT_TYPES_STATUS_PENDING_ID;

    return Model_Constant::PAYMENT_TYPES_STATUS_PENDING_ID;
  }

  /**
   * @param $payment_type_id
   * @param $currency_id
   * @param $product_cost
   * @return string
   */
  private static function _getPaymentTypeCost($payment_type_id, $currency_id, $product_cost){
    $payment_type_price = Model_PaymentTypePrice::getByPaymentTypeIdAndCurrencyId($payment_type_id, $currency_id);

    if($payment_type_price['is_fixed'] == true) {
      $payment_price  = $payment_type_price['extra_cost'];
    } else {
      $payment_price  = (($payment_type_price['extra_cost'] * $product_cost) / 100);
    }

    return $payment_price;
  }

  /**
   * @param $shipping_type_id
   * @param $currency_id
   * @param $product_cost
   * @return string
   */
  private static function _getShippingTypeCost($shipping_type_id, $currency_id, $product_cost){
    if($shipping_type_id == 0)
      return 0;

    $shipping_type_price = Model_ShippingTypePrice::getByShippingTypeIdAndCurrencyId($shipping_type_id, $currency_id);

    if($shipping_type_price['is_fixed'] == true) {
      $shipping_price  = $shipping_type_price['extra_cost'];
    } else {
      $price = (($shipping_type_price['extra_cost'] * $product_cost) / 100);
      $shipping_price  = $price == 0 ? 0 : number_format($price, 2, '.', '');
    }

    return $shipping_price;
  }

  /**
   * @param $product_list
   * @param $currency_id
   * @return array
   */
  private static function _getProductInformation($product_list, $currency_id) {
    $product_information = array();

    foreach($product_list as $product_id  =>  $quantity) {
      $current_product_information = Model_Product::getById($product_id);
      $price_information   = Model_ProductPrice::getByProductIdAndCurrencyId($product_id, $currency_id);

      $product_information[$product_id] = array(
        'product_id'    =>  $product_id,
        'name'          =>  $current_product_information['name'],
        'is_virtual'    =>  $current_product_information['is_virtual'],
        'price'         =>  $price_information['price'],
        'quantity'      =>  $quantity,
        'bonus_points'  =>  $price_information['bonus_points'],
        'currency_id'   =>  $currency_id,
      );
    }

    return $product_information;
  }

  /**
   * @param $product_information
   * @return int
   */
  private static function _calculateProductInformationTotalPrice($product_information){
    $total_price = 0;

    foreach($product_information as $product)
      $total_price += $product['quantity'] * $product['price'];

    return $total_price;
  }

  private static function _insertOrderProducts($product_information, $order_id) {
    foreach($product_information as $current_product_information) {
      $current_product_information['order_id'] =  $order_id;
      Model_OrderedProduct::insertRecord($current_product_information);
    }
  }

  private static function _getCurrentReferAFriendOrderID($ordered_products) {
    if(!isset(Model_Helper_Session::get()->refer_a_friend))
      return 0;
    if(empty($ordered_products))
      return 0;

    $refer_a_friend_information = Model_Helper_Session::get()->refer_a_friend;
    $product_ids = Model_Operation_Array::extractParam($ordered_products, 'product_id', true);

    if(!in_array($refer_a_friend_information['product_id'], $product_ids))
      return 0;

    return $refer_a_friend_information['order_id'];
  }

  private static function _getCurrentReferralID($user_id) {
    $referrer_user_id = self::_getCurrentReferralIDClean($user_id);

    if($referrer_user_id == 0)
      return 0;

    $referrer_user_information = Model_User::getById($referrer_user_id);
    $user_information          = Model_User::getById($user_id);

    $referrer_user_role_information = Model_UserRole::getById($referrer_user_information['user_role_id']);
    $user_role_information      = Model_UserRole::getById($user_information['user_role_id']);

    if($referrer_user_role_information['affiliate_power'] <= $user_role_information['affiliate_power'])
      return 0;

    return $referrer_user_id;
  }

  private static function _getCurrentReferralIDClean($user_id) {
    $user_information = Model_User::getById($user_id);

    if(isset(Model_Helper_Session::get()->direct_buy))
      return Model_Helper_Session::get()->direct_buy;

    if(is_numeric(Model_Helper_Request::getCurrentRequest()->getCookie('affiliate_user_id')))
      return Model_Helper_Request::getCurrentRequest()->getCookie('affiliate_user_id');

    if((time() - strtotime($user_information['creation_date'])) > Model_Constant::AFFILIATE_PROGRAM_LENGTH)
      return 0;

    return $user_information['referred_by_user_id'];
  }

}
