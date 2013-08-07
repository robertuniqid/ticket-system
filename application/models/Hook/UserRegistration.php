<?php

/**
 * Model_Hook_User
 *
 * Access Model_Hook_User - internal functions
 *
 * @author Robert
 */
class Model_Hook_UserRegistration {

  public static $_information_rules = array(
    'first_name'         =>  array(Model_Operation_Validation::REQUIRED),
    'last_name'          =>  array(Model_Operation_Validation::REQUIRED),
    'email_address'      =>  array(Model_Operation_Validation::REQUIRED,
                                   Model_Operation_Validation::EMAIL_ADDRESS),
    'password'           =>  array(Model_Operation_Validation::REQUIRED,
      Model_Operation_Validation::MIN_LENGTH => Model_Constant::USER_PASSWORD_MIN_LENGTH),
    'confirm_password'           =>  array(Model_Operation_Validation::REQUIRED,
                                           Model_Operation_Validation::PASSWORD_CONFIRM => 'password'),
  );

  /**
   * @param $information
   * @return array
   */
  public static function getInformationRulesAccordingToData($information){

    $information_rules = self::$_information_rules;

    if(isset($information['email_address'])) {
      $user_test = Model_User::getByEmailAddress($information['email_address']);

      if(!empty($user_test))
        $information_rules['email_address'][] = Model_Operation_Validation::EMAIL_ADDRESS_TAKEN;
    }

    return $information_rules;
  }

  public static $_allowed_fields = array(
    'first_name', 'last_name', 'email_address', 'password', 'confirm_password'
  );

  /**
   * @static
   * @param array $information
   * @return array
   */
  public static function register(array $information){
    $information = Model_Operation_Validation::cleanInformation($information);

    $information = self::_parseInformation($information);

    $error_list = Model_Operation_Validation::validateInformation($information, self::getInformationRulesAccordingToData($information));

    if(empty($error_list)){
      $name         = $information['first_name'].' '.$information['last_name'];
      $password     = $information['password'];
      $username     = $email_address = $information['email_address'];


      if(isset($information['password']))
        $information['password'] = sha1($information['password']);
      if(isset($information['confirm_password']))
        unset($information['confirm_password']);

      $information = array_merge_recursive($information, self::_getDefaults());



      if(!isset($information['username']))
        $information['username'] = $information['email_address'];

      $affiliate_id = self::_getAffiliateId($information);

      $information['referred_by_user_id'] = $affiliate_id;

      $user_id = Model_User::insertRecord($information);

      Model_Hook_User::createUserFundsAccount($user_id);

      Model_Operation_Email::getInstance()->emailNotificationSuccessRegister($name, $username, $password, $email_address);

      Model_Operation_Auth::getInstance()->processLogin($information['username'], $password);
    }

    return $error_list;
  }

  private static function _parseInformation($information){
    $differences = array_diff(array_keys($information), self::$_allowed_fields);

    foreach($differences as $difference)
      unset($information[$difference]);

    return $information;
  }

  /**
   * @return array
   */
  private static function _getDefaults(){
    $ret = array(
      'user_role_id'    =>  Model_Constant::REGISTERED_DEFAULT_ROLE_ID,
      'hidden'          =>  Model_Constant::REGISTERED_DEFAULT_HIDDEN,
      'is_confirmed'    =>  Model_Constant::REGISTERED_DEFAULT_CONFIRMED
    );

    return $ret;
  }

  private static function _getAffiliateId($user_information) {
    if(isset(Model_Helper_Session::get()->direct_buy))
      return Model_Helper_Session::get()->direct_buy;

    $affiliate_id = 0;

    $auto_binding = Model_ReferralBinding::getByEmailAddress($user_information['email_address']);

    if(!empty($auto_binding)) {
      $affiliate_id = $auto_binding['referral_user_id'];

      return $affiliate_id;
    }

    if(!is_numeric(Model_Helper_Session::get()->affiliate_id))
      $affiliate_id  =  Model_Helper_Session::get()->affiliate_id;
    else if(is_numeric(Model_Helper_Request::getCurrentRequest()->getCookie('affiliate_user_id')))
      $affiliate_id  = Model_Helper_Request::getCurrentRequest()->getCookie('affiliate_user_id');



    if($affiliate_id == 0) {
      $smart_query = Model_ReferralTracking::getReferralInformation($_SERVER['REMOTE_ADDR']);

      if(!empty($smart_query))
        $affiliate_id = $smart_query['referral_user_id'];
    }

    return $affiliate_id;
  }

}
