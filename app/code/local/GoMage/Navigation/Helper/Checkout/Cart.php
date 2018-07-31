<?php
 /**
 * GoMage Advanced Navigation Extension
 *
 * @category     Extension
 * @copyright    Copyright (c) 2010-2016 GoMage (https://www.gomage.com)
 * @author       GoMage
 * @license      https://www.gomage.com/license-agreement/  Single domain license
 * @terms of use https://www.gomage.com/terms-of-use
 * @version      Release: 4.9.2
 * @since        Class available since Release 3.1
 */
 
class GoMage_Navigation_Helper_Checkout_Cart extends Mage_Checkout_Helper_Cart
{
    /**
     * Retrieve current url
     *
     * @return string
     */
    public function getCurrentUrl()
    {          	  
        $url = parent::getCurrentUrl();
		
        if (Mage::helper('gomage_navigation')->isGomageNavigationAjax()){
        	$url = Mage::helper('gomage_navigation/url')->removeRequestParam($url, 'ajax');
        }
		
        return $url;
    }
}
