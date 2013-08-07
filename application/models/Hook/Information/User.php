<?php

/**
 * Model_Hook_Information_User
 *
 * Access Model_Hook_Information_User - internal functions
 *
 * @author Robert
 */
class Model_Hook_Information_User {

  public static function wrapMessageWithInformation($content, $user_information) {
    if(is_numeric($user_information))
      $user_information = Model_User::getById($user_information);

    if(strpos($content, '%%%name%%%') !== false)
      $content = str_replace('%%%name%%%', $user_information['first_name'].' '.$user_information['last_name'], $content);

    if(strpos($content, '%%%email_address%%%') !== false)
      $content = str_replace('%%%email_address%%%', $user_information['email_address'], $content);

    return $content;
  }

}
