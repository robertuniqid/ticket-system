<?php
/**
 * Model_Operation_AdministrationDocuments
 *
 * Access Model_Operation_AdministrationDocuments - internal functions
 *
 * @author Robert
 */
class Model_Operation_AdministrationDocuments {

    protected static $_instance;

    /**
     * Retrieve singleton instance
     *
     * @return Model_Operation_AdministrationDocuments
     */
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Reset the singleton instance
     *
     * @return void
     */
    public static function resetInstance()
    {
        self::$_instance = null;
    }

    private $_storage_directory = '';
    private $_storage_directory_real_path = '';

    public function __construct(){
        $this->_storage_directory = Model_Constant::ADMINISTRATION_DOCUMENTS_PATH;
        $this->_storage_directory_real_path = ROOT_PATH . DIRECTORY_SEPARATOR . $this->_storage_directory . DIRECTORY_SEPARATOR;
    }

    public function getStorageListing() {
        $available_files = array();

        if ($handle = opendir($this->_storage_directory_real_path)) {
            while (false !== ($entry = readdir($handle))) {
                // Ignore Unnecessary files
                if (strpos($entry, '.') !== 0) {
                     $available_files[] = array(
                         'name'         =>  $entry,
                         'real_path'    =>  $this->_storage_directory_real_path . $entry,
                         'last_change'  =>  filemtime($this->_storage_directory_real_path . $entry),
                         'url_path'     =>  Model_Helper_Request::getBaseUrl() . $this->_storage_directory . '/' . $entry
                     );
                }
            }
            closedir($handle);
        }

        Model_Operation_Array::sort($available_files, 'last_change', 'DESC');

        return $available_files;
    }

    public function createInformationCSV($file_name, $information) {
        if(!Model_Operation_File::hasFileExtension($file_name, 'csv'))
            $file_name .= '.csv';


        $fp = fopen($this->_storage_directory_real_path . $file_name, 'w');

        foreach ($information as $fields) {
            fputcsv($fp, $fields);
        }

        fclose($fp);

        return Model_Helper_Request::getBaseUrl() . $this->_storage_directory .'/'. $file_name;
    }

}