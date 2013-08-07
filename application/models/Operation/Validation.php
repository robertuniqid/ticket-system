<?php

/**
 * Model_Operation_Validation
 *
 * Access Model_Operation_Validation - internal functions
 *
 * @author Robert
 */
class Model_Operation_Validation {

  const REQUIRED               = "required";
  const MIN_LENGTH             = "min_length";
  const NUMERIC                = "numeric";
  const ALPHANUMERIC           = "alphanumeric";
  const SELECT_RESTRICTION     = "select_restriction";
  const ACCEPTED_VALUES        = "accepted_values";
  const PASSWORD_CONFIRM       = 'password_confirm';
  const EMAIL_ADDRESS          = 'email_address';
  const EMAIL_ADDRESS_TAKEN    = 'email_Address_taken';

  /**
   * @static
   * @param array $information
   * @param array $information_requirements
   * @return array
   */
  public static function validateInformation(array $information, array $information_requirements){
    $errors = array();
    
    foreach($information_requirements as $field_key => $requirements) {
        //// REQUIRED CHECK
      if(in_array(self::REQUIRED, $requirements)
          && ( !isset($information[$field_key]) || $information[$field_key] == "")){
          $errors[$field_key] = Model_Interface_Strings::getInterfaceString('error_field_is_required');
      } elseif(isset($information[$field_key])){
        //// NUMERIC CHECK
        if(in_array(self::NUMERIC, $requirements) && !is_numeric($information[$field_key])){
          $errors[$field_key] = Model_Interface_Strings::getInterfaceString('error_field_is_numeric');
          continue;
        }

        //// MIN LENGTH CHECK
        if(isset($requirements[self::MIN_LENGTH]) && strlen($information[$field_key]) < $requirements[self::MIN_LENGTH]){
          $errors[$field_key] = str_replace('%%%length%%%',
                                            $requirements[self::MIN_LENGTH],
                                            Model_Interface_Strings::getInterfaceString('error_field_is_too_short'));
          continue;
        }

        //// MIN LENGTH CHECK
        if(isset($requirements[self::PASSWORD_CONFIRM]) && $information[$field_key] != $information[$requirements[self::PASSWORD_CONFIRM]]) {
          $errors[$field_key] = Model_Interface_Strings::getInterfaceString('error_field_password_not_match');
          continue;
        }

        //// ALPHANUMERIC CHECK
        if(in_array(self::ALPHANUMERIC, $requirements) && !ctype_alnum($information[$field_key])) {
          $errors[$field_key] = Model_Interface_Strings::getInterfaceString('error_field_is_alphanumeric');
          continue;
        }

        //// SELECT_RESTRICTION CHECK
        if(isset($requirements[self::SELECT_RESTRICTION]) && $requirements[self::SELECT_RESTRICTION] == $information[$field_key]) {
          $errors[$field_key] = Model_Interface_Strings::getInterfaceString('error_field_please_select');
          continue;
        }

        //// ACCEPTED VALUES CHECK
        if(isset($requirements[self::ACCEPTED_VALUES]) && !in_array($information[$field_key], $requirements[self::ACCEPTED_VALUES])) {
          $errors[$field_key] = str_replace(
                                "%values%" , implode(' , ', $requirements[self::ACCEPTED_VALUES]),
                                Model_Interface_Strings::getInterfaceString('error_field_values_restricted'));
          continue;
        }

        //// EMAIL ADDRESS CHECK
        if(in_array(self::EMAIL_ADDRESS, $requirements) && !(self::isValidEmailAddress($information[$field_key]))){
          $errors[$field_key] = Model_Interface_Strings::getInterfaceString('error_field_email_address');
          continue;
        }

        //// EMAIL_ADDRESS_TAKEN
        if(in_array(self::EMAIL_ADDRESS_TAKEN, $requirements)) {
          $errors[$field_key] = Model_Interface_Strings::getInterfaceString('error_field_email_address_taken');
          continue;
        }
      }
    }

    return $errors;
  }

  /**
   * @static
   * @param array $information
   * @return array
   */
  public static function cleanInformation(array $information){
    foreach($information as $key => $value)
      $information[$key] = trim($value);

    return $information;
  }

  /**
   * @var Zend_Validate_EmailAddress
   */
  private static $_email_validation = null;

  /**
   * @param $email
   * @return bool
   */
  public static function isValidEmailAddress($email){
    if(is_null(self::$_email_validation))
      self::$_email_validation = new Zend_Validate_EmailAddress();

    return self::$_email_validation->isValid($email);
  }

}
