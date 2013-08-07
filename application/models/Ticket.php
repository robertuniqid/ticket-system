<?php 

/**
 * Model_Ticket
 *
 * Access Model_Ticket - internal functions
 *
 * @author Robert
 */
class Model_Ticket
{

    private static $_table = null;

    /**
     * Implement a singleton to interact with the database.
     *
     * @return Model_Table_Ticket
     */
    public static function getTableInstance()
    {
        if (!self::$_table) self::$_table = new Model_Table_Ticket();
         return self::$_table;
    }

    /**
     * Get all
     *
     * @param array $ids
     * @return array
     */
    public static function getAll($ids = array ())
    {
        return self::getTableInstance()->getAll($ids);
    }

    /**
     * Get the value from the record with the given id
     *
     * @param int $id
     * @return array
     */
    public static function getById($id)
    {
        return self::getTableInstance()->getById($id);
    }

    /**
     * Get the value from the record with the given ticket_status_id
     *
     * @param int $ticket_status_id
     * @return array
     */
    public static function getAllByTicketStatusId($ticket_status_id)
    {
        return self::getTableInstance()->getAllByTicketStatusId($ticket_status_id);
    }

    /**
     * Delete the values from the records with the given ticket_status_id
     *
     * @param int $ticket_status_id
     */
    public static function deleteByTicketStatusId($ticket_status_id)
    {
        self::getTableInstance()->deleteByTicketStatusId($ticket_status_id);
    }

    /**
     * Get the value from the record with the given client_id
     *
     * @param int $client_id
     * @return array
     */
    public static function getAllByClientId($client_id)
    {
        return self::getTableInstance()->getAllByClientId($client_id);
    }

    /**
     * Delete the values from the records with the given client_id
     *
     * @param int $client_id
     */
    public static function deleteByClientId($client_id)
    {
        self::getTableInstance()->deleteByClientId($client_id);
    }

    /**
     * Get the value from the record with the given ticket_category_id
     *
     * @param int $ticket_category_id
     * @return array
     */
    public static function getAllByTicketCategoryId($ticket_category_id)
    {
        return self::getTableInstance()->getAllByTicketCategoryId($ticket_category_id);
    }

    /**
     * Delete the values from the records with the given ticket_category_id
     *
     * @param int $ticket_category_id
     */
    public static function deleteByTicketCategoryId($ticket_category_id)
    {
        self::getTableInstance()->deleteByTicketCategoryId($ticket_category_id);
    }

    /**
     * Insert the record in the database
     *
     * @param array $record
     * @return array
     */
    public static function insertRecord($record)
    {
        return self::getTableInstance()->insertRecord($record);
    }

    /**
     * Update the record in the database
     *
     * @param array $record
     * @param int $record_id
     * @return array
     */
    public static function updateRecord($record, $record_id)
    {
        return self::getTableInstance()->updateRecord($record, $record_id);
    }


}
