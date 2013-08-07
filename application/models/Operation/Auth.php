<?php

/**
 * Model_Operation_Auth
 *
 * Access Model_Operation_Auth - internal functions
 *
 * @author Robert
 */
class Model_Operation_Auth {

    protected $_auth;
    protected static $_instance;
    protected $_user_role_information = null;
   /**
    * Retrieve singleton instance
    *
    * @return Model_Operation_Auth
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

    protected $_salt = 'BackboneJS';
    protected $_login_time = 604800;
    protected $_persistent_cookie_name = 'auth';

    public function __construct(){
        $this->_auth = Zend_Auth::getInstance();

        if($this->isLogged() == false) {
          $this->_handlePersistentLogin();
        }
    }

    private function _setPersistentLogin($id, $username) {
      $identifier = $this->_generateIdentifier($username);
      $token = $this->_generateToken();
      $timeout = time() + $this->_login_time;

      setcookie($this->_persistent_cookie_name, $identifier . ':' . $token, $timeout, '/');

      Model_User::updateRecord(
        array(
          'login_identifier'  =>  $identifier,
          'login_token'       =>  $token,
          'login_timeout'     =>  $timeout
        ), $id);
    }

    private function _handlePersistentLogin() {
      if(isset($_COOKIE[$this->_persistent_cookie_name])
          && $_COOKIE[$this->_persistent_cookie_name] != '') {
        $information = explode(':', $_COOKIE[$this->_persistent_cookie_name]);

        $identifier = array_shift($information);
        $token      = array_shift($information);

        $user = Model_User::getByLoginIdentifierAndToken($identifier, $token);

        if(empty($user)) {
          setcookie($this->_persistent_cookie_name, '', time() + 5, '/');
          return false;
        }

        if($user['login_timeout'] > time()) {
          $this->_auth->getStorage()->write((object)$user);
        } else {
          setcookie($this->_persistent_cookie_name, '', time() + 5, '/');
        }
      }
    }

    /**
     * @note Unset this somehow and prevent having blank fields
     * @param $user_id
     */
    private function _unsetPersistentLogin($user_id) {
      setcookie($this->_persistent_cookie_name, '', time() + 5, '/');

      Model_User::updateRecord(
        array(
          'login_identifier'  =>  substr(md5(time()), 0, 16),
          'login_token'       =>  substr(md5(time()), 0, 16),
          'login_timeout'     =>  time()
        ), $user_id);
    }

    /**
     * Generate a sha1 identifier that looks like md5
     * @param $username
     * @return string
     */
    private function _generateIdentifier($username) {
      $identifier = sha1($username . $this->_salt);
      $identifier = substr($identifier, 0, 16) . substr($identifier, 16, 16);
      $identifier = sha1($identifier . $this->_salt);
      $identifier = substr($identifier, 0, 16) . substr($identifier, 16, 16);

      return $identifier;
    }

    private function _generateToken() {
      return md5(uniqid(rand(), TRUE));
    }

    public function isLogged()
    {
        if ($this->_auth->hasIdentity()){
            return 1;
        }
        return 0;
    }

    public function logout(){
      $this->_unsetPersistentLogin($this->getUserId());

      Zend_Auth::getInstance()->clearIdentity();
    }

    public function getUserId(){
        if($this->_auth->hasIdentity()){
            $identity = $this->_auth->getIdentity();
            // Object to array
                $identity = get_object_vars($identity);
            return $identity['id'];
        } else {
            return 0;
        }
    }

    public function getUserRoleId(){
        if($this->_auth->hasIdentity()){
            $identity = $this->_auth->getIdentity();
            // Object to array
                $identity = get_object_vars($identity);
            return $identity['user_role_id'];
        } else {
            return Model_Constant::DEFAULT_ROLE_ID;
        }
    }

    public function getLoggedUser(){
        if($this->_auth->hasIdentity()){
            $identity = $this->_auth->getIdentity();
            // Object to array
                $identity = get_object_vars($identity);
            return $identity;
        } else {
            return array();
        }
    }

    public function getCurrentUserRole(){
      if($this->_user_role_information == null)
        $this->_user_role_information = Model_UserRole::getById($this->getUserRoleId());

      return $this->_user_role_information;
    }

    public function processLogin($username, $password, $encryption_mode = 'sha1') {
        // Get our authentication adapter and check credentials
        $adapter = $this->_getAuthAdapter($encryption_mode);
        $adapter->setIdentity($username);
        $adapter->setCredential($password);

        $auth = Zend_Auth::getInstance();
        $result = $auth->authenticate($adapter);
        if ($result->isValid()) {
            $user = $adapter->getResultRowObject();

            $user = (object)Model_User::getById($user->id);

            $auth->getStorage()->write($user);

            $this->_setPersistentLogin($user->id, $user->username);

            return true;
        }
        return false;
    }

    protected function _getAuthAdapter($crypt = "sha1") {

        $dbAdapter = Zend_Db_Table::getDefaultAdapter();
        $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);

        $authAdapter->setTableName('user')
                    ->setIdentityColumn('username')
                    ->setCredentialColumn('password');

        if($crypt == "sha1")
          $authAdapter->setCredentialTreatment('sha1(?)');

        if($crypt == "md5")
          $authAdapter->setCredentialTreatment('md5(?)');

        return $authAdapter;
}
}
