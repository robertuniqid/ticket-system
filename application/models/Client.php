<?php 

/**
 * Model_Client
 *
 * Access Model_Client - internal functions
 *
 * @author Robert
 */
class Model_Client
{

    private static $_table = null;

    /**
     * Implement a singleton to interact with the database.
     *
     * @return Model_Table_Client
     */
    public static function getTableInstance()
    {
        if (!self::$_table) self::$_table = new Model_Table_Client();
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
     * Get the value from the record with the given email_address
     *
     * @param string $email_address
     * @return array
     */
    public static function getByEmailAddress($email_address)
    {
      return self::getTableInstance()->getByEmailAddress($email_address);
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
