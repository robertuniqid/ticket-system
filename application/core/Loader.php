<?php
/**
 * @author Andrei Robert Rusu
 * @throws Exception
 */
class Application_Loader{
  protected static $_instance;

  private $_loaded         = array();
  
  /**
   * Retrieve singleton instance
   *
   * @return Application_Loader
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

  /**
   * @param string|array $load_request
   * @return bool
   */
  public function load($load_request){
    if(is_array($load_request)){
      foreach($load_request as $request){
        $this->load($request);
      }
      return true;
    }elseif(is_string($load_request)){
      if(is_dir($load_request))
        return $this->_loadDirectory($load_request);
      else
        return $this->_loadFile($load_request);
    }

    throw new Exception(__CLASS__." ".__METHOD__." expects string or array");
  }

  /**
   * @param $directory_path
   * @return bool
   */
  private function _loadDirectory($directory_path){
    if(!is_dir($directory_path))
      return false;

    if($directory_path[count($directory_path) - 1] !== DIRECTORY_SEPARATOR)
      $directory_path .= DIRECTORY_SEPARATOR;

    if ($directory_handler = opendir($directory_path)) {
        while (false !== ($request_handler = readdir($directory_handler))) {
            if($request_handler !== '.' && $request_handler !== '..'){
              if(is_dir($directory_path.$request_handler))
                $this->_loadDirectory($directory_path.$request_handler . DIRECTORY_SEPARATOR);
              else
                $this->_loadFile($directory_path.$request_handler);
            }
        }
    }
    return true;
  }

  /**
   * @var string $file_path
   * @return bool
   */
  private function _loadFile($file_path){
    if(in_array($file_path, $this->_loaded)
        && !file_exists($file_path))
      return false;

    if($this->_getFileExtension($file_path, false) != 'php')
      return false;

    require_once($file_path);

    $this->_loadFile[] = $file_path;

    return true;
  }

  private function _getFileExtension($filename, $comma = false) {
    if (strrpos($filename, '.')){
      $extension = substr($filename, strrpos($filename, '.'));

      return strtolower(($comma ? $extension : substr($extension, 1)));
    } else
      return false;

  }

}
