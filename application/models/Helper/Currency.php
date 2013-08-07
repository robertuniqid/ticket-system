<?php
/**
 * @author Rusu Andrei Robert
 */
class Model_Helper_Currency{

  protected static $_instance;

  /**
   * Retrieve singleton instance
   *
   * @return Model_Helper_Currency
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

  public $xml_document = "";
  public $currency_list = array('RON' =>  1);

  private $_source_url = 'http://www.bnr.ro/nbrfxrates.xml';

  public function __construct(){
    $this->updateCurrentInformation();
  }

  public function updateCurrentInformation(){
    $this->updateXMLDocument();
    $this->updateCurrencyList();
  }

  /**
   * Update the Current XML Document
   * @return void
   */
  public function updateXMLDocument() {
    $this->xml_document = file_get_contents($this->_source_url);
  }

  /**
   * @return void
   */
  public function updateCurrencyList() {
    $xml = new SimpleXMLElement($this->xml_document);

    foreach($xml->Body->Cube->Rate as $line)
    {
      $value =  ((float)$line /
                  (
                    (is_null($line["multiplier"])
                      || $line["multiplier"] == 0
                    )
                  ?
                    1 : $line["multiplier"]
                  )
                 );

      $this->currency_list[((string)$line["currency"])] = $value;
    }
  }

  /**
   * Return the currency value reported to RON
   * @param $currency
   * @return mixed
   * @throws Exception
   */
  public function getCurrencyValue($currency)
  {
    if(isset($this->currency_list[$currency]))
      return $this->currency_list[$currency];

    throw new Exception('Currency Not Found');
  }

  /**
   * Convert any currency sum to another currency, and round up the result if you wish to
   * @param $currency_from
   * @param $currency_to
   * @param int $amount
   * @param bool $round_result
   * @return float|int
   */
  public function convertAmount($currency_from, $currency_to, $amount = 1, $round_result = false){
    $from_value = $this->getCurrencyValue($currency_from);

    $to_value = $this->getCurrencyValue($currency_to);

    $result = ($from_value / $to_value) * $amount;

    return $round_result ? ceil($result) : $result;
  }

}