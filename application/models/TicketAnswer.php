<?php 

/**
 * Model_TicketAnswer
 *
 * Access Model_TicketAnswer - internal functions
 *
 * @author Robert
 */
class Model_TicketAnswer
{

    private static $_table = null;

    /**
     * Implement a singleton to interact with the database.
     *
     * @return Model_Table_TicketAnswer
     */
    public static function getTableInstance()
    {
        if (!self::$_table) self::$_table = new Model_Table_TicketAnswer();
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
     * Get the value from the record with the given ticket_possible_answer_id
     *
     * @param int $ticket_possible_answer_id
     * @return array
     */
    public static function getAllByTicketPossibleAnswerId($ticket_possible_answer_id)
    {
        return self::getTableInstance()->getAllByTicketPossibleAnswerId($ticket_possible_answer_id);
    }

    /**
     * Delete the values from the records with the given ticket_possible_answer_id
     *
     * @param int $ticket_possible_answer_id
     */
    public static function deleteByTicketPossibleAnswerId($ticket_possible_answer_id)
    {
        self::getTableInstance()->deleteByTicketPossibleAnswerId($ticket_possible_answer_id);
    }

    /**
     * Get the value from the record with the given user_id
     *
     * @param int $user_id
     * @return array
     */
    public static function getAllByUserId($user_id)
    {
        return self::getTableInstance()->getAllByUserId($user_id);
    }

    /**
     * Delete the values from the records with the given user_id
     *
     * @param int $user_id
     */
    public static function deleteByUserId($user_id)
    {
        self::getTableInstance()->deleteByUserId($user_id);
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
