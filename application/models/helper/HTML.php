<?php

/**
 * Model_Helper_HTML
 *
 * Access Model_Helper_HTML - internal functions
 *
 * @author Robert
 */
class Model_Helper_HTML {

  private static $_i = 1;

  /**
   * @static
   * @param array|string $error
   * @return string
   */
  public static function generateErrorHTML($error){
    $ret = '<ul class="errors">'."\n";

    if(is_array($error)) {
      foreach($error as $e)
        $ret .= "<li>".$e."</li> \n";

    } elseif(is_string($error)) {
      $ret .= "<li>".$error."</li> \n";
    }

    $ret .= "</ul> \n";

    return $ret;
  }

  /**
   * @static
   * @param array $options
   * @param string $select_name
   * @param string $selected_option
   * @param string $select_id
   * @return string
   */
  public static function generateSelectFromArray($options , $select_name , $selected_option = null, $select_id = null){
    if(is_null($select_id))
      $select_id = $select_name;

    $return = "";
    $return .= '<select id="'.$select_id.'" name="'.$select_name.'">';
    $return .=    self::generateSelectOptionsHTML($options, $selected_option);
    $return .= '</select>';

    return $return;
  }

  /**
   * @static
   * @param $options
   * @param $selected_option
   * @return string
   */
  public static function generateSelectOptionsHTML($options, $selected_option){
    $return = "";
    foreach($options as $value=>$name){
      $return .= '<option value="'.$value.'"';

      if($value == $selected_option
          && $selected_option != null
          || (is_array($selected_option) && in_array($value, $selected_option)))
        $return .= 'selected="selected"';

      $return .= '>'.$name.'</option>';
    }
    return $return;
  }

  public static function generateRadioOptions($checkbox_name, array $options, $checked_option = "", $class = ""){
    $ret = "";

    foreach($options as $option_value => $option_label) {
      $ret .= '<section class="row">';

      $ret .= '<input id="row_' . self::$_i . '" class="'.$class.'" type="radio"
                                    name="'.$checkbox_name.'"
                                    value="'.$option_value.'" ';
      $ret .= ($option_label == $checked_option
          || $option_value == $checked_option)
          ? ' checked="checked" ' : '';

      $ret .= (!in_array($checked_option, array_keys($options))
          && $option_value == Model_Constant::FREE_FIELD_IDENTIFIER
          && $checked_option != NULL
          && $checked_option != '') ? ' checked="checked" real_value="'.$checked_option.'" ' : '';

      $ret .= '/>';
      $ret .= '<label for="row_' . self::$_i . '">'.$option_label.'</label>';

      $ret .= '</section>';

      self::$_i++;
    }


    return $ret;
  }

  public static function showInformationLines($information){
    $ret = "";

    $ret .= '<div class="simple_information">';

    foreach($information as $key => $value)
      $ret .= '<div class="key">'.$key.'</div>'
          . '<div class="value">'.$value.'</div>';

    $ret .= '</div>';

    return $ret;
  }

  /**
   * @static
   * @param string $checkbox_name
   * @param $options
   * @param array $checked_options
   * @return string
   */
  public static function generateEasyOptionCheckBoxList($checkbox_name, array $options, array $checked_options = array()){
    $ret = "";

    $ret .= "<table class='tablesorter'>";
    $ret .= "<thead>";
    $ret .= "<tr>";
    $ret .= "<th>&nbsp;</th>";
    $ret .= "<th>&nbsp;</th>";
    $ret .= "</tr>";
    $ret .= "</thead>";

    $ret .= "<tbody>";
    foreach($options as $option_value => $option_label) {
      $ret .= "<tr>";
      $ret .= "<td>".$option_label."</td>";
      $ret .= "<td>";
      $ret .= '<input type="checkbox"
                                  name="'.$checkbox_name.'['.$option_value.']"
                                  value="'.$option_value.'"';
      $ret .= in_array($option_value, $checked_options) ? ' checked="checked" ' : '';
      $ret .= '/>';
      $ret .= "</td>";
      $ret .= "</tr>";
    }
    $ret .= "</tbody>";
    $ret .= "</table>";


    return $ret;
  }

  /**
   * @static
   * @param $menu_items
   * @param $active_url $active
   * @return string
   */
  public static function generateMenu($menu_items, $active_url = null){
    $ret = '<ul>';

    foreach($menu_items as $menu_key => $menu_group) {
      $has_children = (isset($menu_group['children']) && !empty($menu_group['children']));

      $ret .= '<li>';
      $ret .=   '<a href="'.$menu_group['href'].'">'.$menu_key.'</a>';
      $ret .=   ($has_children ? self::generateMenu($menu_group['children']) : '');
      $ret .= '</li>';
    }

    $ret .= '</ul>';

    return $ret;
  }

  /**
   * @static
   * @param string $option_name
   * @param array $option_list_tree
   * @param array $selected_options_key
   * @return string
   */
  public static function generateTreeLikeOptionList($option_name = "", array $option_list_tree, array $selected_options_key = array()){
    if(!is_array($option_list_tree))
      return '';

    $html = '<ul>';

    foreach($option_list_tree as $key => $child) {

      if(is_string($child)) {
        $active = (in_array($key, $selected_options_key));

        $html .= '<li>';

        $html .= '<span>'.$child.'</span>';
        $html .= '<input type="checkbox"
                           name="'.$option_name.'['.$key.']"
                           value="'.$key.'"
                           '.($active ? 'checked="checked"' : '').'
                           />';

        $html .= '</li>';

      } elseif(is_array($child)) {
        $html .= '<li><span class="title">'.$key.'</span>'.self::generateTreeLikeOptionList($option_name, $child, $selected_options_key).'</li>';
      }

    }

    $html .= '</ul>';

    return $html;
  }

  /**
   * @param $user_guide_title
   * @param $information_array
   * @return string
   */
  public static function generateUserGuide($user_guide_title, $information_array){
    $ret = '';

    $ret .= '<div class="user_guide">';
    $ret .=   '<h2>'.$user_guide_title.'</h2>';
    $ret .=   '<div id="'.str_replace(' ', '-', $user_guide_title).'">';
    $ret .=     '<ul>';

    $i = 1;foreach($information_array as $information_title => $information) {
      $ret .=     '<li>';
      $ret .=       '<a href="#tabs-'.$i.'">'.$information_title.'</a>';
      $ret .=     '</li>';

      $i++;
    }

    $ret .=     '</ul>';

    $i = 1;foreach($information_array as $information_title => $information) {
      $ret .=   '<div id="tabs-'.$i.'">';
      $ret .=     $information;
      $ret .=   '</div>';

      $i++;
    }

    $ret .=   '</div>';

    $ret .= '</div>';

    $ret .= '<script>$(function() { $( "#'.str_replace(' ', '-', $user_guide_title).'" ).tabs(); }); </script>';

    return $ret;

  }

}
