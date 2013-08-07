<?php

/**
 * Model_Hook_Information_Product
 *
 * Access Model_Hook_Information_Product - internal functions
 *
 * @author Robert
 */
class Model_Hook_Information_Product {

  /**
   * @param $product_information
   * @return array
   */
  public static function getBasketProductInformation($product_information){
    if(empty($product_information))
      return array();

    $basket_product_information = Model_Product::getBasketProductInformation(array_keys($product_information),
                                                               Model_Operation_UserPreferences::getInstance()->display_currency_id
                                                              );

    foreach($basket_product_information as $key =>  $basket_product){
      $basket_product_information[$key]['quantity'] = $product_information[$basket_product['id']];
    }

    return $basket_product_information;
  }

  public static function getProductListWithAffiliateLinks($user_id){
    $product_list = Model_Product::getProductWithAffiliateOptions(Model_Constant::ITEM_VISIBLE, Model_Constant::ITEM_NOT_DELETED);

    $url_ending = '?'.Model_Constant::AFFILIATE_IDENTIFIER_PARAM.'='.Model_Helper_Encoding::safeHexEncoding($user_id);

    foreach($product_list as $key =>  $product)
      if($product_list[$key]['squeeze_page_url'] != '')
        $product_list[$key]['squeeze_page_url'] = $product['squeeze_page_url'].$url_ending;
      else
        unset($product_list[$key]);


    return $product_list;
  }

}
