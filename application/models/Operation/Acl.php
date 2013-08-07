<?php
/**
 * Model_Operation_Acl
 *
 * Access Model_Operation_Acl - internal functions
 *
 * @author Robert
 */
class Model_Operation_Acl {

  protected static $_instance;

  /**
   * Retrieve singleton instance
   *
   * @return Model_Operation_Acl
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
   * @var Zend_Acl
   */
  protected $_acl;
  protected $_resources;

  public function __construct(){
    $this->_acl = Model_Operation_FileStorage::getInstance()->fetchInformation(Model_Constant::FILE_STORAGE_ACL_OBJECT_NAME);

    $this->_resources = $this->_acl->getResources();
  }

  /**
   * @return bool
   */
  public function checkCurrentAccessPermissions(){
    return $this->hasAccess(Model_Operation_Auth::getInstance()->getUserRoleId(),
                            Model_Helper_Request::getCurrentModule(),
                            Model_Helper_Request::getCurrentController(),
                            Model_Helper_Request::getCurrentAction());
  }

  public function hasAccess($user_role_id, $module, $controller, $action){
    if($module == 'default')
      return true;

    if($user_role_id == Model_Constant::DEFAULT_ADMIN_ROLE_ID)
      return true;

    if(!in_array($this->_glueModuleAndController($module, $controller), $this->_resources))
      return false;

    return $this->_acl->isAllowed($user_role_id,
                                  $this->_glueModuleAndController($module, $controller),
                                  $action);
  }

  public function buildFreshMap(){
    $this->_acl = new Zend_Acl();

    $this->_populateACLWithUserRoleList();

    $this->_populateACLWithResources();

    $module_list = Model_Module::getAll();
    $controller_list_mapped = Model_Operation_Array::mapByParam(Model_Controller::getAll(), 'module_id', true, true);
    $action_list_mapped     = Model_Operation_Array::mapByParam(Model_Action::getAll(), 'controller_id', true, true);

    foreach(Model_UserRole::getAll() as $user_role) {
      $role_action_ids = Model_UserRoleHasActionAccess::fetchAllActionAccessIdsByUserRoleId($user_role['id']);
      foreach($module_list as $module)  {
        foreach($controller_list_mapped[$module['id']] as $controller) {
          $allow = 0;
          $this->_acl->allow($user_role['id'],
            $this->_glueModuleAndController($module['alias'], $controller['alias']));

          foreach($action_list_mapped[$controller['id']] as $action){

            if(in_array($action['id'], $role_action_ids)
                || $user_role['id'] == Model_Constant::DEFAULT_ADMIN_ROLE_ID) {
              $this->_acl->allow($user_role['id'],
                                 $this->_glueModuleAndController($module['alias'], $controller['alias']),
                                 $action['alias']);
              $allow++;
            } else {
              $this->_acl->deny($user_role['id'],
                                $this->_glueModuleAndController($module['alias'], $controller['alias']),
                                $action['alias']);
            }
          }

          if($allow)
            $this->_acl->deny($user_role['id'],
              $this->_glueModuleAndController($module['alias'], $controller['alias']));
        }
      }
    }

    Model_Operation_FileStorage::getInstance()->storeInformation(Model_Constant::FILE_STORAGE_ACL_OBJECT_NAME, $this->_acl);
  }

  private function _populateACLWithUserRoleList(){
    foreach(Model_UserRole::getAll() as $role){
      $this->_acl->addRole(new Zend_Acl_Role($role['id']));
    }
  }

  private function _getZendAclResourceList(){
    $module_controller_list = array();

    foreach(Model_Module::getAll() as $module){
      foreach(Model_Controller::getAllByModuleId($module['id']) as $controller)
        $module_controller_list[] = $this->_glueModuleAndController($module['alias'], $controller['alias']);
    }


    $action_list     = Model_Operation_Array::extractParam(Model_Action::getAll(), 'alias', true);

    return array_unique(array_merge($module_controller_list, $action_list));
  }

  private function _populateACLWithResources(){
    foreach($this->_getZendAclResourceList() as $resource)
      $this->_acl->addResource($resource);
  }

  private function _glueModuleAndController($module, $controller){
    return $module.'_'.$controller;
  }

}