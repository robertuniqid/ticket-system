<?php

 class IndexController extends Zend_Controller_Action {
    
    protected $_session;

    /**
     * IndexController::init()
     * Executed for each request, before the requested action.
     * @return void
     */
    public function init() {
        $this->_helper->layout->setLayout("layout");
    }

    public function indexAction(){
    
    }

}



