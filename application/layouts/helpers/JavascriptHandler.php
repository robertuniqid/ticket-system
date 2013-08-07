<?php

class Zend_View_Helpers_JavascriptHandler extends Zend_View_Helper_Abstract
{
  private $_current_map = array();

  private $_base_url = '';

  private $_folder_path = 'assets/scripts/request_handler';

  private $_file_map = array(
    'default'  =>  array(
      'view'    =>  array(
        'default' =>  array('view.js'  =>  'Stepout.View.Init();'),
        'specific'=>  array(
          'product'  =>  array('view/product.js'  =>  'Stepout.View.Product.Init();')
        )
      ),
      'index'    =>  array(
        'default' =>  array('view.js'  =>  'Stepout.View.Init();'),
        'specific'=>  array(
          'index'  =>  array('view/product_list.js'  =>  'Stepout.View.ProductList.Init();')
        )
      ),
    ),

    'user'  =>  array(
      'auth'    =>  array(
        'default' =>  array('user.js'  =>  'Stepout.User.Init();'),
        'specific'=>  array(
          'login'  =>  array('user/login.js'  =>  'Stepout.User.Login.Init();')
        )
      ),
    ),

    'shop'  =>  array(
      'orders'    =>  array(
        'default' =>  array('shop.js'  =>  'Stepout.Shop.Init();'),
        'specific'=>  array(
          'view'  =>  array()
        )
      ),

      'my-earnings'    =>  array(
        'default' =>  array('shop.js'  =>  'Stepout.Shop.Init();'),
        'specific'=>  array(
          'index'  =>  array('shop/my-earnings.js'  =>  'Stepout.Shop.MyEarnings.Init();')
        )
      ),

      'check-out' =>  array(
        'default' =>  array(),
        'specific'=>  array(
          'index'      =>  array('check-out.js'  =>  'Stepout.CheckOut.Init();'),
          'one-click'  =>  array('one-click-check-out.js'  =>  'Stepout.OneClickCheckOut.Init();')
        )
      ),

      'affiliate' =>  array(
        'default'  =>  array( 'shop.js'  =>  'Stepout.Shop.Init();'),
        'specific' =>  array(
          'index'  =>  array('shop/affiliate.js'  =>  'Stepout.Shop.Affiliate.Init();'),
        )
      ),

      'affiliate-statistics' =>  array(
        'default'  =>  array( 'shop.js'  =>  'Stepout.Shop.Init();'),
        'specific' =>  array(
          'index'  =>  array('shop/affiliate-statistics.js'  =>  'Stepout.Shop.AffiliateStatistics.Init();'),
        )
      ),

      'affiliate-orders' =>  array(
        'default'  =>  array( 'shop.js'  =>  'Stepout.Shop.Init();'),
        'specific' =>  array(
          'index'  =>  array('shop/affiliate-orders.js'  =>  'Stepout.Shop.AffiliateOrders.Init();'),
          'global'  =>  array('shop/affiliate-orders-global.js'  =>  'Stepout.Shop.AffiliateOrdersGlobal.Init();'),
        )
      ),

    ),
    'shop-administration' =>  array(
      // Start Product Section

      'product'  =>  array(
        'default'  =>  array( 'product.js'  =>  'Stepout.Product.Init();'),
        'specific' =>  array(
          'add'  =>  array('product/form.js'  =>  'Stepout.Product.Form.Init();'),
          'edit' =>  array('product/form.js'  =>  'Stepout.Product.Form.Init();')
        )
      ),

      // End Product Section

      // Start Payment Type Section

      'payment-type'  =>  array(
        'default'  =>  array( 'payment-type.js'  =>  'Stepout.PaymentType.Init();'),
        'specific' =>  array(
          'index' =>  array('payment-type/index.js' =>  'Stepout.PaymentType.Index.Init();'),
          'add'   =>  array('payment-type/form.js'  =>  'Stepout.PaymentType.Form.Init();'),
          'edit'  =>  array('payment-type/form.js'  =>  'Stepout.PaymentType.Form.Init();')
        )
      ),

      'payment-type-order-status'  =>  array(
        'default' =>  array( 'payment-type.js'               =>  'Stepout.PaymentType.Init();',
                             'payment-type/order-status.js'  =>  'Stepout.PaymentType.OrderStatus.Init();'),
        'specific' =>  array(
          'index' =>  array()
        )
      ),

      // End Payment Type Section

      // Start Payment Type Section

      'shipping-type'  =>  array(
        'default'  =>  array( 'shipping-type.js'  =>  'Stepout.ShippingType.Init();'),
        'specific' =>  array(
          'index' =>  array('shipping-type/index.js' =>  'Stepout.ShippingType.Index.Init();'),
          'add'   =>  array('shipping-type/form.js'  =>  'Stepout.ShippingType.Form.Init();'),
          'edit'  =>  array('shipping-type/form.js'  =>  'Stepout.ShippingType.Form.Init();')
        )
      ),

      'shipping-type-order-status'  =>  array(
        'default' =>  array( 'shipping-type.js'               =>  'Stepout.ShippingType.Init();',
                             'shipping-type/order-status.js'  =>  'Stepout.ShippingType.OrderStatus.Init();'),
        'specific' =>  array(
          'index' =>  array()
        )
      ),

      // End Payment Type Section
    ),
    'shop-servicing'  =>  array(
      'orders'    =>  array(
        'default' =>  array('shop-servicing.js'  =>  'Stepout.ShopServicing.Init();'),
        'specific'=>  array(
          'index'  =>  array('shop-servicing/orders.js'  =>  'Stepout.ShopServicing.Orders.Init()'),
          'edit'   =>  array('shop-servicing/order-edit.js'  =>  'Stepout.ShopServicing.OrderEdit.Init();'),
          'view'   =>  array('shop-servicing/order-view.js'  =>  'Stepout.ShopServicing.OrderView.Init();')
        )
      ),

      'earnings-distribution'    =>  array(
        'default' =>  array('shop-servicing.js'  =>  'Stepout.ShopServicing.Init();'),
        'specific'=>  array(
          'view-person'  =>  array('shop-servicing/earnings-distributions-view-person.js'  =>  'Stepout.ShopServicing.EarningsDistributionsViewPerson.Init();')
        )
      ),
    ),
    'shop-shipping'  =>  array(
      'orders'    =>  array(
        'default' =>  array('shop-shipping.js'  =>  'Stepout.ShopShipping.Init();'),
        'specific'=>  array(
          'index'  =>  array('shop-shipping/orders.js'  =>  'Stepout.ShopShipping.Orders.Init()'),
          'view'   =>  array('shop-shipping/order-view.js'  =>  'Stepout.ShopShipping.OrderView.Init();')
        )
      ),
    ),
    'user-administration'  =>  array(
      'index' =>  array(
        'default'   =>  array('user-administration.js'  =>  'Stepout.UserAdministration.Init();'),
        'specific'  =>  array(
          'index' =>  array('user-administration/index.js'  =>  'Stepout.UserAdministration.Index.Init();')
        )
      )
    )
  );

  /**
   * @return Zend_View_Helpers_JavascriptHandler
   */
  public function JavascriptHandler()
  {
    $this->_current_map = $this->getCurrentMap();
 
    $config = Zend_Registry::get('config');

    $this->_base_url = $config['url']['base'];

    return $this;
  }


  public function __toString()
  {
    $return = '<script type="text/javascript" src="'.$this->_base_url.$this->_folder_path.'/stepout.js"></script>'."\r\n";
    $return .= '<script type="text/javascript" src="'.$this->_base_url.$this->_folder_path.'/basket.js"></script>'."\r\n";
    $return .= $this->getRequiredJavascriptFiles();

    $return .= '        <script type="text/javascript">'."\r\n";
    $return .= '          $(document).ready(function(){'."\r\n";

      $return .= '            Stepout.Init();'."\r\n";
      $return .= '            Stepout.Basket.Init();'."\r\n";

      $return .= '            LayoutHelper.Init();'."\r\n";
      $return .= $this->getRequiredJavascriptActions();

      $return .= '          });'."\r\n";

    $return .= '        </script>'."\r\n";

    return $return;
  }

  public function getRequiredJavascriptFiles(){
    $ret = "";

    if(empty($this->_current_map))
      return $ret;

    foreach($this->_current_map as $file_name =>  $init_action) {
      $ret .= '        <script type="text/javascript" src="'.$this->_base_url.$this->_folder_path.'/'.$file_name.'"></script>'."\r\n";
    }

    return $ret;
  }

  public function getRequiredJavascriptActions(){
    $ret = "";

    if(empty($this->_current_map))
      return $ret;

    foreach($this->_current_map as $file_name =>  $init_action) {
      $ret  .=  '            '.$init_action."\r\n";
    }

    return $ret;
  }

  public function getCurrentMap(){
    $return = array();

    if(isset($this->_file_map[Model_Helper_Request::getCurrentModule()])){
      $map_module_level = $this->_file_map[Model_Helper_Request::getCurrentModule()];

      if(isset($map_module_level[Model_Helper_Request::getCurrentController()])){
        $map_controller_level = $map_module_level[Model_Helper_Request::getCurrentController()];

        $return += $map_controller_level['default'];

        if(isset($map_controller_level['specific'][Model_Helper_Request::getCurrentAction()])){
          $return += $map_controller_level['specific'][Model_Helper_Request::getCurrentAction()];
        }
      }
    }

    return $return;
  }


}
