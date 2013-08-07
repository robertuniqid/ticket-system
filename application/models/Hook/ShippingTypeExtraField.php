<?php

/**
 * Model_Hook_ShippingTypeExtraField
 *
 * Access Model_Hook_ShippingTypeExtraField - internal functions
 *
 * @author Robert
 */
class Model_Hook_ShippingTypeExtraField {

  public static $_information_rules = array(
    'name'         =>  array(Model_Operation_Validation::REQUIRED),
    'hidden'       =>  array(
      Model_Operation_Validation::REQUIRED,
      Model_Operation_Validation::SELECT_RESTRICTION  => Model_Constant::ITEM_UNDEFINED,
      Model_Operation_Validation::ACCEPTED_VALUES     => array( Model_Constant::ITEM_HIDDEN ,
        Model_Constant::ITEM_VISIBLE )
    )
  );

  /**
   * @static
   * @param array $information
   * @return array
   */
  public static function addField(array $information){
    $information = Model_Operation_Validation::cleanInformation($information);


    $error_list = Model_Operation_Validation::validateInformation($information, self::$_information_rules);

    if(empty($error_list)){
      $field_id = Model_ShippingExtraFields::insertRecord($information);
    }
    return $error_list;
  }

  /**
   * @static
   * @param array $information
   * @param int $payment_field_id
   * @return array
   */
  public static function editField(array $information, $payment_field_id){
    $information = Model_Operation_Validation::cleanInformation($information);


    $error_list = Model_Operation_Validation::validateInformation($information, self::$_information_rules);

    if(empty($error_list)){
      Model_ShippingExtraFields::updateRecord($information, $payment_field_id);
    }

    return $error_list;
  }

  /**
   * @static
   * @TODO Implement logic to not allow delete if has payment_type_binded binded
   * @param $payment_field_id
   * @return array
   */
  public static function deleteField($payment_field_id) {
    $errors = array();

    Model_ShippingExtraFields::deleteById($payment_field_id);

    return $errors;
  }

}
