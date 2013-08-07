<?php

class TicketController extends Zend_Controller_Action {

  protected $_session;

  /**
   * IndexController::init()
   * Executed for each request, before the requested action.
   * @return void
   */
  public function init() {
    $this->_helper->layout->setLayout("layout");
  }

  public function indexAction() {
    $this->view->ticket_category_list_composed = Model_Operation_Array::composeKeyToValue(Model_TicketCategory::getAll(), 'id', 'name');

    if($this->_hasParam('submit') && $this->_hasParam('info')) {
      Zend_Debug::dump($this->_getParam('info'));

      exit;
    }
  }

}



