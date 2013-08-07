<?php

/**
 * Model_Hook_PaymentTypeOrderStatus
 *
 * Access Model_Hook_PaymentTypeOrderStatus - internal functions
 *
 * @author Robert
 */
class Model_Hook_PaymentTypeOrderStatus {

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
   * @static
   * @param array $information
   * @return array
   */
  public static function addOrderStatus(array $information){
    $information = Model_Operation_Validation::cleanInformation($information);


    $error_list = Model_Operation_Validation::validateInformation($information, self::$_information_rules);

    if(empty($error_list)){
      $field_id = Model_PaymentTypeOrderStatus::insertRecord($information);
    }

    return $error_list;
  }

  /**
   * @static
   * @param array $information
   * @param int $payment_type_order_status_id
   * @return array
   */
  public static function editOrderStatus(array $information, $payment_type_order_status_id){
    $information = Model_Operation_Validation::cleanInformation($information);


    $error_list = Model_Operation_Validation::validateInformation($information, self::$_information_rules);

    if(empty($error_list)){
      Model_PaymentTypeOrderStatus::updateRecord($information, $payment_type_order_status_id);
    }

    return $error_list;
  }

  /**
   * @static
   * @param int $notify_user
   * @param int $payment_type_order_status_id
   * @return array
   */
  public static function updateNotifyUser($notify_user, $payment_type_order_status_id){
    $error_list = Model_Operation_Validation::validateInformation(array('notify_user' =>  $notify_user), self::$_information_rules_notify_user);

    if(empty($error_list)){
      Model_PaymentTypeOrderStatus::updateRecord(array('notify_user' => $notify_user), $payment_type_order_status_id);
    }

    return $error_list;
  }

  /**
   * @static
   * @param int $email_message
   * @param int $payment_type_order_status_id
   * @return array
   */
  public static function updateEmailMessage($email_message, $payment_type_order_status_id){
    $error_list = Model_Operation_Validation::validateInformation(array('email_message' =>  $email_message), self::$_information_rules_email_message);

    if(empty($error_list)){
      Model_PaymentTypeOrderStatus::updateRecord(array('email_message' => $email_message), $payment_type_order_status_id);
    }

    return $error_list;
  }

}
