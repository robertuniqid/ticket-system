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
      $information = $this->_getParam('info');

      $clientInformation = Model_Client::getByEmailAddress($information['email_address']);

      if(empty($clientInformation)) {
        $clientId = Model_Client::insertRecord(array(
          'first_name'     =>  $information['first_name'],
          'last_name'      =>  $information['last_name'],
          'email_address'  =>  $information['email_address'],
          'phone_number'   =>  $information['phone_number'],
        ));

        $clientInformation = Model_Client::getById($clientId);
      }

      $ticketStatus = Model_TicketStatus::getFirstInOrder();

      Model_Ticket::insertRecord(array(
        'client_id'         =>  $clientInformation['id'],
        'ticket_status_id'  =>  $ticketStatus['id'],
        'ticket_category_id'=>  $information['ticket_category_id'],
        'title'             =>  $information['title'],
        'content'           =>  $information['content']
      ));

      $this->_redirect('ticket/sent');
    }
  }

  public function sentAction() {

  }

}



