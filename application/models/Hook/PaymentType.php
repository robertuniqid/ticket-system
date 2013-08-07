<?php

/**
 * Model_Hook_PaymentType
 *
 * Access Model_Hook_PaymentType - internal functions
 *
 * @author Robert
 */
class Model_Hook_PaymentType {

  public static $_information_rules = array(
    'name'         =>  array(Model_Operation_Validation::REQUIRED),
    'description'  =>  array(Model_Operation_Validation::REQUIRED),
    'hidden'       =>  array(
      Model_Operation_Validation::REQUIRED,
      Model_Operation_Validation::SELECT_RESTRICTION  => Model_Constant::ITEM_UNDEFINED,
      Model_Operation_Validation::ACCEPTED_VALUES     => array( Model_Constant::ITEM_HIDDEN ,
                                                                Model_Constant::ITEM_VISIBLE )
    ),
  );

  /**
   * @static
   * @param array $information
   * @param array $extra_field_ids
   * @param array $payment_prices
   * @return array
   */
  public static function addPaymentType(array $information, array $extra_field_ids, array $payment_prices = array()){
    $information = Model_Operation_Validation::cleanInformation($information);


    $error_list = Model_Operation_Validation::validateInformation($information, self::$_information_rules);

    if(empty($error_list)){
      $payment_type_id = Model_PaymentType::insertRecord($information);

      Model_PaymentTypeHasPaymentTypeField::insertRecords($payment_type_id, $extra_field_ids);

      if(!empty($payment_prices))
        self::_insertPaymentTypePrices($payment_prices, $payment_type_id);
    }

    return $error_list;
  }

  /**
   * @static
   * @param array $information
   * @param $payment_type_id
   * @param array $extra_field_ids
   * @param array $payment_prices
   * @return array
   */
  public static function editPaymentType(array $information,
                                         $payment_type_id,
                                         array $extra_field_ids = array(),
                                         array $payment_prices = array()){
    $information = Model_Operation_Validation::cleanInformation($information);


    $error_list = Model_Operation_Validation::validateInformation($information, self::$_information_rules);

    if(empty($error_list)){
      Model_PaymentType::updateRecord($information, $payment_type_id);

      Model_PaymentTypeHasPaymentTypeField::updateRecords($payment_type_id, $extra_field_ids);

      Model_PaymentTypePrice::deleteByPaymentTypeId($payment_type_id);

      if(!empty($payment_prices)){
        self::_insertPaymentTypePrices($payment_prices, $payment_type_id);
      }
    }

    return $error_list;
  }

  /**
   * @param array $payment_prices
   * @param $payment_type_id
   */
  private static function _insertPaymentTypePrices(array $payment_prices, $payment_type_id){
    foreach($payment_prices as $payment_price)
      Model_PaymentTypePrice::insertRecord(array(
        'payment_type_id'   =>  $payment_type_id,
        'currency_id'       =>  $payment_price['currency_id'],
        'extra_cost'        =>  $payment_price['extra_cost'],
        'is_fixed'          =>  $payment_price['is_fixed'],
      ));
  }

  /**
   * @static
   * @TODO Implement logic to not allow delete if has on-going orders binded
   * @param $payment_field_id
   * @return array
   */
  public static function deletePaymentType($payment_field_id) {
    $errors = array();

    Model_PaymentType::deleteById($payment_field_id);

    return $errors;
  }

}
