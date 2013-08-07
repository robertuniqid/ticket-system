<?php

/**
 * Model_Interface_Layout
 *
 * Access Model_Interface_Layout - internal functions
 *
 * @author Robert
 */
class Model_Interface_Layout {
    const PARTIAL_FOLDER_PATH         = '/layouts/partials/';

    const VIEW_HELPER_FOLDER_PATH     = '/layouts/helpers/';
    const VIEW_HELPER_NAMESPACE       = 'Zend_View_Helpers';

    const ACTION_HELPER_PATH = '/controllers/helpers/';

    /**
     * @static
     * @return string
     */
    public static function getPartialPath(){
        return realpath(APPLICATION_PATH . self::PARTIAL_FOLDER_PATH).DIRECTORY_SEPARATOR;
    }

    /**
     * @static
     * @return string
     */
    public static function getViewHelperPath(){
        return realpath(APPLICATION_PATH . self::VIEW_HELPER_FOLDER_PATH).DIRECTORY_SEPARATOR;
    }

    /**
     * @static
     * @return string
     */
    public static function getActionHelpersPath(){
        return realpath(APPLICATION_PATH . self::ACTION_HELPER_PATH).DIRECTORY_SEPARATOR;
    }

}
