<?php 

/**
 * Model_Table_TicketAnswer
 *
 * Access Model_Table_TicketAnswer - internal functions
 *
 * @author Robert
 */
class Model_Table_TicketAnswer extends Zend_Db_Table_Abstract
{

    protected $_name = 'ticket_answer';

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
     * Get the value from the record with the given id
     *
     * @param int $id
     * @return array
     */
    public function getById($id)
    {
        $sql = $this->select()->where($this->_db->quoteInto("id = ?" , $id));
        
        $result = $this->fetchRow($sql);
        $ret = array();
        if(!empty($result)){
        $ret = $result->toArray();
        }
        
        return $ret;
    }

    /**
     * Get the value from the record with the given ticket_possible_answer_id
     *
     * @param int $ticket_possible_answer_id
     * @return array
     */
    public function getAllByTicketPossibleAnswerId($ticket_possible_answer_id)
    {
        $sql = $this->select()->where($this->_db->quoteInto("ticket_possible_answer_id = ?" , $ticket_possible_answer_id));
        
        $result = $this->fetchAll($sql);
        $ret = array();
        if(!empty($result)){
        $ret = $result->toArray();
        }
        
        return $ret;
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
     * Get the value from the record with the given user_id
     *
     * @param int $user_id
     * @return array
     */
    public function getAllByUserId($user_id)
    {
        $sql = $this->select()->where($this->_db->quoteInto("user_id = ?" , $user_id));
        
        $result = $this->fetchAll($sql);
        $ret = array();
        if(!empty($result)){
        $ret = $result->toArray();
        }
        
        return $ret;
    }

    /**
     * Delete the values from the records with the given user_id
     *
     * @param int $user_id
     */
    public function deleteByUserId($user_id)
    {
        $this->delete($this->_db->quoteInto("user_id = ?" , $user_id));
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
