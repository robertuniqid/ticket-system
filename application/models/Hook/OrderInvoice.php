<?php

class Model_Hook_OrderInvoice {

  protected static $_instance;

  /**
   * Retrieve singleton instance
   *
   * @return Model_Hook_OrderInvoice
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

  public function __construct() {
    require_once(ROOT_PATH . DIRECTORY_SEPARATOR . 'library/PHPExcel/PHPExcel.php');
  }

  public function generateInvoicePDFByOrderId($order_id) {
    $invoice_html = $this->generateInvoiceHTMLByOrderId($order_id);

    echo $invoice_html;

    exit;

    return false;
  }

  public function generateInvoiceHTMLByOrderId($order_id, $is_fiscal = false) {

    $order_information = Model_Order::getById($order_id);
    $ordered_products_information = Model_OrderedProduct::getAllByOrderId($order_id);
    $order_payment_type  = Model_PaymentType::getById($order_information['payment_type_id']);
    $order_shipping_type = Model_ShippingType::getById($order_information['shipping_type_id']);

    // Fiscal Special Checks if allowed
    if($is_fiscal) {
      // Set tot false, and then change to true if possible
      $is_fiscal = false;

      if($order_shipping_type['pay_on_delivery'] && $order_shipping_type['is_invoicable'])
        $is_fiscal = true;
      elseif($order_payment_type['is_invoicable'])
        $is_fiscal = true;

      if($is_fiscal == true) {
        if($order_information['invoice_number'] == 0)
          $order_information['invoice_number'] = $this->generateInvoiceNumber($order_information);

      }

      if($is_fiscal == false)
        return '------- Invalid, contact support tehnic@stepout.ro';
    }

    $user_information  = Model_User::getById($order_information['user_id']);
    $currency          = Model_Currency::getById($order_information['currency_id']);

    $payment_type_clean_name = '-';

    if($order_information['payment_type_id'] == 0)
      $payment_type_clean_name = 'Ramburs';
    if($order_information['payment_type_id'] == Model_Constant::PAYMENT_TYPE_MOBILPAY_ID)
      $payment_type_clean_name = 'Mobilpay - Netopia';
    elseif($order_information['payment_type_id'] == Model_Constant::PAYMENT_TYPE_BANK_DEPOSIT_ID)
      $payment_type_clean_name = 'Banca (Ordin de Plata)';


    return Model_Helper_View::getObject()->partial('shop/invoice/general.phtml', array(
      'order_information'             => $order_information,
      'order_payment_type'            => $order_payment_type,
      'order_shipping_type'           => $order_shipping_type,
      'order_user_information'        => $user_information,
      'ordered_products_information'  => $ordered_products_information,
      'order_currency_name'           => strtoupper($currency['name']),
      'invoice_is_fiscal'             => $is_fiscal,
      'invoice_number'                => $is_fiscal ? $order_information['invoice_number'] : $order_information['id'],
      'invoice_name'                  => $is_fiscal ? 'FACTURA FISCALA' : 'PROFORMA',
      'invoice_serial'                => $is_fiscal ? 'STPB-F' : 'STPB-P',
      'invoice_observation'           => $is_fiscal ? 'Factura achitata' . ($order_shipping_type['pay_on_delivery'] == 1 ? '' : ' in avans') . ' prin ' . $payment_type_clean_name
                                            : 'Factura se achita prin ' . $payment_type_clean_name . ' in termen de 5 zile.',
    ));
  }

  public function generateInvoiceNumber($order) {
    $order_information = is_array($order) ? $order : Model_Order::getById($order);

    if($order_information['invoice_number'] != 0)
      return $order_information['invoice_number'];

    $invoice_number = Model_Helper_Settings::getNextInvoiceNumber();

    Model_Order::updateRecord(array('invoice_number'  => $invoice_number), $order_information['id']);

    return $invoice_number;
  }

  public function sendFiscalInvoiceByEmail($order) {
    $order_information = is_array($order) ? $order : Model_Order::getById($order);
    $user_information  = Model_User::getById($order_information['user_id']);

    $html = Model_Hook_OrderInvoice::getInstance()->generateInvoiceHTMLByOrderId($order_information['id'], true);

    $html = str_replace('http://www.stepout.ro/assets/images/stepout_logo.png', ROOT_PATH . '/assets/images/stepout_logo.png', $html);

    Model_Operation_Email::getInstance()->sendEmail($this->_getFiscalInvoiceEmailTitle(),
                                                    $this->_getFiscalInvoiceEmailContent($order_information),
                                                    $user_information['email_address'],
                                                    array('invoice.pdf' => Model_Helper_Pdf::getString($html)));

    return true;
  }

  private function _getFiscalInvoiceEmailTitle() {
    return 'Factura Fiscala';
  }

  private function _getFiscalInvoiceEmailContent($order_information) {
    $template = '';

    $template .= '<p>Salut %%%name%%%,</p>';
    $template .= '<p>Prin emailul asta iti confirm faptul ca am inregistrat plata pentru comanda nr %%%order_id%%% facuta pe Stepout Shop.</p>';
    $template .= '<p>Atasat gasesti factura fiscala. Aceasta factura <strong>NU TREBUIE PLATITA</strong>, reprezinta documentul fiscal care confirma plata comenzii.</p>';
    $template .= '<p>Iti stau la dispozitie pentru orice alte detalii.</p>';
    $template .= '<p>Marina Oprea</p>';
    $template .= '<p>shop@stepout.ro</p>';
    $template .= '<p>Departament Financiar</p>';
    $template .= '<p>Stepout Business SRL</p>';

    return Model_Hook_Information_Order::wrapMessageWithInformation($template, $order_information);
  }

  public function getOverviewInformation($date_from = null, $date_to = null) {
    $orders = Model_Order::getAllInvoicedOrders($date_from, $date_to);
    $payment_type_list = Model_Operation_Array::mapByParam(Model_PaymentType::getAll(), 'id');
    $currency_list     = Model_Operation_Array::mapByParam(Model_Currency::getAll(), 'id');

    $information = array(
      'head'  => array(
        'Nr Crt.', 'Nr Factura', 'Data', 'Nume', 'Suma', 'Metoda de plata', 'Nr comanda'
      ),
      'body'  => array(),
    );

    $i = 1;
    foreach($orders as $order) {
      $information['body'][] = array(
        $i, $order['invoice_number'], $order['creation_date'],
        ($order['payment_company'] != '-' ? $order['payment_company'] : $order['payment_first_name'] . ' ' . $order['payment_last_name']),
        ($order['total_price'] . ' ' . $currency_list[$order['currency_id']]['name']),
        (isset($payment_type_list[$order['payment_type_id']]) ? $payment_type_list[$order['payment_type_id']]['name'] : 'Ramburs'),
        $order['id']
      );

      $i++;
    }

    return $information;
  }

  public function downloadOverviewInformation($type = 'excel', $date_from = null, $date_to = null) {
    $information = $this->getOverviewInformation($date_from, $date_to);

    $objPHPExcel = new PHPExcel();
    $objPHPExcel->getProperties()->setCreator("Stepout")
                                 ->setLastModifiedBy("Stepout")
                                 ->setTitle("Invoices")
                                 ->setSubject("Invoices")
                                 ->setDescription($date_from . " to " . $date_to . ' - ' . count($information['body']) . ' invoices')
                                 ->setKeywords("Invoices")
                                 ->setCategory("Invoices");
    $objPHPExcel->setActiveSheetIndex(0);

    $head_column = 1;
    foreach($information['head'] as $column_information) {
      $objPHPExcel->getActiveSheet()->setCellValue(Model_Constant::$_excel_map[$head_column] . '1', $column_information);
      $head_column++;
    }

    $body_row_position = 2;
    foreach($information['body'] as $row_information) {
      $column_position = 1;
      foreach($row_information as $column_information) {
        $objPHPExcel->getActiveSheet()->setCellValue(Model_Constant::$_excel_map[$column_position] . $body_row_position,
                                                     $column_information);

        $column_position++;
      }

      $body_row_position++;
    }

    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="invoices.xls"');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
    exit;
  }


}
