<?php

class Model_Operation_File
{

  public static $image_type_extensions = array('png', 'jpg', 'jpeg', 'gif');

  /**
   * @param $string
   * @param array $extensions
   * @return bool
   */
  public static function hasFileExtension($string, $extensions = array()){
    if(is_string($extensions))
        return self::getFileExtension($string) == $extensions;

    return in_array(self::getFileExtension($string), $extensions);
  }

  /**
   * @static
   * @param $filename
   * @param bool $comma
   * @return bool|string
   */
  public static function getFileExtension($filename, $comma = false) {
    if (strrpos($filename, '.')){
      $extension = substr($filename, strrpos($filename, '.'));

      return strtolower(($comma ? $extension : substr($extension, 1)));
    } else
      return false;

  }

}