<?php

/**
 * Model_Hook_User
 *
 * Access Model_Hook_User - internal functions
 *
 * @author Robert
 */
class Model_Hook_User {

  public static $_information_rules_add = array(
    'first_name'         =>  array(Model_Operation_Validation::REQUIRED),
    'last_name'          =>  array(Model_Operation_Validation::REQUIRED),
    'email_address'      =>  array(Model_Operation_Validation::REQUIRED),
    'username'           =>  array(Model_Operation_Validation::REQUIRED,
                                    Model_Operation_Validation::MIN_LENGTH => Model_Constant::USER_USERNAME_MIN_LENGTH),
    'password'           =>  array(Model_Operation_Validation::REQUIRED,
                                    Model_Operation_Validation::MIN_LENGTH => Model_Constant::USER_PASSWORD_MIN_LENGTH),
    'hidden'       =>  array(
        Model_Operation_Validation::REQUIRED,
        Model_Operation_Validation::SELECT_RESTRICTION  => Model_Constant::ITEM_UNDEFINED,
        Model_Operation_Validation::ACCEPTED_VALUES     => array( Model_Constant::ITEM_HIDDEN ,
          Model_Constant::ITEM_VISIBLE )
    )
  );

  public static $_information_rules_edit = array(
    'first_name'         =>  array(Model_Operation_Validation::REQUIRED),
    'last_name'          =>  array(Model_Operation_Validation::REQUIRED),
    'email_address'      =>  array(Model_Operation_Validation::REQUIRED),
    'username'           =>  array(Model_Operation_Validation::REQUIRED,
      Model_Operation_Validation::MIN_LENGTH => Model_Constant::USER_USERNAME_MIN_LENGTH),
    'password'           =>  array(Model_Operation_Validation::MIN_LENGTH => Model_Constant::USER_PASSWORD_MIN_LENGTH),
    'hidden'       =>  array(
      Model_Operation_Validation::REQUIRED,
      Model_Operation_Validation::SELECT_RESTRICTION  => Model_Constant::ITEM_UNDEFINED,
      Model_Operation_Validation::ACCEPTED_VALUES     => array( Model_Constant::ITEM_HIDDEN ,
        Model_Constant::ITEM_VISIBLE )
    )
  );

  public static $_change_password_rules = array(
    'password'           =>  array(Model_Operation_Validation::MIN_LENGTH => Model_Constant::USER_PASSWORD_MIN_LENGTH)
  );

  /**
   * @static
   * @param array $information
   * @param array $options
   * @return array
   */
  public static function addUser(array $information, array $options = array()){
    $information = Model_Operation_Validation::cleanInformation($information);


    $error_list = Model_Operation_Validation::validateInformation($information, self::$_information_rules_add);

    if(empty($error_list)){
      if(isset($information['password']))
        $information['password'] = sha1($information['password']);

        $user_id = Model_User::insertRecord($information);
        Model_Hook_User::createUserFundsAccount($user_id);

      if(isset($options['platform_information']))
        self::updatePlatformInformation($user_id, $options['platform_information']);
    }

    return $error_list;
  }

  /**
   * @static
   * @param array $information
   * @param int $user_id
   * @param array $options
   * @return array
   */
  public static function editUser(array $information, $user_id, $options){
    $information = Model_Operation_Validation::cleanInformation($information);

    if($information['password'] == '')
       unset($information['password']);

    $error_list = Model_Operation_Validation::validateInformation($information, self::$_information_rules_edit);

    if(empty($error_list)){
      if(isset($information['password']))
        $information['password'] = sha1($information['password']);

      Model_User::updateRecord($information, $user_id);

      if(isset($options['platform_information']))
        self::updatePlatformInformation($user_id, $options['platform_information']);
    }

    return $error_list;
  }

  public static function updatePlatformInformation($user_id, $list) {
    foreach($list as $platform_id => $value)
        Model_Hook_UserPlatform::setPlatformTime($user_id, $platform_id, $value);
   }

  /**
   * @static
   * @TODO Implement logic to not allow delete if has unsent paid orders
   * @param $user_id
   * @return array
   */
  public static function deleteUser($user_id) {
    $errors = array();

    Model_User::deleteById($user_id);

    return $errors;
  }

  /**
   * @param $user_id
   * @param $new_password
   * @param $confirm_password
   * @return array
   */
  public static function changePassword($user_id, $new_password, $confirm_password){
    $error_list = Model_Operation_Validation::validateInformation(array('password'  =>  $new_password), self::$_change_password_rules);

    if(!empty($error_list))
      return $error_list;

    if($new_password != $confirm_password)
      $error_list['confirm_password'] = Model_Interface_StringsUser::getInterfaceString('profile_notification_password_not_match');

    if(empty($error_list))
      Model_User::updateRecord(array('password' =>  sha1($new_password)), $user_id);

    return $error_list;
  }

  public static function createUserFundsAccount($user_id){
    $currency_list = Model_Currency::getAll();

    foreach($currency_list as $currency) {
      Model_UserFunds::insertRecord(array(
        'user_id'     =>  $user_id,
        'currency_id' =>  $currency['id'],
        'amount'      =>  0
      ));
    }
  }

  public static function fixUserFundsAccounts() {
    $currency_list = Model_Operation_Array::mapByParam(Model_Currency::getAll(), 'id');

    foreach(Model_User::getAll() as $user) {
      $user_funds = Model_UserFunds::getAllByUserId($user['id']);

      $has_funds = array();

      foreach($user_funds as $user_fund) {
        $has_funds[] = $user_fund['currency_id'];
      }

      foreach($currency_list as $currency) {
        if(!in_array($currency['id'], $has_funds)) {
          Model_UserFunds::insertRecord(array(
            'user_id'     =>  $user['id'],
            'currency_id' =>  $currency['id'],
            'amount'      =>  0
          ));
        }
      }
    }
  }


}
