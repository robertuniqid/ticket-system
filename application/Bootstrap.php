<?php
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

    protected function _initDB()
    {
        // Database
        $db = $this->getOption('db');
        if($db) {
            $dbAdapter = Zend_Db::factory($db['adapter'],$db['params']);
            Zend_Db_Table_Abstract::setDefaultAdapter($dbAdapter);
            Zend_Registry::set('dbAdapter', $dbAdapter);
        }

    }

    protected function _initView(){
        // Initialize view
        $view = new Zend_View();
        $view->doctype('XHTML1_TRANSITIONAL');
        $view->headMeta()->appendHttpEquiv('Content-Type', 'text/html; charset=utf-8');
        $view->config = $this->getOptions();
        Zend_Registry::set('config', $this->getOptions());

        // Add it to the ViewRenderer
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper(
            'ViewRenderer'
        );
        $viewRenderer->setView($view);
        
        // Return it, so that it can be stored by the bootstrap
        return $view;
    }

    protected function _initModules()
    {
        $frontController = Zend_Controller_Front::getInstance();
	    $frontController->addModuleDirectory(APPLICATION_PATH . '/modules');
	    
        // action helpers
        //Zend_Controller_Action_HelperBroker::addPath(APPLICATION_PATH . '/controllers/helpers');

		// autoloaders
        $autoloader = Zend_Loader_Autoloader::getInstance();
		
		
        $modules = $frontController->getControllerDirectory();
        $default = $frontController->getDefaultModule();
        
        foreach (array_keys($modules) as $module) {
            $autoloader->pushAutoloader(new Zend_Application_Module_Autoloader(array(
                'namespace' => ucwords($module),
                'basePath' => $frontController->getModuleDirectory($module),
            )));
        }
		
		$resourceLoader = new Zend_Loader_Autoloader_Resource(array(
			'basePath'  => APPLICATION_PATH,
			'namespace' => '',
		));
		
		$resourceLoader->addResourceType('form', 'forms/', 'Form')
					   ->addResourceType('model', 'models/', 'Model')
					   ->addResourceType('model_table', 'models/Table/', 'Model_Table')
             ->addResourceType('model_operation', 'models/Operation/', 'Model_Operation')
             ->addResourceType('model_hook', 'models/Hook/', 'Model_Hook')
             ->addResourceType('model_hook_information', 'models/Hook/Information/', 'Model_Hook_Information')
             ->addResourceType('model_helper', 'models/Helper/', 'Model_Helper')
             ->addResourceType('model_interface', 'models/Interface/', 'Model_Interface')
             ->addResourceType('model_payment_gateway', 'models/PaymentGateway/', 'Model_PaymentGateway')
             ->addResourceType('model_ajax', 'models/Ajax/', 'Model_Ajax');
         
        $frontController->getRouter()         
           ->addroute('logout',new Zend_Controller_Router_Route('logout',array('module'=>'default', 'controller'=>'index', 'action'=>'logout')))
        ;

    }

    protected function _initHelpersAndPartials()
    {
        //Partials
            $this->view->addScriptPath(Model_Interface_Layout::getPartialPath());
        //ViewHelpers
            $this->view->addHelperPath(Model_Interface_Layout::getViewHelperPath() , Model_Interface_Layout::VIEW_HELPER_NAMESPACE);
        //ActionHelpers
            Zend_Controller_Action_HelperBroker::addPath(Model_Interface_Layout::getActionHelpersPath());

        $this->view->session = Model_Helper_Session::get();
    }

}

