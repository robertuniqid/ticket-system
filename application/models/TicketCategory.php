<?php 

/**
 * Model_TicketCategory
 *
 * Access Model_TicketCategory - internal functions
 *
 * @author Robert
 */
class Model_TicketCategory
{

    private static $_table = null;

    /**
     * Implement a singleton to interact with the database.
     *
     * @return Model_Table_TicketCategory
     */
    public static function getTableInstance()
    {
        if (!self::$_table) self::$_table = new Model_Table_TicketCategory();
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
