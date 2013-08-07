<?php

/**
 * Model_Hook_PaymentType
 *
 * Access Model_Hook_PaymentType - internal functions
 *
 * @author Robert
 */
class Model_Hook_Product {

  public static $_information_rules = array(
    'name'                   =>  array(Model_Operation_Validation::REQUIRED),
    'alias'                  =>  array(Model_Operation_Validation::REQUIRED),
    'description'            =>  array(Model_Operation_Validation::REQUIRED),
    'technical_description'  =>  array(Model_Operation_Validation::REQUIRED),
    'hidden'       =>  array(
      Model_Operation_Validation::REQUIRED,
      Model_Operation_Validation::SELECT_RESTRICTION  => Model_Constant::ITEM_UNDEFINED,
      Model_Operation_Validation::ACCEPTED_VALUES     => array( Model_Constant::ITEM_HIDDEN ,
        Model_Constant::ITEM_VISIBLE )
    )
  );

  /**
   * @param array $information
   * @param array $product_price
   * @param array $product_percent | array('user_id'  => 'percent')
   * @param array $product_category_ids
   * @param array $product_shipping_ids
   * @return array
   */
  public static function addProduct(array $information,
                                    array $product_price = array(),
                                    array $product_percent = array(),
                                    array $product_category_ids = array(),
                                    array $product_shipping_ids = array()){
    $information = Model_Operation_Validation::cleanInformation($information);


    $error_list = Model_Operation_Validation::validateInformation($information, self::$_information_rules);
    $error_list = array_merge_recursive($error_list, self::_validationProductPrice($product_price));

    if((array_sum($product_percent) - (isset($product_percent[0]) ? $product_percent[0] : 0)) !== 100)
      $error_list['percent_information'] = str_replace('%%%current_sum%%%',
                                                array_sum($product_percent),
                                                Model_Interface_StringsShopAdministration::getInterfaceString('product_error_invalid_user_revenue'));

    if(empty($error_list)){
      $product_id = Model_Product::insertRecord($information);

      self::_insertProductPercent($product_id, $product_percent);

      self::_insertProductPrice($product_id, $product_price);

      Model_ProductHasProductCategory::insertRecords($product_id, $product_category_ids);

      Model_ProductHasShippingType::insertRecords($product_id, $product_shipping_ids);

    }

    return $error_list;
  }

  /**
   * @param array $information
   * @param int   $product_id
   * @param array $product_price
   * @param array $product_percent | array('user_id'  => 'percent')
   * @param array $product_category_ids
   * @param array $product_shipping_ids
   * @return array
   */
  public static function editProduct(array $information,
                                           $product_id,
                                     array $product_price = array(),
                                     array $product_percent = array(),
                                     array $product_category_ids = array(),
                                     array $product_shipping_ids = array()){

    $information = Model_Operation_Validation::cleanInformation($information);


    $error_list = Model_Operation_Validation::validateInformation($information, self::$_information_rules);

    if((array_sum($product_percent) - (isset($product_percent[0]) ? $product_percent[0] : 0)) !== 100)
      $error_list['global_error'] = str_replace('%%%current_sum%%%',
        array_sum($product_percent),
        Model_Interface_StringsShopAdministration::getInterfaceString('product_error_invalid_user_revenue'));

    if(empty($error_list)){
      Model_Product::updateRecord($information, $product_id);

      self::_flushAndInsertProductPercent($product_id, $product_percent);

      self::_flushAndInsertProductPrice($product_id, $product_price);

      Model_ProductHasProductCategory::flushAndInsertRecords($product_id, $product_category_ids);

      Model_ProductHasShippingType::flushAndInsertRecords($product_id, $product_shipping_ids);
    }

    return $error_list;
  }

  /**
   * @param array $product_price_list
   * @return array $error_list
   */
  private static function _validationProductPrice(array $product_price_list){
    $error_list = array();

    foreach($product_price_list as $currency_id  =>  $product_price)
      if($product_price['price'] == 0)
        $error_list[$currency_id]  = Model_Interface_StringsShopAdministration::getInterfaceString('product_error_price_is_required');
      elseif($product_price['price'] - $product_price['price_manufacture'] < 0)
        $error_list[$currency_id]  = Model_Interface_StringsShopAdministration::getInterfaceString('product_error_price_is_lower_than_manufacture_price');


    return empty($error_list) ? array() : array('product_percent' =>  $error_list);
  }

  /**
   * @param int $product_id
   * @param array $product_price_list
   */
  private static function _insertProductPrice($product_id, array $product_price_list){
    if(!empty($product_price_list))
      foreach($product_price_list as $currency_id =>  $product_price)
        Model_ProductPrice::insertRecord(array(
          'product_id'       =>  $product_id,
          'currency_id'      =>  $currency_id,
          'price'            =>  $product_price['price'],
          'price_manufacture'=>  $product_price['price_manufacture'],
          'bonus_points'     =>  isset($product_price['bonus_points']) ? $product_price['bonus_points'] : 0,
        ));
  }

  /**
   * @param int $product_id
   * @param array $product_price_list
   */
  private static function _flushAndInsertProductPrice($product_id, array $product_price_list){
    Model_ProductPrice::deleteByProductId($product_id);
    self::_insertProductPrice($product_id, $product_price_list);
  }

  /**
   * @param int $product_id
   * @param array $product_percent
   */
  private static function _insertProductPercent($product_id, array $product_percent){
    if(!empty($product_percent))
      foreach($product_percent as $user_id  =>  $percent)
        Model_ProductPercent::insertRecord(array(
          'product_id'   =>  $product_id,
          'user_id'      =>  $user_id,
          'percent'      =>  $percent,
        ));
  }

  /**
   * @param int $product_id
   * @param array $product_percent
   */
  private static function _flushAndInsertProductPercent($product_id, array $product_percent){
    Model_ProductPercent::deleteByProductId($product_id);
    self::_insertProductPercent($product_id, $product_percent);
  }

  /**
   * @static
   * @TODO Implement logic to not allow delete if has on-going orders binded
   * @param $product_id
   * @return array
   */
  public static function deleteProduct($product_id) {
    $errors = array();

    Model_Product::deleteById($product_id);

    return $errors;
  }

}
