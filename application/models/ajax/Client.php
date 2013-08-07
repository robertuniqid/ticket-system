<?php

class Model_Ajax_Client {

 public static function getInformation() {
   $entry_id = Model_Helper_Request::getParam('entry_id');

   $information = Model_Client::getInstance()->getById($entry_id);

   return array(
     'status' => 'ok',
     'html'   => Application_View::getInstance()->partial('client_information.phtml', array('info'  =>  $information))
   );
 }

 public static function changeFlag() {
    $entry_id = Model_Helper_Request::getParam('entry_id');
    $new_flag_id = Model_Helper_Request::getParam('flag_id', 0);

    $information = Model_Client::getInstance()->getById($entry_id);
    $previous_flag_class = Model_Constant::getInstance()->client_flags_class[$information['flag']];
    $new_flag_class      = Model_Constant::getInstance()->client_flags_class[$new_flag_id];

    Model_Client::getInstance()->updateRecord($entry_id, array('flag'  =>  $new_flag_id));

    return array(
      'status'              => 'ok',
      'entry_id'            =>  $entry_id,
      'previous_flag_class' =>  $previous_flag_class,
      'new_flag_class'      =>  $new_flag_class
    );
 }

}