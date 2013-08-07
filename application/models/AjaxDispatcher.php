<?php

class Model_AjaxDispatcher {
  const AJAX_METHOD_RETURN_GLUE_JSON			=	"JSON";
  const AJAX_METHOD_RETURN_GLUE_XML			  =	"XML";
  const AJAX_METHOD_RETURN_GLUE_HTML			=	"HTML";
  const AJAX_METHOD_NOT_FOUND_EXCEPTION		=	"Remote Method Not Found.";
  const AJAX_METHOD_INVALID_RETURN_TYPE		=	"The returned value from ajax node must be an array";
  const AJAX_INVALID_GLUE_VALUE_EXCEPTION		=	"Invalid Response Glue Value.";

  protected $_instancePrefix					=	'Model_Ajax_';
  protected $_glue							      =	self::AJAX_METHOD_RETURN_GLUE_XML;
  protected $_allowedResponses				= 	array(
    self::AJAX_METHOD_RETURN_GLUE_JSON,
    self::AJAX_METHOD_RETURN_GLUE_XML,
    self::AJAX_METHOD_RETURN_GLUE_HTML
  );

  protected $_globalMethods					=	array();
  private	$_node								=	null;
  private $_method							=	null;

  public function __construct()
  {

    foreach( get_class_methods(__CLASS__) as $method) {
      $this->_globalMethods[]	=	$method;
    }

    $split=	explode(".", $_REQUEST['am']);
    $this->_node	=	$this->_instancePrefix	.	ucfirst($split[0]);
    $this->_method	=	$split[1];

    if( isset( $_REQUEST['glue'] ) )
    {
      if(!in_array($_REQUEST['glue'], $this->_allowedResponses))
      {
        $this->_abort(self::AJAX_INVALID_GLUE_VALUE_EXCEPTION);
      }
      else
      {
        $this->_glue = $_REQUEST['glue'];
      }
    }
  }

  public function indexAction()
  {

    if(method_exists($this->_node, $this->_method)) {
      $output = call_user_func( $this->_node . '::' . $this->_method );
      if(!is_array($output)) {
        $this->_abort(self::AJAX_METHOD_INVALID_RETURN_TYPE);
      }
      else {
        echo $this->_out($output);
      }
    } else {
      $this->_abort(self::AJAX_METHOD_NOT_FOUND_EXCEPTION);
    }
    exit;
  }

  private function _abort($errMessage)
  {
    throw new Exception($errMessage);
  }

  private function _xml($array)
  {
    $xml 	= 	new SimpleXMLElement('<root/>');
    array_walk_recursive($array, array ($xml, 'addChild'));
    $out	=	$xml->asXML();

    return $out;
  }

  private function _out($parameter)
  {
    switch($this->_glue)
    {
      case self::AJAX_METHOD_RETURN_GLUE_JSON :
        header('Content-Type: application/json');

        if(isset($parameter['html']) || isset($parameter['content']))
          $parameter = array_map('utf8_encode', $parameter);

        $parameter 	=  json_encode($parameter);
        break;

      case self::AJAX_METHOD_RETURN_GLUE_XML :
        header('Content-type: text/xml');
        $parameter	=	$this->_xml($parameter);
        break;

      case self::AJAX_METHOD_RETURN_GLUE_HTML :

        $parameter = $parameter[0];

        break;

      default:
        header('Content-type: text/xml');
        $parameter	=	$this->_xml($parameter);
        break;
    }
    return $parameter;
  }
}