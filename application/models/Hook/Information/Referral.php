<?php

/**
 * Model_Hook_Information_Referral
 *
 * Access Model_Hook_Information_Referral - internal functions
 *
 * @author Robert
 */
class Model_Hook_Information_Referral {

  protected static $_instance;

  /**
   * Retrieve singleton instance
   *
   * @return Model_Hook_Information_Referral
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

  private $_referral_user_ids  = array();
  private $_referral_user_list = array();

  public function __construct() {
    $this->_referral_user_list = Model_Hook_Information_Referral::getReferrerUsersList();
    $this->_referral_user_ids  = Model_Operation_Array::extractParam($this->_referral_user_list, 'id', true);
  }

  public function getFullReferralInformation($limit = null, $offset = 0, $detailed_response = false){
    if(!Model_Operation_FileStorage::getInstance()->storeExists('full_referral_information'))
      $this->flushFullReferralInformation();

    $referrer_users = Model_Operation_FileStorage::getInstance()->fetchInformation('full_referral_information');

    if($detailed_response)
      return array(
        'user_list'       =>  is_null($limit) ? $referrer_users : array_slice($referrer_users, $offset, $limit),
        'total_entries'   =>  count($referrer_users),
        'current_page'    =>  ($offset == 0 ? 0 : $offset / $limit) + 1,
        'total_pages'     =>  ceil(count($referrer_users) / $limit)
      );

    return $referrer_users;
  }

  public function flushFullReferralInformation() {
    $information = array();

    $referrer_users = $this->getReferrerUsersList();


    $referred_registered_users_registered_list = Model_User::getAll();
    $referred_registered_users_list_mapped_by_referral_id = Model_Operation_Array::mapByParam($referred_registered_users_registered_list, 'referred_by_user_id', true, true);

    $referred_sales_list = Model_Order::getAll();
    $referred_sales_list_mapped_by_referral_id = Model_Operation_Array::mapByParam($referred_sales_list, 'referral_id', true, true);

    foreach($referrer_users as $referral_user) {
      $information[$referral_user['id']] = array(
        'id'                                  =>  $referral_user['id'],
        'name'                                =>  $referral_user['first_name'].' '.$referral_user['last_name'],
        'link_referred_users'                 =>  $this->fetchUserTotalReferred($referral_user['id']),
        'registered_referred_users'           =>  (isset($referred_registered_users_list_mapped_by_referral_id[$referral_user['id']]) ? count($referred_registered_users_list_mapped_by_referral_id[$referral_user['id']]) : 0),
        'sales_referred_users'                =>  (isset($referred_sales_list_mapped_by_referral_id[$referral_user['id']]) ? count($referred_sales_list_mapped_by_referral_id[$referral_user['id']]) : 0)
      );
    }

    Model_Operation_Array::sort($information, 'sales_referred_users', 'DESC');

    Model_Operation_FileStorage::getInstance()->storeInformation('full_referral_information', $information);
  }

  public function fetchUserTotalReferred($user_id){
    $tracking_information = Model_ReferralTrackingStorage::getAllByUserId($user_id);

    $ret = 0;

    foreach($tracking_information as $current_tracking_storage) {
      $current_tracking = null;

      $date = $current_tracking_storage['date'];

      if(empty($current_tracking_storage)) {
        $current_tracking = $this->flushDateInformation($user_id, $date, $current_tracking_storage);
      } elseif(is_null($current_tracking)) {
        $current_tracking = $current_tracking_storage['count'];
      }

      $ret += $current_tracking;
    }
    return $ret;
  }

  public function getReferrerUsersList($user_ids = array()) {
    $user_role_ids = Model_Operation_Array::extractParam(Model_UserRole::getAll(array(), 1), 'id');

    $referred_registered_users_list = Model_User::getAllByUserRoleIds($user_role_ids,
                                                                      Model_Constant::ITEM_VISIBLE,
                                                                      Model_Constant::ITEM_NOT_DELETED,
                                                                      'creation_date ASC',
                                                                      $user_ids);

    return $referred_registered_users_list;
  }

  public function getStatisticsInformation($from = null, $to = null, $user_ids = array()) {
    if($from == '')
      $from  =  time() - 604800;
    elseif(!is_numeric($from))
      $from = strtotime($from);
    elseif(is_null($from))
      $from  =  time() - 604800;

    if($to == '')
      $to = time();
    elseif(!is_numeric($to))
      $to = strtotime($to);
    elseif(is_null($to))
      $to = time();

    if($to > time())
      $to = time();

    if($from > $to)
      return '';

    $line_information = array();

    $referrer_users = Model_Hook_Information_Referral::getReferrerUsersList($user_ids);

    if(empty($referrer_users))
      return $line_information;

    for($i = $from; $i <= $to; $i += 86400) {

      $used_date_time = $i;
      $used_date = date('Y-m-d',$used_date_time);

      $current_referrer_row = array();

      foreach($referrer_users as $referrer_user) {
        $current_referrer_row[$referrer_user['id']] = $this->getDateAffiliateCount($referrer_user['id'], $used_date);
      }

      $line_information[] = date('F d, Y', $used_date_time)."\t".array_sum($current_referrer_row)."\t".implode("\t", $current_referrer_row);
    }

    return $line_information;
  }

  public function getDateAffiliateCount($user_id, $date) {
    $current_tracking_storage = Model_ReferralTrackingStorage::getByUserIdAndDate($user_id, $date);

    if(empty($current_tracking_storage)) {
      return $this->flushDateInformation($user_id, $date, $current_tracking_storage);
    } else {
      return $current_tracking_storage['count'];
    }
  }

  public function flushDateInformation($user_id, $date, $current_tracking_storage = null) {
    if(is_null($current_tracking_storage))
      $current_tracking_storage = Model_ReferralTrackingStorage::getByUserIdAndDate($user_id, $date);

    if(is_null($user_id))
      $user_id = $current_tracking_storage['user_id'];

    if(is_null($date))
      $date = $current_tracking_storage['date'];

    if(empty($current_tracking_storage)) {
      $current_count = count(Model_ReferralTracking::getOwnedReferredUsers($user_id, $date, $date));

      Model_ReferralTrackingStorage::insertRecord(array(
        'user_id'           =>  $user_id,
        'date'              =>  $date,
        'count'             =>  $current_count,
        'last_updated_time' =>  trim(time())
      ));
    } else {
      $current_count = count(Model_ReferralTracking::getOwnedReferredUsers($user_id, $date, $date));
      Model_ReferralTrackingStorage::updateRecord(array(
        'user_id'           =>  $user_id,
        'date'              =>  $date,
        'count'             =>  $current_count,
        'last_updated_time' =>  trim(time())
      ), $current_tracking_storage['id']);
    }


   return $current_count;
  }

  public function fetchPieChartInformation($used_date = null) {
    $referrer_users = Model_Hook_Information_Referral::getReferrerUsersList(array());

    if(is_null($used_date))
      $used_date = date('Y-m-d');

    $referrer_information = array();
    $total_count = 0;

    foreach($referrer_users as $referrer_user) {
      $count = $this->getDateAffiliateCount($referrer_user['id'], $used_date);

      if($count == 0)
        continue;

      $total_count += $count;

      $referrer_information[$referrer_user['id']] = array(
        'user_id'    => $referrer_user['id'],
        'first_name' => $referrer_user['first_name'],
        'last_name'  => $referrer_user['last_name'],
        'count'      => $count
      );
    }

    foreach($referrer_information as $key =>  $referrer_info)
      $referrer_information[$key]['percent'] = floatval(number_format($referrer_info['count'] / $total_count * 100 , 1));

    return array('referrer_information' =>  $referrer_information, 'total_referred' =>  $total_count);
  }


}
