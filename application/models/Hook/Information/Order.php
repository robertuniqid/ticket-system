<?php

/**
 * Model_Hook_Information_PaymentType
 *
 * Access Model_Hook_Information_PaymentType - internal functions
 *
 * @author Robert
 */
class Model_Hook_Information_Order {

  public static function wrapMessageWithInformation($content, $order_information) {
      if(is_numeric($order_information))
        $order_information = Model_Order::getById($order_information);

      if(strpos($content, '%%%name%%%') !== false) {
        $user_information = Model_User::getById($order_information['user_id']);

        $content = str_replace('%%%name%%%', $user_information['first_name'].' '.$user_information['last_name'], $content);
      }

    if(strpos($content, '%%%email_address%%%') !== false) {
      $user_information = Model_User::getById($order_information['user_id']);

      $content = str_replace('%%%email_address%%%', $user_information['email_address'], $content);
    }

      if(strpos($content, '%%%order_currency_name%%%') !== false) {
        $currency = Model_Currency::getById($order_information['currency_id']);

        $content = str_replace('%%%order_currency_name%%%', $currency['short_name'], $content);
      }

      if(strpos($content, '%%%product_information%%%') !== false) {
        $ordered_products = Model_OrderedProduct::getAllByOrderId($order_information['id']);

        $content = str_replace('%%%product_information%%%',
                               Model_Helper_View::getObject()->partial('shop/email_order_information/ordered_product.phtml',
                                                                      array(
                                                                        'ordered_products'      =>  $ordered_products,
                                                                        'currency_information'  =>  Model_Currency::getById($order_information['currency_id'])
                                                                      )),
                               $content);
      }

      if(strpos($content, '%%%product_information_billing%%%') !== false) {
        $ordered_products = isset($ordered_products) ? $ordered_products : Model_OrderedProduct::getAllByOrderId($order_information['id']);

        $content = str_replace('%%%product_information_billing%%%',
          Model_Helper_View::getObject()->partial('shop/email_order_information/ordered_product_billing.phtml',
            array(
              'ordered_products'      =>  $ordered_products,
              'order_information'     =>  $order_information,
              'currency_information'  =>  Model_Currency::getById($order_information['currency_id'])
            )),
          $content);
      }

      if(strpos($content, '%%%order_id%%%') !== false) {
        $content = str_replace('%%%order_id%%%', $order_information['id'], $content);
      }

      if(strpos($content, '%%%order_price%%%') !== false) {
        $content = str_replace('%%%order_price%%%', $order_information['total_price'], $content);
      }

      return $content;
  }

}
