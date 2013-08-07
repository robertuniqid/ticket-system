<?php

/**
 * Model_Hook_Information_PaymentType
 *
 * Access Model_Hook_Information_PaymentType - internal functions
 *
 * @author Robert
 */
class Model_Hook_Information_PaymentType {

  public static function getAvailablePaymentTypeList($shipping_type_id, $currency_id){

    $payment_type_ids = Model_ShippingTypeHasPaymentType::fetchAllPaymentTypeIdsByShippingTypeId($shipping_type_id);

    if(empty($payment_type_ids))
      return array();

    $payment_type_list = Model_PaymentType::getAll($payment_type_ids,
                                                   Model_Constant::ITEM_VISIBLE,
                                                   Model_Constant::ITEM_NOT_DELETED);



    return self::parsePaymentTypeList($payment_type_list, $currency_id);
  }

  public static function parsePaymentTypeList($payment_type_list, $currency_id){

    $currency_information = Model_Currency::getById($currency_id);
    $currency_name = $currency_information['short_name'];


    foreach($payment_type_list as $key  =>  $payment_type) {
      $payment_type_list[$key]['extra_cost'] = self::_paymentTypeListPopulateInformation($payment_type['id'], $currency_id);

      if(!isset($payment_type_list[$key]['extra_cost']['currency_name']))
        $payment_type_list[$key]['extra_cost']['currency_name'] = $currency_name;
    }

    return $payment_type_list;
  }

  private static function _paymentTypeListPopulateInformation($payment_type_id, $currency_id){
    $currency_id = self::getPaymentTypeCurrency($payment_type_id, $currency_id);

    $ret  =  Model_PaymentTypePrice::getByPaymentTypeIdAndCurrencyId($payment_type_id, $currency_id);

    if(in_array($payment_type_id,
                array(
                  Model_Constant::PAYMENT_TYPE_PAYPAL_ID,
                  Model_Constant::PAYMENT_TYPE_MOBILPAY_ID,
                  Model_Constant::PAYMENT_TYPE_TWO_CHECKOUT_ID
                )
                )
      ) {
      $paypal_info = Model_Currency::getById($currency_id);
      $ret['currency_name'] = $paypal_info['name'];
    }

    return $ret;
  }

  public static function getPaymentTypeCurrency($payment_type_id, $currency_id) {
    if($payment_type_id == Model_Constant::PAYMENT_TYPE_PAYPAL_ID)
      $currency_id = Model_Constant::PAYMENT_TYPE_PAYPAL_DEFAULT_CURRENCY_ID;
    if($payment_type_id == Model_Constant::PAYMENT_TYPE_MOBILPAY_ID)
      $currency_id = Model_Constant::PAYMENT_TYPE_MOBILPAY_DEFAULT_CURRENCY_ID;
    if($payment_type_id == Model_Constant::PAYMENT_TYPE_TWO_CHECKOUT_ID)
      $currency_id = Model_Constant::PAYMENT_TYPE_TWO_CHECKOUT_DEFAULT_CURRENCY_ID;
    if($payment_type_id == Model_Constant::PAYMENT_TYPE_BANK_DEPOSIT_ID)
      $currency_id = Model_Constant::PAYMENT_TYPE_BANK_DEPOSIT_CURRENCY_ID;

    return $currency_id;
  }

}
