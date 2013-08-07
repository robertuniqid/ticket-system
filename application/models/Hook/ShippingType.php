<?php

/**
 * Model_Hook_ShippingType
 *
 * Access Model_Hook_ShippingType - internal functions
 *
 * @author Robert
 */
class Model_Hook_ShippingType {

  public static $_information_rules = array(
    'name'         =>  array(Model_Operation_Validation::REQUIRED),
    'description'  =>  array(Model_Operation_Validation::REQUIRED),
    'hidden'       =>  array(
      Model_Operation_Validation::REQUIRED,
      Model_Operation_Validation::SELECT_RESTRICTION  => Model_Constant::ITEM_UNDEFINED,
      Model_Operation_Validation::ACCEPTED_VALUES     => array( Model_Constant::ITEM_HIDDEN ,
        Model_Constant::ITEM_VISIBLE )
    ),
    'extra_cost'  => array(Model_Operation_Validation::NUMERIC)
  );

  /**
   * @static
   * @param array $information
   * @param array $extra_field_ids
   * @param array $responsible_users_ids
   * @param array $available_payment_types
   * @param array $payment_prices
   * @return array
   */
  public static function addShippingType(array $information,
                                         array $extra_field_ids = array(),
                                         array $responsible_users_ids = array(),
                                         array $available_payment_types = array(),
                                         array $payment_prices = array()){
    $information = Model_Operation_Validation::cleanInformation($information);


    $error_list = Model_Operation_Validation::validateInformation($information, self::$_information_rules);

    if(empty($error_list)){
      $shipping_type_id = Model_ShippingType::insertRecord($information);

      Model_ShippingTypeHasShippingExtraFields::insertRecords($shipping_type_id, $extra_field_ids);
      Model_ShippingTypeHasUser::insertRecords($shipping_type_id, $responsible_users_ids);
      Model_ShippingTypeHasPaymentType::insertRecords($shipping_type_id, $available_payment_types);

      self::_insertShippingTypePrices($payment_prices, $shipping_type_id);
    }

    return $error_list;
  }

  /**
   * @static
   * @param array $information
   * @param $shipping_type_id
   * @param array $extra_field_ids
   * @param array $responsible_users_ids
   * @param array $available_payment_types
   * @param array $payment_prices
   * @return array
   */
  public static function editShippingType(array $information,
                                          $shipping_type_id,
                                          array $extra_field_ids = array(),
                                          array $responsible_users_ids = array(),
                                          array $available_payment_types = array(),
                                          array $payment_prices = array()){
    $information = Model_Operation_Validation::cleanInformation($information);


    $error_list = Model_Operation_Validation::validateInformation($information, self::$_information_rules);

    if(empty($error_list)){
      Model_ShippingType::updateRecord($information, $shipping_type_id);

      Model_ShippingTypeHasShippingExtraFields::flushAndInsertRecords($shipping_type_id, $extra_field_ids);
      Model_ShippingTypeHasUser::flushAndInsertRecords($shipping_type_id, $responsible_users_ids);
      Model_ShippingTypeHasPaymentType::flushAndInsertRecords($shipping_type_id, $available_payment_types);

      self::_flushAndInsertShippingTypePrices($payment_prices, $shipping_type_id);
    }

    return $error_list;
  }

  /**
   * @param $payment_prices
   * @param $shipping_type_id
   */
  private static function _insertShippingTypePrices($payment_prices, $shipping_type_id){
    if(!empty($payment_prices))
      foreach($payment_prices as $payment_price)
        Model_ShippingTypePrice::insertRecord(array(
          'shipping_type_id'  =>  $shipping_type_id,
          'currency_id'       =>  $payment_price['currency_id'],
          'extra_cost'        =>  $payment_price['extra_cost'],
          'is_fixed'          =>  $payment_price['is_fixed'],
        ));
  }

  /**
   * @param $payment_prices
   * @param $shipping_type_id
   */
  private static function _flushAndInsertShippingTypePrices($payment_prices, $shipping_type_id){
    Model_ShippingTypePrice::deleteByShippingTypeId($shipping_type_id);
    self::_insertShippingTypePrices($payment_prices, $shipping_type_id);
  }

  /**
   * @static
   * @TODO Implement logic to not allow delete if has on-going orders
   * @param $Shipping_field_id
   * @return array
   */
  public static function deleteShippingType($Shipping_field_id) {
    $errors = array();

    Model_ShippingType::deleteById($Shipping_field_id);

    return $errors;
  }

  /**
   * @static
   * @return array
   */
  public static function getUserShippingResponsibleCandidatesMapped(){
    $responsible_user_role_ids = Model_Helper_Settings::getDeliveryResponsibleUserRoleIds();

    $user_roles = Model_UserRole::getAll($responsible_user_role_ids);

    $responsible_users = Model_User::getAllByUserRoleIds($responsible_user_role_ids, Model_Constant::ITEM_VISIBLE, Model_Constant::ITEM_NOT_DELETED, 'first_name ASC');

    $responsible_users_mapped_by_role_id = Model_Operation_Array::mapByParam($responsible_users, 'user_role_id', false, true);

    $return = array();

    foreach($user_roles as $user_role){
      $list = array();

      if(isset($responsible_users_mapped_by_role_id[$user_role['id']]))
          $list = Model_Operation_Array::composeKeyToValue($responsible_users_mapped_by_role_id[$user_role['id']], 'id', array('first_name', ' ' , 'last_name'));

      $return[$user_role['name']] = $list;
    }
    return $return;
  }

}
