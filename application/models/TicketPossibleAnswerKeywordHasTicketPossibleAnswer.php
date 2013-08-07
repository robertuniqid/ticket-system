<?php 

/**
 * Model_TicketPossibleAnswerKeywordHasTicketPossibleAnswer
 *
 * Access Model_TicketPossibleAnswerKeywordHasTicketPossibleAnswer - internal
 * functions
 *
 * @author Robert
 */
class Model_TicketPossibleAnswerKeywordHasTicketPossibleAnswer
{

    private static $_table = null;

    /**
     * Implement a singleton to interact with the database.
     *
     * @return Model_Table_TicketPossibleAnswerKeywordHasTicketPossibleAnswer
     */
    public static function getTableInstance()
    {
        if (!self::$_table) self::$_table = new Model_Table_TicketPossibleAnswerKeywordHasTicketPossibleAnswer();
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
     * Fetch the ticket_possible_answer_keyword_ids from the records with the given
     * ticket_possible_answer_id
     *
     * @param int $ticket_possible_answer_id
     */
    public static function fetchAllTicketPossibleAnswerKeywordIdsByTicketPossibleAnswerId($ticket_possible_answer_id)
    {
        return self::getTableInstance()->fetchAllTicketPossibleAnswerKeywordIdsByTicketPossibleAnswerId($ticket_possible_answer_id);
    }

    /**
     * Fetch the ticket_possible_answer_ids from the records with the given
     * ticket_possible_answer_keyword_id
     *
     * @param int $ticket_possible_answer_keyword_id
     */
    public static function fetchAllTicketPossibleAnswerIdsByTicketPossibleAnswerKeywordId($ticket_possible_answer_keyword_id)
    {
        return self::getTableInstance()->fetchAllTicketPossibleAnswerIdsByTicketPossibleAnswerKeywordId($ticket_possible_answer_keyword_id);
    }

    /**
     * Delete the values from the records with the given
     * ticket_possible_answer_keyword_id
     *
     * @param int $ticket_possible_answer_keyword_id
     */
    public static function deleteByTicketPossibleAnswerKeywordId($ticket_possible_answer_keyword_id)
    {
        self::getTableInstance()->deleteByTicketPossibleAnswerKeywordId($ticket_possible_answer_keyword_id);
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
