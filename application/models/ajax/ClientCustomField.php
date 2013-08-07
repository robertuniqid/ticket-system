<?php

class Model_Ajax_ClientCustomField {

  public static function add() {
    $entry_name = Model_Helper_Request::getParam('name');
    $entry_type = Model_Helper_Request::getParam('type');
    $entry_is_required = Model_Helper_Request::getParam('is_required');

    $id = Model_CustomFields::getInstance()->insert(array('name'        =>  $entry_name,
                                                          'type'        =>  $entry_type,
                                                          'is_required' =>  $entry_is_required));

    $custom_field = Model_CustomFields::getInstance()->getById($id);

    return array(
      'status'     => 'ok',
      'entry_html' => Application_View::getInstance()->partial(
        'client_custom_field_table_line.phtml',
        array(
          'custom_field'  =>  $custom_field
        )
      )
    );
  }

  public static function edit() {
    $entry_id   = Model_Helper_Request::getParam('id');
    $entry_name = Model_Helper_Request::getParam('name');
    $entry_type = Model_Helper_Request::getParam('type');
    $entry_is_required = Model_Helper_Request::getParam('is_required');

    Model_CustomFields::getInstance()->updateRecord($entry_id,
                                                    array('name'        =>  $entry_name,
                                                          'type'        =>  $entry_type,
                                                          'is_required' =>  $entry_is_required));

    $custom_field = Model_CustomFields::getInstance()->getById($entry_id);

    return array(
      'status'     => 'ok',
      'entry_id'   => $entry_id,
      'entry_html' => Application_View::getInstance()->partial(
        'client_custom_field_table_line.phtml',
        array(
          'custom_field'  =>  $custom_field
        )
      )
    );
  }

  public static function delete() {
    Model_CustomFields::getInstance()->deleteRecord(Model_Helper_Request::getParam('id'));

    return array(
      'status'  =>  'ok',
      'entry_id'=>  Model_Helper_Request::getParam('id')
    );
  }

}