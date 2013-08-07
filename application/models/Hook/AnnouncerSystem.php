<?php

/**
 * Model_Hook_AnnouncerSystem
 *
 * Access Model_Hook_AnnouncerSystem - internal functions
 *
 * @author Robert
 */
class Model_Hook_AnnouncerSystem {

  protected static $_instance;

  /**
   * Retrieve singleton instance
   *
   * @return Model_Hook_AnnouncerSystem
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

  /**
   * @var Nexmo
   */
  private $_sms_apy;

  public function __construct() {
    require_once(ROOT_PATH.DIRECTORY_SEPARATOR.'library/Nexmo/Nexmo.php');

    $this->_sms_apy = Nexmo::getInstance(Model_Constant::NEXMO_APY_KEY, Model_Constant::NEXMO_APY_PASSWORD);
  }

  public function _sentSMS($to, $from_name, $message) {
    $this->_sms_apy->sendMessage($to, $from_name, $message);
  }

  public function _sentEmail($title,
                              $content,
                              $from,
                              $from_name,
                              $receiver){

    $mail = new Zend_Mail();
    $mail->setBodyText(strip_tags($content))
      ->setBodyHtml($content)
      ->setFrom($from , $from_name)
      ->addTo($receiver)
      ->setSubject($title)
      ->send()
    ;
    return 1;
  }

}
