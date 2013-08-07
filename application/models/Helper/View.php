<?php

class Model_Helper_View {

  /**
   * @return Zend_View
   */
  public static function getObject(){
    $view = Zend_Controller_Front::getInstance()
      ->getParam('bootstrap')
      ->getResource('view');

    return $view;
  }

  public static function getCurrentLayout(){
    return 'layout';
  }

}