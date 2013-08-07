<?php

/**
 * Model_Hook_UserType
 *
 * Access Model_Hook_UserType - internal functions
 *
 * @author Robert
 */
class Model_Hook_UserType {

  public static $_information_rules = array(
    'name'         =>  array(Model_Operation_Validation::REQUIRED),
    'description'  =>  array(Model_Operation_Validation::REQUIRED)
  );

  /**
   * @static
   * @param array $information
   * @param array $action_access_ids
   * @return array
   */
  public static function addUserType(array $information, array $action_access_ids){
    $information = Model_Operation_Validation::cleanInformation($information);


    $error_list = Model_Operation_Validation::validateInformation($information, self::$_information_rules);

    if(empty($error_list)){
      $user_type_id = Model_UserRole::insertRecord($information);

      Model_UserRoleHasActionAccess::insertRecords($action_access_ids, $user_type_id);

      Model_Operation_Acl::getInstance()->buildFreshMap();
    }

    return $error_list;
  }

  /**
   * @static
   * @param array $information
   * @param $user_type_id
   * @param array $action_access_ids
   * @return array
   */
  public static function editUserType(array $information, $user_type_id, array $action_access_ids){
    $information = Model_Operation_Validation::cleanInformation($information);


    $error_list = Model_Operation_Validation::validateInformation($information, self::$_information_rules);

    if(empty($error_list)){
      Model_UserRole::updateRecord($information, $user_type_id);

      Model_UserRoleHasActionAccess::flushAndInsertRecords($action_access_ids, $user_type_id);

      Model_Operation_Acl::getInstance()->buildFreshMap();
    }

    return $error_list;
  }

  /**
   * @static
   * @TODO Implement logic to not allow delete if has users binded
   * @param $user_type_id
   * @return array
   */
  public static function deletePaymentType($user_type_id) {
    $errors = array();

    Model_UserRole::deleteById($user_type_id);

    return $errors;
  }

  /**
   * Return an array that represents the mapping of the application, this is needed to edit the user permissions over the application
   * @static
   * @return array
   */
  public static function getAccessPermissionsMap(){
    $access_permissions_map = array();

    $module_list = Model_Module::getAll();
    $controller_list = Model_Operation_Array::mapByParam(Model_Controller::getAll(array()), 'module_id', true, true);
    $action_list     = Model_Operation_Array::mapByParam(Model_Action::getAll(array()), 'controller_id', true, true);

    foreach($module_list as $module){
      if(isset($controller_list[$module['id']])){
        $access_permissions_map[$module['name']] =  array();

        foreach($controller_list[$module['id']] as $key  =>  $controller){
          if(isset($action_list[$controller['id']])){
            $access_permissions_map[$module['name']][$controller['name']] =
              Model_Operation_Array::composeKeyToValue($action_list[$controller['id']], 'id', 'name');
          }
        }
      }
    }
    return $access_permissions_map;
  }

}
