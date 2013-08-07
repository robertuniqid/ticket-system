<?php

class Model_Container {

  protected $values = array();

  public function getStorage() {
    return $this->values;
  }

  function __set($id, $value){
    $this->values[$id] = $value;
  }

  function __get($id){
    if(!isset($this->values[$id])){
      throw new InvalidArgumentException(sprintf('Value "%s" is not defined', $id));
    }

    return $this->values[$id];
  }

}
