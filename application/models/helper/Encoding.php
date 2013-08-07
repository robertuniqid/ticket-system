<?php

class Model_Helper_Encoding {

  /**
   * @param $token
   * @return string
   */
  public static function safeHexEncoding($token){
    return str_rot13(bin2hex(base64_encode($token)));
  }

  /**
   * @param $token
   * @return string
   */
  public static function safeHexDecoding($token){
    return base64_decode(pack("H*",str_rot13($token)));
  }

  /**
   * @param $array
   * @return string
   */
  public static function safeSerialize($array) {
    return base64_encode(serialize($array));
  }

  /**
   * @param $string
   * @return mixed
   */
  public static function safeUnSerialize($string) {
    return unserialize(base64_decode($string));
  }

}