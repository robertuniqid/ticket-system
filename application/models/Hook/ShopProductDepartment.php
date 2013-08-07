<?php

/**
 * Model_Hook_ShopProductDepartment
 *
 * Access Model_Hook_ShopProductDepartment - internal functions
 *
 * @author Robert
 */
class Model_Hook_ShopProductDepartment {

  public static $_information_rules = array(
    'name'         =>  array(Model_Operation_Validation::REQUIRED),
    'description'  =>  array(Model_Operation_Validation::REQUIRED),
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
  public static function addDepartment(array $information){
    $information = Model_Operation_Validation::cleanInformation($information);


    $error_list = Model_Operation_Validation::validateInformation($information, self::$_information_rules);

    if(empty($error_list)){
      $product_department_id = Model_ProductDepartment::insertRecord($information);
    }

    return $error_list;
  }

  /**
   * @static
   * @param array $information
   * @param int $product_department_id
   * @return array
   */
  public static function editDepartment(array $information, $product_department_id){
    $information = Model_Operation_Validation::cleanInformation($information);


    $error_list = Model_Operation_Validation::validateInformation($information, self::$_information_rules);

    if(empty($error_list)){
      Model_ProductDepartment::updateRecord($information, $product_department_id);
    }

    return $error_list;
  }

  /**
   * @static
   * @TODO Implement logic to not allow delete if has categories binded
   * @param $product_department_id
   * @return array
   */
  public static function deleteDepartment($product_department_id) {
    $errors = array();

    Model_ProductDepartment::deleteById($product_department_id);

    return $errors;
  }

}
