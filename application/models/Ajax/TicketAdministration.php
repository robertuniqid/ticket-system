<?php

class Model_Ajax_TicketAdministration {

  public static function getTickets() {
    $args = array(
      'order' =>  Model_Helper_Request::getParam('order', 't.id DESC')
    );

    $limit  = Model_Helper_Request::getParam('limit', Model_Constant::TICKET_ADMINISTRATION_DEFAULT_LIMIT);
    $page_number = Model_Helper_Request::getParam('page_number', Model_Constant::TICKET_ADMINISTRATION_DEFAULT_PAGE_NUMBER);

    $tickets = Model_Ticket::getAllWithClientInformation($args);

    $pager = new Zend_Paginator(new Zend_Paginator_Adapter_Array($tickets));
    $pager->setCurrentPageNumber($page_number);
    $pager->setItemCountPerPage($limit);

    return array(
      'status'            =>  'ok',
      'tickets'           =>  $pager->getCurrentItems(),
      'pagination_object' =>  $pager->getPages()
    );
  }

}