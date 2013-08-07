<?php

class Model_Operation_Email {

  protected static $_instance;

  /**
   * Retrieve singleton instance
   *
   * @return Model_Operation_Email
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

  private $_from = 'do-not-reply@stepout.ro';
  private $_from_name = 'Stepout';

  public function __construct(){
  }

  public function sendEmail($title, $content, $receiver, $pdf_attachment = null){
    $mail = new Zend_Mail();
    $mail->setBodyText(strip_tags($content))
      ->setBodyHtml($content)
      ->setFrom($this->_from , $this->_from_name)
      ->addTo($receiver)
      ->setSubject($title);

    if(is_string($pdf_attachment)) {
      $at = new Zend_Mime_Part($pdf_attachment);
      $at->type        = 'application/pdf';
      $at->disposition = Zend_Mime::DISPOSITION_INLINE;
      $at->encoding    = Zend_Mime::ENCODING_BASE64;
      $at->filename    = 'attachment.pdf';
      $mail->addAttachment($at);
    } elseif(is_array($pdf_attachment)) {
      foreach($pdf_attachment as $name => $information) {
        $at = new Zend_Mime_Part($information);
        $at->type        = 'application/pdf';
        $at->disposition = Zend_Mime::DISPOSITION_INLINE;
        $at->encoding    = Zend_Mime::ENCODING_BASE64;
        $at->filename    = $name . (Model_Operation_File::hasFileExtension($name, 'pdf') ? '' : '.pdf');
        $mail->addAttachment($at);
      }
    }

    $mail->send();

    return 1;
  }

  public function emailNotificationPaypalPaymentPending($order_id, $pending_reason) {

  }

  public function emailNotificationPaypalPaymentInvalidReceiver($order_id) {

  }

  public function emailNotificationSuccessRegister($name, $username, $password, $email_address) {
    $email_title   = Model_Interface_Strings::getInterfaceString('register_email_notification_title');

    $email_content = Model_Interface_Strings::getInterfaceString('register_email_notification_content');
    $email_content = str_replace('%%%name%%%', $name, $email_content);
    $email_content = str_replace('%%%username%%%', $username, $email_content);
    $email_content = str_replace('%%%password%%%', $password, $email_content);
    $email_content = str_replace('%%%login_url%%%', Model_Constant::getBaseUrl().'user/auth/login', $email_content);
    $email_content = str_replace('%%%recover_password_url%%%', Model_Constant::getBaseUrl().'user/profile/index', $email_content);

    return $this->sendEmail($email_title, $email_content, $email_address);
  }
}