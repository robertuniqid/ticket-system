<?php 

/**
 * Model_TicketPossibleAnswer
 *
 * Access Model_TicketPossibleAnswer - internal functions
 *
 * @author Robert
 */
class Model_TicketPossibleAnswer
{

    private static $_table = null;

    /**
     * Implement a singleton to interact with the database.
     *
     * @return Model_Table_TicketPossibleAnswer
     */
    public static function getTableInstance()
    {
        if (!self::$_table) self::$_table = new Model_Table_TicketPossibleAnswer();
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
