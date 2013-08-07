<?php

/**
 * Model_Hook_ShippingTypeOrderStatus
 *
 * Access Model_Hook_ShippingTypeOrderStatus - internal functions
 *
 * @author Robert
 */
class Model_Hook_ShippingTypeOrderStatus {

  public static $_information_rules = array(
    'name'         =>  array(Model_Operation_Validation::REQUIRED)
  );

  public static $_information_rules_notify_user = array(
    'notify_user'         =>  array(Model_Operation_Validation::ACCEPTED_VALUES =>  array(0,1))
  );

  public static $_information_rules_email_message = array(
    'email_message'         =>  array(Model_Operation_Validation::REQUIRED)
  );

  /**
   * @param array $information
   * @param null $field_id
   * @return array
   */
  public static function addOrderStatus(array $information, &$field_id = null){
    $information = Model_Operation_Validation::cleanInformation($information);

    $error_list = Model_Operation_Validation::validateInformation($information, self::$_information_rules);

    if(empty($error_list)){
      if(!isset($information['order']))
        $information['order'] = count(Model_ShippingTypeOrderStatus::getAll()) + 1;

      $field_id = Model_ShippingTypeOrderStatus::insertRecord($information);
    }

    return $error_list;
  }

  /**
   * @static
   * @param array $information
   * @param int $shipping_type_order_status_id
   * @return array
   */
  public static function editOrderStatus(array $information, $shipping_type_order_status_id){
    $information = Model_Operation_Validation::cleanInformation($information);


    $error_list = Model_Operation_Validation::validateInformation($information, self::$_information_rules);

    if(empty($error_list)){
      Model_ShippingTypeOrderStatus::updateRecord($information, $shipping_type_order_status_id);
    }

    return $error_list;
  }

  /**
   * @static
   * @param int $notify_user
   * @param int $shipping_type_order_status_id
   * @return array
   */
  public static function updateNotifyUser($notify_user, $shipping_type_order_status_id){
    $error_list = Model_Operation_Validation::validateInformation(array('notify_user' =>  $notify_user), self::$_information_rules_notify_user);

    if(empty($error_list)){
      Model_ShippingTypeOrderStatus::updateRecord(array('notify_user' => $notify_user), $shipping_type_order_status_id);
    }

    return $error_list;
  }

  /**
   * @static
   * @param int $email_message
   * @param int $shipping_type_order_status_id
   * @return array
   */
  public static function updateEmailMessage($email_message, $shipping_type_order_status_id){
    $error_list = Model_Operation_Validation::validateInformation(array('email_message' =>  $email_message), self::$_information_rules_email_message);

    if(empty($error_list)){
      Model_ShippingTypeOrderStatus::updateRecord(array('email_message' => $email_message), $shipping_type_order_status_id);
    }

    return $error_list;
  }

  /**
   * @TODO Implement the System to NOT allow deletion of ON-GOING Order Statuses
   * @static
   * @param $record_id
   * @return array
   */
  public static function deleteShippingTypeOrderStatus($record_id){
    $errors = array();

    if(empty($errors))
      Model_ShippingTypeOrderStatus::deleteRecord($record_id);

    return $errors;
  }

}
