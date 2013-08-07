<?php

class Model_Hook_OrderCSV {

  protected static $_instance;

  /**
   * Retrieve singleton instance
   *
   * @return Model_Hook_OrderCSV
   */
  public static function getInstance()
  {
    if (null === self::$_instance) {
      self::$_instance = new self();
    }
    return self::$_instance;
  }

  /**
   * Reset the singleton instance
   *
   * @return void
   */
  public static function resetInstance()
  {
    self::$_instance = null;
  }

  private $_shipping_headline = array('Nume Destinatar', 'Id comanda', 'Strada', 'Numar', 'Bloc', 'Scara', 'Etaj', 'Apartament',
                            'Localitate', 'Judet', 'Sector', 'Cod Postal', 'Nr. telefon', 'Email',
                            'Starea comenzii', 'Total de plata', 'Data comenzii',
                            'Metoda de plata', 'Metoda Livrare', 'Produse comandate');

  public function __construct() {

  }

  public function createShippingOrders($name = null) {
    $order_list = Model_Order::getAllOrderShippingFiltered(array());

    return $this->_create($order_list, $name);
  }

  public function createByOrderIds($ids, $name = null) {
    $orders_information = Model_Order::getAllOrderShippingFiltered($ids);

    return $this->_create($orders_information, $name);
  }

  public function createAllForOneClick($name) {
    $orders_information = Model_Order::getAllOrderShippingFiltered(
      array(),
      array(),
      array(),
      array(0,
            Model_Constant::PAYMENT_TYPES_STATUS_PENDING_ID,
            Model_Constant::PAYMENT_TYPES_STATUS_PAYMENT_RECEIVED_ID),
      array(Model_Constant::SHIPPING_TYPES_STATUS_PENDING)
    );

    $order_shipping_type_list = Model_Operation_Array::mapByParam(Model_ShippingType::getAll(), 'id');
    $order_payment_type_list = Model_Operation_Array::mapByParam(Model_PaymentType::getAll(array()), 'id');
    $order_payment_type_status_list = Model_Operation_Array::mapByParam(Model_PaymentTypeOrderStatus::getAll(), 'id');


    foreach($orders_information as $key =>  $order_information) {
      if($order_shipping_type_list[$order_information['shipping_type_id']]['pay_on_delivery'] == true)
        continue;

      if($order_information['payment_type_status_id'] == Model_Constant::PAYMENT_TYPES_STATUS_PAYMENT_RECEIVED_ID)
        continue;

      unset($orders_information[$key]);
    }

    return $this->_create($orders_information, $name);
  }

  private function _create($orders_information, $name = null) {
    $information = array($this->_shipping_headline);

    $order_shipping_type_list = Model_Operation_Array::mapByParam(Model_ShippingType::getAll(), 'id');
    $order_payment_type_list = Model_Operation_Array::mapByParam(Model_PaymentType::getAll(array()), 'id');
    $order_payment_type_status_list = Model_Operation_Array::mapByParam(Model_PaymentTypeOrderStatus::getAll(), 'id');


    foreach($orders_information as $order) {
      $ordered_products = Model_OrderedProduct::getAllByOrderId($order['id']);

      $ordered_products_line = Model_Operation_Array::composeKeyToValue($ordered_products, 'id', array('quantity', ' x ', 'name'));

      $information[] = array(
        ucfirst($order['shipping_first_name']) . ' ' . ucfirst($order['shipping_last_name']),
        $order['id'],
        $order['shipping_street'],
        $order['shipping_street_number'],
        $order['shipping_flat_block'],
        $order['shipping_flat_scale'],
        $order['shipping_flat_floor'],
        $order['shipping_flat_apartment'],
        ucfirst($order['shipping_city']),
        ucfirst($order['shipping_region']),
        '-',
        $order['shipping_zip_code'],
        $order['shipping_phone_number'],
        $order['user_email_address'],
        $order['payment_type_status_id'] == 0 ? 'Pending' : $order_payment_type_status_list[$order['payment_type_status_id']]['name'],
        ($order_shipping_type_list[$order['shipping_type_id']]['pay_on_delivery'] == true
            ? 'Ramburs' : $order_payment_type_list[$order['payment_type_id']]['name']),
        $order_shipping_type_list[$order['shipping_type_id']]['name'],
        $order['total_price'],
        $order['creation_date'],
        implode(' & ', $ordered_products_line),
      );
    }

    return Model_Operation_AdministrationDocuments::getInstance()->createInformationCSV(
      ($name == null ? 'Shipping-' . date('Y-m-d_H:i:s', time()) : str_replace(' ', '-', $name)),
      $information
    );
  }

  public function createEmailAddressListByOrderIds($order_ids, $name = null) {
    $orders_information = Model_Order::getAllOrderServicingFiltered($order_ids);

    $email_map = array_keys(Model_Operation_Array::mapByParam($orders_information, 'user_email_address'));

    $information = array();

    foreach($email_map as $email)
      $information[] = array($email);

    return Model_Operation_AdministrationDocuments::getInstance()->createInformationCSV(
      ($name == null ? 'Email-List-' . date('Y-m-d_H:i:s', time()) : str_replace(' ', '-', $name)),
      $information
    );
  }

}