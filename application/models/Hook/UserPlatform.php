<?php

/**
 * Model_Hook_UserPlatform
 *
 * Access Model_Hook_UserPlatform - internal functions
 *
 * @author Robert
 */
class Model_Hook_UserPlatform {

  public static function addPlatformTime($user_id, $platform_id, $influence) {
    $current_information = Model_UserPlatform::getByUserIdAndPlatformId($user_id, $platform_id);

    if(empty($current_information)) {
      Model_UserPlatform::insertRecord(array(
        'user_id'     =>  $user_id,
        'platform_id' =>  $platform_id,
        'value'       =>  $influence
      ));
    } else {
      Model_UserPlatform::updateRecord(
        array(
          'value' =>  ($influence + $current_information['value'])
        ),
        $current_information['id']
      );
    }

    return true;
  }

  public static function setPlatformTime($user_id, $platform_id, $influence) {
    $current_information = Model_UserPlatform::getByUserIdAndPlatformId($user_id, $platform_id);

    if(empty($current_information)) {
      Model_UserPlatform::insertRecord(array(
        'user_id'     =>  $user_id,
        'platform_id' =>  $platform_id,
        'value'       =>  $influence
      ));
    } else {
      Model_UserPlatform::updateRecord(
        array(
          'value' =>  $influence
        ),
        $current_information['id']
      );
    }

    return true;
  }

}
