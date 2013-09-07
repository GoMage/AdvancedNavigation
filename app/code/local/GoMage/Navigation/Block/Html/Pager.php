<?php
 /**
 * GoMage Advanced Navigation Extension
 *
 * @category     Extension
 * @copyright    Copyright (c) 2010-2011 GoMage (http://www.gomage.com)
 * @author       GoMage
 * @license      http://www.gomage.com/license-agreement/  Single domain license
 * @terms of use http://www.gomage.com/terms-of-use
 * @version      Release: 2.0
 * @since        Class available since Release 1.0
 */

class GoMage_Navigation_Block_Html_Pager extends Mage_Page_Block_Html_Pager
{
    
    protected function _construct(){
    	return parent::_construct();
    	
    }
    
    public function getPagerUrl($params=array())
    {
    	
    	if(intval(Mage::getStoreConfigFlag('gomage_navigation/general/mode'))){
    	
    		$params['ajax'] = null;
    	
    	}
    	
    	
        $urlParams = array();
        $urlParams['_nosid']    = true;
        $urlParams['_current']  = true;
        $urlParams['_escape']   = true;
        $urlParams['_use_rewrite']   = true;
        $urlParams['_query']    = $params;
        return $this->getUrl('*/*/*', $urlParams);
    }
    
}
