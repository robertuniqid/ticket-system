<?php

class Zend_Controller_Action_Helper_Upload extends Zend_Controller_Action_Helper_Abstract
{

    private $_slash_count = 3;

    public function __construct(){

    }

    private function _genRandomFileName($slash_count , $deep = 0) {
        $return = rand(1000 , (10000 * ($deep + 1) ) );

        for($i=1;$i<$slash_count;$i++)
            $return .= '_'.rand(10000 , (100000 * ($deep + 1) ) );

        return $return;
    }

    private function _genRandomUniqueFileName($path , $tag , $prefix = ""){
        $current_depth = 0;
        do{
            $file_name = $this->_genRandomFileName($this->_slash_count , $current_depth);
        }while(file_exists($path.$file_name.".".$tag));
        return $path.$prefix.$file_name.".".$tag;
    }

    private function getAllowedTagsForAsArray($tags){
        $tags = trim(str_replace(" " , '' , $tags));
        $tokens = explode(',' , $tags);

        return $tokens;
    }

    private function fileTagAllowed($file_name , $allowed){
        $tag = $this->_getTag($file_name);
        if(is_array($allowed)){
            $allowed_tokens = $allowed;
        }else{
            $allowed_tokens = $this->getAllowedTagsForAsArray($allowed);
        }
        if(in_array($tag , $allowed_tokens))
            return true;
        return false;
    }

    private function _getTag($file_name){
        $tokens = explode('.' , $file_name);
        return strtolower($tokens[count($tokens) - 1]);
    }

    public function handle($options = array()){
     $needed_params = array('upload_input_name' , 'destination' , 'allowed_tags');
     foreach($needed_params as $param){
         if(!array_key_exists($param , $options))
            return false;
     }

     if($options['destination'][strlen($options['destination']) - 1] != "/")
         $options['destination'] .= "/";

      $upload = new Zend_File_Transfer();
      $upload->setDestination($options['destination']);
      // Returns all known internal file information
      $files = $upload->getFileInfo();
      if(empty($files))
         return array();
      foreach ($files as $file => $info) {
          if (!$upload->isUploaded($file)) {
              return false;
          }

          if (!$this->fileTagAllowed($info['name'],$options['allowed_tags'])) {
              return false;
          }
      }

      $upload->receive();

      foreach($files as $file){
          if($this->fileTagAllowed($file['name'],$options['allowed_tags'])){
            $fullFilePath = $this->_genRandomUniqueFileName($options['destination'],$this->_getTag($file['name'], $options['file_prefix']));
            $filterFileRename = new Zend_Filter_File_Rename(array('target'    => $fullFilePath,
                                                                  'overwrite' => true)
                                                       );
            $filterFileRename->filter($options['destination'].$file['name']);
            $ret[] = $fullFilePath;
        }
      }

      return $ret;
    }
}