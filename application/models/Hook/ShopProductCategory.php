<?php

/**
 * Model_Hook_ShopProductCategory
 *
 * Access Model_Hook_ShopProductCategory - internal functions
 *
 * @author Robert
 */
class Model_Hook_ShopProductCategory {

  public static $_information_rules = array(
    'name'         =>  array(Model_Operation_Validation::REQUIRED),
    'product_department_id' => array(
                                      Model_Operation_Validation::SELECT_RESTRICTION  => Model_Constant::ITEM_UNDEFINED
                                      ),
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
  public static function addCategory(array $information){
    $information = Model_Operation_Validation::cleanInformation($information);


    $error_list = Model_Operation_Validation::validateInformation($information, self::$_information_rules);

    if(empty($error_list)){
      $product_category_id = Model_ProductCategory::insertRecord($information);
    }

    return $error_list;
  }

  /**
   * @static
   * @param array $information
   * @param int $product_category_id
   * @return array
   */
  public static function editCategory(array $information, $product_category_id){
    $information = Model_Operation_Validation::cleanInformation($information);


    $error_list = Model_Operation_Validation::validateInformation($information, self::$_information_rules);

    if(empty($error_list)){
      Model_ProductCategory::updateRecord($information, $product_category_id);
    }

    return $error_list;
  }

  /**
   * @static
   * @TODO Implement logic to not allow delete if has products binded
   * @param $product_category_id
   * @return array
   */
  public static function deleteCategory($product_category_id) {
    $errors = array();

    Model_ProductCategory::deleteById($product_category_id);

    return $errors;
  }

}
