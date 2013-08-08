<?php

class Admin_TicketController extends Zend_Controller_Action {

  protected $_session;

  /**
   * Admin_TicketController::init()
   * Executed for each request, before the requested action.
   * @return void
   */
  public function init() {
    $this->_helper->layout->setLayout("layout");
  }

  public function indexAction() {
    Model_Hook_RequestHandler::getInstance()->queRequestHandler('ticket_administration.js', 'Application.TicketAdministration.Init();');
  }

}



