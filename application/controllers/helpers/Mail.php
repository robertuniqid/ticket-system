<?php

class Zend_Controller_Action_Helper_Mail extends Zend_Controller_Action_Helper_Abstract
{

    private $_validator = null;

    public function __construct(){
        $this->_validator = new Zend_Validate();
    }

    public function send($message , $subject , $to , $from, $from_name = null){
        $answer = null;
        if(is_array($to)){
            $response_list = array();
            array_unique($to);
            foreach($to as $t)
                $response_list[] = $this->mail($message , $subject , $t , $from);

            $answer = array_sum($response_list) == count($response_list);// Just positive responses
        }elseif(is_string($to)){
                $answer = $this->mail($message , $subject , $to , $from, $from_name);
        }
        return $answer;
    }

    private function mail($message , $subject , $to , $from, $from_name = null){
        if(!$this->_validator->is($to,'EmailAddress'))
            return 0;
        $mail = new Zend_Mail();
        $mail->setBodyHtml($message)
            ->setFrom($from , (is_null($from_name) ? $from : $from_name))
            ->addTo($to)
            ->setSubject($subject)
            ->send()
        ;
        return 1;
    }


}