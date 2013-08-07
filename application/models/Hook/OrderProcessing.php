<?php

/**
 * Model_Hook_Order
 *
 * Access Model_Hook_Order - internal functions
 *
 * @author Robert
 */
class Model_Hook_OrderProcessing {

  /**
   * @param $order_id
   */
  public static function updateOrderPaymentStatusAfterSuccessfulAutomaticPayment($order_id) {
    $order_information = is_array($order_id) ? $order_id : Model_Order::getById($order_id);

    $order_products = Model_OrderedProduct::getAllByOrderId($order_information['id']);

    $virtual_products = Model_Operation_Array::extractParam($order_products, 'is_virtual', false);

    if(count($order_products) == count($virtual_products)) {
      self::updateOrderPaymentStatus($order_information, Model_Constant::PAYMENT_TYPES_STATUS_COMPLETED_ID);
    } else {
      self::updateOrderPaymentStatus($order_information, Model_Constant::PAYMENT_TYPES_STATUS_PAYMENT_RECEIVED_ID);
    }
  }

  /**
   * @param $order_id
   * @param $payment_status_id
   */
  public static function updateOrderPaymentStatus($order_id, $payment_status_id) {
    $order_information = is_array($order_id) ? $order_id : Model_Order::getById($order_id);

    $user_information = Model_User::getById($order_information['user_id']);

    Model_Order::updateRecord(array('payment_type_status_id'  =>  $payment_status_id),  $order_information['id']);

    $payment_type_status  = Model_PaymentTypeOrderStatus::getById($payment_status_id);

    $title = str_replace('%%%current_status%%%', $payment_type_status['name'], Model_Interface_StringsShop::getInterfaceString('email_notification_title_update_status_change'));
    $email_content = $payment_type_status['email_message'];

    if($payment_status_id == Model_Constant::PAYMENT_TYPES_STATUS_COMPLETED_ID) {
      Model_Order::updateRecord(array('is_cashed' =>  1), $order_information['id']);
      self::awardUserMoney($order_information);

      self::_awardOnlineTime($order_information);
    }

    if($payment_status_id == Model_Constant::PAYMENT_TYPES_STATUS_REFUND_ID) {
      self::_removeOnlineTime($order_information);
    }

    if($payment_status_id == Model_Constant::PAYMENT_TYPES_STATUS_COMPLETED_ID)
      self::_productSpecificEmailTrigger($order_id);

    if($payment_type_status['notify_user'] == 0)
      return false;

    $content = Model_Hook_Information_Order::wrapMessageWithInformation($email_content, $order_information);

    Model_Operation_Email::getInstance()->sendEmail($title, $content, $user_information['email_address']);
  }

  public static function updateOrderShippingStatus($order_id, $shipping_status_id) {
    $order_information = Model_Order::getById($order_id);

    $user_information = Model_User::getById($order_information['user_id']);

    Model_Order::updateRecord(array('shipping_type_status_id'  =>  $shipping_status_id),  $order_id);

    $shipping_type_status  = Model_ShippingTypeOrderStatus::getById($shipping_status_id);

    if($shipping_type_status['notify_user'] == 0)
      return false;

    $title = str_replace('%%%current_status%%%',
                         $shipping_type_status['name'],
                         Model_Interface_StringsShop::getInterfaceString('email_notification_title_update_status_change'));

    $content = Model_Hook_Information_Order::wrapMessageWithInformation($shipping_type_status['email_message'], $order_information);

    Model_Operation_Email::getInstance()->sendEmail($title,
                                                    $content,
                                                    $user_information['email_address']);
  }

  public static function paymentTypeSpecificActions($order_id) {
    $order_information = Model_Order::getById($order_id);
    $payment_type_information = Model_PaymentType::getById($order_information['payment_type_id']);
    $user_information = Model_User::getById($order_information['user_id']);

    if($payment_type_information['email_message_title'] != ''
        && !is_null($payment_type_information['email_message_title'])) {
      $content = Model_Hook_Information_Order::wrapMessageWithInformation($payment_type_information['email_message'], $order_information);

      $pdf_attachment = '';

      if($payment_type_information['has_billing_sheet']) {
        $html = Model_Hook_OrderInvoice::getInstance()->generateInvoiceHTMLByOrderId($order_information['id']);

        $html = str_replace('http://www.stepout.ro/assets/images/stepout_logo.png', ROOT_PATH . '/assets/images/stepout_logo.png', $html);

        $pdf_attachment = Model_Helper_Pdf::getString($html);
      }

      Model_Operation_Email::getInstance()->sendEmail($payment_type_information['email_message_title'],
                                                      $content,
                                                      $user_information['email_address'],
                                                      $pdf_attachment);
    }
  }

  public static function shippingTypeSpecificActions($order_id) {
    $order_information = Model_Order::getById($order_id);
    $shipping_type_information = Model_ShippingType::getById($order_information['shipping_type_id']);
    $user_information = Model_User::getById($order_information['user_id']);

    if($shipping_type_information['email_message_title'] != ''
        && !is_null($shipping_type_information['email_message_title'])) {
      $content = Model_Hook_Information_Order::wrapMessageWithInformation($shipping_type_information['email_message'], $order_information);

      Model_Operation_Email::getInstance()->sendEmail($shipping_type_information['email_message_title'],
                                                      $content,
                                                      $user_information['email_address']);
    }
  }

  public static function awardUserMoney($order_id) {
    if(is_array($order_id))
      $order_information = $order_id;
    else
      $order_information = Model_Order::getById($order_id);

    if($order_information['awarded_contributors_money'] == 1)
      return false;

    $ordered_products = Model_OrderedProduct::getAllByOrderId($order_information['id']);
    $currency_information = Model_Operation_Array::mapByParam(Model_Currency::getAll(), 'id');

    $user_distributions = array();
    $user_distributions_logs = array();

    $affiliate_awarded = 0;

    foreach($ordered_products as $ordered_product){
      $distribution_money = $ordered_product['price'] * $ordered_product['quantity'];

      $percent_information = Model_Operation_Array::mapByParam(Model_ProductPercent::getAllByProductId($ordered_product['product_id']), 'user_id');

      // Referral Selling Case
      if(isset($percent_information[0])) {
        if($order_information['referral_id'] != 0){
          if(!in_array($order_information['referral_id'], explode(',', $order_information['contributors_user_ids'])))  {
            $awarded_credit = $distribution_money * $percent_information[0]['percent'] / 100;
            $user_distributions[$order_information['referral_id']] = isset($user_distributions[$order_information['referral_id']]) ?
              $user_distributions[$order_information['referral_id']] + $awarded_credit : $awarded_credit;

            $user_distributions_logs[$order_information['referral_id']] = Model_Interface_StringsShop::getInterfaceString('label_money_distribution_awarded_row');

            $user_distributions_logs[$order_information['referral_id']] = str_replace(array(
               '%%%amount%%%', '%%%ammount_currency%%%', '%%%product_name%%%'
            ), array(
               $awarded_credit, $currency_information[$order_information['currency_id']]['name'], $ordered_product['quantity'].' x '.$ordered_product['name']
            ), $user_distributions_logs[$order_information['referral_id']]);

            $distribution_money -= $awarded_credit;

            $affiliate_awarded += $awarded_credit;
          }
        }

        unset($percent_information[0]);
      }

      foreach($percent_information as $percent_information_line){
        $user_id = $percent_information_line['user_id'];
        $awarded_credit = $distribution_money * $percent_information_line['percent'] / 100;

        $user_distributions[$user_id] = isset($user_distributions[$user_id]) ?
          $user_distributions[$user_id] + $awarded_credit : $awarded_credit;

        $user_distributions_logs[$user_id] = Model_Interface_StringsShop::getInterfaceString('label_money_distribution_awarded_row');
        $user_distributions_logs[$user_id] = str_replace(array(
          '%%%amount%%%', '%%%ammount_currency%%%', '%%%product_name%%%'
        ), array(
          $awarded_credit, $currency_information[$order_information['currency_id']]['name'], $ordered_product['quantity'].' x '.$ordered_product['name']
        ), $user_distributions_logs[$user_id]);
      }
    }

    foreach($user_distributions as $user_id =>  $user_distribution)
      Model_UserFunds::addFunds($user_distribution, $user_id, $order_information['currency_id']);

    foreach($user_distributions_logs as $user_id =>  $user_distribution_log)
      Model_UserFundsInformation::insertRecord(array(
        'user_id'                       =>  $user_id,
        'amount'                        =>  $user_distributions[$user_id],
        'currency_id'                   =>  $order_information['currency_id'],
        'logged_message'                =>  '+ '.$user_distributions[$user_id].' '.$currency_information[$order_information['currency_id']]['name'],
        'logged_message_description'    =>  $user_distribution_log
      ));

    Model_Order::updateRecord(
      array(
        'awarded_contributors_money'  =>  1,
        'affiliate_price'             =>  $affiliate_awarded
      ),
      $order_information['id']
    );

    return true;
  }

  private static function _awardOnlineTime($order_information) {
    $ordered_products = Model_OrderedProduct::getAllByOrderId($order_information['id']);

    foreach($ordered_products as $ordered_product) {
      $platform_options = Model_ProductPlatformOption::getAllByProductId($ordered_product['product_id']);

      foreach($platform_options as $platform_option) {
        Model_Hook_UserPlatform::addPlatformTime(
          $order_information['user_id'], $platform_option['platform_id'], $platform_option['influence']
        );
      }
    }
  }

  private static function _removeOnlineTime($order_information) {
    $ordered_products = Model_OrderedProduct::getAllByOrderId($order_information['id']);

    foreach($ordered_products as $ordered_product) {
      $platform_options = Model_ProductPlatformOption::getAllByProductId($ordered_product['id']);

      foreach($platform_options as $platform_option) {
        Model_Hook_UserPlatform::addPlatformTime(
          $order_information['user_id'], $platform_option['platform_id'], ($platform_option['influence'] * -1 )
        );
      }
    }
  }

  private static function _productSpecificEmailTrigger($order_id) {
    self::_productSpecificEmailTriggerThankYou($order_id);
    self::_productSpecificEmailTriggerReferAFriend($order_id);

  }

  private static function _productSpecificEmailTriggerThankYou($order_id){
    $order = is_array($order_id) ? $order_id : Model_Order::getById($order_id);

    if($order['is_thanked'] == true)
      return false;

    $order_user = Model_User::getById($order['user_id']);

    $ordered_products = Model_OrderedProduct::getAllByOrderId($order['id']);

    $ordered_product_ids = Model_Operation_Array::extractParam($ordered_products, 'product_id');

    if(empty($ordered_product_ids))
      return false;

    $ordered_products = Model_Product::getAll($ordered_product_ids);

    foreach($ordered_products as $product) {
      if($product['thank_you_is_active'] == true) {
        Model_Operation_Email::getInstance()->sendEmail($product['thank_you_title'],
          Model_Hook_Information_Order::wrapMessageWithInformation($product['thank_you_content'], $order),
          $order_user['email_address']);
      }
    }

    Model_Order::updateRecord(array('is_thanked' => 1), $order['id']);

    return true;
  }

  private static function _productSpecificEmailTriggerReferAFriend($order_id) {
    $order = is_array($order_id) ? $order_id : Model_Order::getById($order_id);

    self::_productSpecificEmailTriggerReferAFriendCurrentOrder($order);

    if($order['refer_a_friend_referral_order_id'] != 0)
      self::_productSpecificEmailTriggerReferAFriendCurrentOrder($order['refer_a_friend_referral_order_id']);
  }

  private static function _productSpecificEmailTriggerReferAFriendCurrentOrder($order_id)  {
    $order = is_array($order_id) ? $order_id : Model_Order::getById($order_id);

    if($order['is_refer_a_friend_confirmed'] == true)
      return true;

    $referral_orders = Model_Order::getAllByReferAFriendOrderId($order['id']);
    $order_user = Model_User::getById($order['user_id']);

    foreach($referral_orders as $referral_order) {
      if($referral_order['payment_type_status_id'] == Model_Constant::PAYMENT_TYPES_STATUS_COMPLETED_ID) {
        $ordered_products = Model_OrderedProduct::getAllByOrderId($order['id']);

        $ordered_product_ids = Model_Operation_Array::extractParam($ordered_products, 'product_id');

        if(empty($ordered_product_ids))
          continue;

        $ordered_products = Model_Product::getAll($ordered_product_ids);

        foreach($ordered_products as $product) {
          if($product['refer_a_friend_is_active'] == true) {
            $reward_content_path = $product['refer_a_friend_reward_url'];

            $reward_content_path = str_replace('http://www.stepout.ro', ROOT_PATH , $reward_content_path);

            $reward_content = file_get_contents($reward_content_path);

            Model_Operation_Email::getInstance()->sendEmail($product['refer_a_friend_email_title'],
              Model_Hook_Information_Order::wrapMessageWithInformation($product['refer_a_friend_email'], $order),
              $order_user['email_address'],
              $reward_content);
          }
        }
      }
    }

    Model_Order::updateRecord(array('is_refer_a_friend_confirmed' => 1), $order['id']);

    return true;
  }

}
