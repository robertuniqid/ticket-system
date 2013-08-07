<?php 

/**
 * Model_Table_TicketPossibleAnswerKeywordHasTicketPossibleAnswer
 *
 * Access Model_Table_TicketPossibleAnswerKeywordHasTicketPossibleAnswer - internal
 * functions
 *
 * @author Robert
 */
class Model_Table_TicketPossibleAnswerKeywordHasTicketPossibleAnswer extends Zend_Db_Table_Abstract
{

    protected $_name = 'ticket_possible_answer_keyword_has_ticket_possible_answer';

    /**
     * Get all
     *
     * @param array $ids
     * @return array
     */
    public function getAll($ids = array ())
    {
        $sql = $this->select();
        
        if(!empty($ids) && is_array($ids)){
        $sql->where("id IN (" . implode("," , $ids) . ")");
        }
        
        $result = $this->fetchAll($sql);
        
        $ret = array();
        if(!empty($result)){
        $ret = $result->toArray();
        }
        
        return $ret;
    }

    /**
     * Fetch the ticket_possible_answer_keyword_ids from the records with the given
     * ticket_possible_answer_id
     *
     * @param int $ticket_possible_answer_id
     */
    public function fetchAllTicketPossibleAnswerKeywordIdsByTicketPossibleAnswerId($ticket_possible_answer_id)
    {
        $sql = $this->select()->where($this->_db->quoteInto("ticket_possible_answer_id = ?" , $ticket_possible_answer_id));
        
        $result = $this->fetchAll($sql);
        
        $ret = array();
        
        if(!empty($result)){
           $result = $result->toArray();
           foreach($result as $r){
        	   $ret[] = $r["ticket_possible_answer_keyword_id"];
           }
        }
        return $ret;
    }

    /**
     * Fetch the ticket_possible_answer_ids from the records with the given
     * ticket_possible_answer_keyword_id
     *
     * @param int $ticket_possible_answer_keyword_id
     */
    public function fetchAllTicketPossibleAnswerIdsByTicketPossibleAnswerKeywordId($ticket_possible_answer_keyword_id)
    {
        $sql = $this->select()->where($this->_db->quoteInto("ticket_possible_answer_keyword_id = ?" , $ticket_possible_answer_keyword_id));
        
        $result = $this->fetchAll($sql);
        
        $ret = array();
        
        if(!empty($result)){
           $result = $result->toArray();
           foreach($result as $r){
        	   $ret[] = $r["ticket_possible_answer_id"];
           }
        }
        return $ret;
    }

    /**
     * Delete the values from the records with the given
     * ticket_possible_answer_keyword_id
     *
     * @param int $ticket_possible_answer_keyword_id
     */
    public function deleteByTicketPossibleAnswerKeywordId($ticket_possible_answer_keyword_id)
    {
        $this->delete($this->_db->quoteInto("ticket_possible_answer_keyword_id = ?" , $ticket_possible_answer_keyword_id));
    }

    /**
     * Delete the values from the records with the given ticket_possible_answer_id
     *
     * @param int $ticket_possible_answer_id
     */
    public function deleteByTicketPossibleAnswerId($ticket_possible_answer_id)
    {
        $this->delete($this->_db->quoteInto("ticket_possible_answer_id = ?" , $ticket_possible_answer_id));
    }

    /**
     * Insert the record in the database
     *
     * @param array $record
     * @return array
     */
    public function insertRecord($record)
    {
        $record = $this->_sanitizeRecord($record);
        $errors = $this->_validateRecord($record);
        
        if(!empty($errors))
        return $errors;
        
        $id = $this->insert($record);
        return $id;
    }

    /**
     * Update the record in the database
     *
     * @param array $record
     * @param int $record_id
     * @return array
     */
    public function updateRecord($record, $record_id)
    {
        $record = $this->_sanitizeRecord($record);
        $errors = $this->_validateRecord($record);
        
        if(!empty($errors))
        return $errors;
        
        if($record_id !== NULL){
        $this->update($record,$this->_db->quoteInto("id = ?" , $record_id));
        return $record_id;
        }
        return false;
    }

    /**
     * Sanitize
     *
     * @param array $record
     * @return array
     */
    private function _sanitizeRecord($record)
    {
        foreach($record as $k=>$r){
        $record[$k] = htmlentities($r);
        }
        return $record;
    }

    /**
     * Sanitize
     *
     * @param array $record
     * @return array
     */
    private function _validateRecord($record)
    {
        return array();
    }


}
