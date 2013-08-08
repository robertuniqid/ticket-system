<?php 

/**
 * Model_Constant
 *
 * Access Model_Constant - internal functions
 *
 * @author Robert
 */
class Model_Constant
{
    const ITEM_UNDEFINED = -1;
    const ITEM_HIDDEN = 1;
    const ITEM_VISIBLE = 0;
    const ITEM_DELETED = 1;
    const ITEM_NOT_DELETED = 0;

    const SESSION_NAMESPACE = 'ticket_system';
    const SITE_TITLE        = 'Ticket System';

    const TICKET_ADMINISTRATION_DEFAULT_LIMIT  = 30;
    const TICKET_ADMINISTRATION_DEFAULT_PAGE_NUMBER = 1;
}
