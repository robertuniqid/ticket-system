<?php

/**
 * Model_Hook_UserNotifications
 *
 * Access Model_Hook_UserNotifications - internal functions
 *
 * @author Robert
 */
class Model_Hook_UserNotifications {

  private static $_available_messages = array(
    'success' => array('message', 'user_message'),
    'error'   => array('error_message'),
    'info'    => array('notify_message'),
  );

  public static function setError($error) {
    Model_Helper_Session::get()->error_message = $error;
  }

  public static function setSuccess($message) {
    Model_Helper_Session::get()->message = $message;
  }

  public static function setInfo($message) {
    Model_Helper_Session::get()->notify_message = $message;
  }

  public static function fetchUserAlerts(){
    $message = false;

    foreach(self::$_available_messages as $message_type =>  $message_keys) {
      foreach($message_keys as $message_key) {
        if(isset(Model_Helper_Session::get()->$message_key)) {

          $message = Model_Helper_Session::get()->$message_key;

          unset(Model_Helper_Session::get()->$message_key);

          return array(
            'type'    =>  $message_type,
            'message' =>  $message
          );
        }
      }
    }

    return $message;
  }

  public static function unsetAllMessages() {
    foreach(self::$_available_messages as $message_keys)
      foreach($message_keys as $message_key)
        if(isset(Model_Helper_Session::get()->$message_key))
          unset(Model_Helper_Session::get()->$message_key);
  }

}
