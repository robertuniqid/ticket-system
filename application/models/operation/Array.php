<?php

/**
 * Model_Operation_Array
 *
 * Access Model_Operation_Array - internal functions
 *
 * @author Robert
 */
class Model_Operation_Array {

    public static function utfEncode($parameter) {
        foreach($parameter as $key  =>  $param)
          $parameter[$key]  = is_array($param) ? self::utfEncode($param) : utf8_encode($param);

        return $parameter;
    }

    /**
     * Quick solution for preparing select elements
     * @static
     * @param $from  -> bidimensional array
     * @param $key   -> index of the new array
     * @param $value -> value of the new array
     * @return array
     */
    public static function composeKeyToValue($from , $key , $value){
        $ret = array();

        foreach($from as $f){
            if(isset($f[$key])){
              if(is_string($value)) {
                $ret[$f[$key]] = $f[$value];
              } else {
                $ret[$f[$key]] = "";
                foreach($value as $v){
                  if(isset($f[$v]))
                    $ret[$f[$key]] .= $f[$v];
                  else
                    $ret[$f[$key]] .= $v;
                }
              }
            }
        }

        return $ret;
    }

    /**
     *  MapByParam an Array or an object
     *  Features : CheckArray . If the param does not exist in one of the arrays this will return FALSE
     *             Avoid Collisions . If this is True is the key is identical it will transform the `entries` into an array
     * @param $toMap - @type array or object
     * @param $param - @type string
     * @param bool $check_array - @type boolean ; DEFAULT : FALSE
     * @param bool $avoid_collisions - @type boolean ; DEFAULT : FALSE
     * @return array
     */
    public static function mapByParam($toMap , $param , $check_array = false , $avoid_collisions = false){
        $collision = array();
        $final = array();
        if(!empty($toMap)){
            foreach($toMap as $k=>$map){
                if(isset($map[$param])){
                    if($avoid_collisions == true){
                        $final[$map[$param]][] = $map;
                    } else {
                        $final[$map[$param]] = $map;
                    }
                } else {
                    // Return False because array is malformed,there is no $param in the $toMap[$key]
                    if($check_array == true)
                        return false;
                }
            }
        }
        return $final;
    }

    /**
     * @param $array
     * @param $extract_param
     * @param bool $unique
     * @return array
     */
    public static function extractParam($array, $extract_param, $unique = false){
      $extracted_params = array();

      foreach($array as $a)
        $extracted_params[] = $a[$extract_param];

      return ( $unique == true ? array_unique($extracted_params) : $extracted_params);
    }

    public static function cleanArrayByParam($array, $param, $allow_values) {

    }

    public static function sort(&$array, $column=0, $order="ASC") {
      $oper = ($order == "ASC")?">":"<";

      if(!is_array($array)) return;

      usort($array, create_function('$a,$b',"return (\$a['$column'] $oper \$b['$column']);"));

      reset($array);
    }

}
