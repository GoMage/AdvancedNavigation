<?php
 /**
 * GoMage Advanced Navigation Extension
 *
 * @category     Extension
 * @copyright    Copyright (c) 2010-2011 GoMage (http://www.gomage.com)
 * @author       GoMage
 * @license      http://www.gomage.com/license-agreement/  Single domain license
 * @terms of use http://www.gomage.com/terms-of-use
 * @version      Release: 2.2
 * @since        Class available since Release 1.0
 */

class GoMage_Navigation_Block_Product_List_Toolbar extends Mage_Catalog_Block_Product_List_Toolbar
{
    
	protected function _toHtml(){
    	if(Mage::helper('gomage_navigation')->isGomageNavigationAjax()){
            $this->setTemplate('gomage/navigation/catalog/product/list/toolbar.phtml');
        }        
        return parent::_toHtml();
    }
    
    public function getPagerUrl($params=array()){
    	
    	if(Mage::helper('gomage_navigation')->isGomageNavigationAjax()){    	    	    
    		$params['ajax'] = 1;    	
    	}else{
    		$params['ajax'] = null;
    	}
    	
    	$urlParams = array();
    	$urlParams['_nosid']    = true;
        $urlParams['_current']  = true;
        $urlParams['_escape']   = true;
        $urlParams['_use_rewrite']   = true;
        $urlParams['_query']    = $params;
        
        return Mage::helper('gomage_navigation')->getFilterUrl('*/*/*', $urlParams);        
    }
    
    
     public function getPagerHtml()
     {         
         
         $pagerBlock = $this->getChild('gomage_navigation_product_list_toolbar_pager');
         
         if (!$pagerBlock)
         {
             $pagerBlock = $this->getLayout()->createBlock('gomage_navigation/product_list_toolbar_pager', 'gomage_navigation_product_list_toolbar_pager');
             $this->insert($pagerBlock);
         }     
         
         if ($pagerBlock instanceof Varien_Object) 
         {

            $pagerBlock->setAvailableLimit($this->getAvailableLimit());

            $pagerBlock->setUseContainer(false)
                ->setShowPerPage(false)
                ->setShowAmounts(false)
                ->setLimitVarName($this->getLimitVarName())
                ->setPageVarName($this->getPageVarName())
                ->setLimit($this->getLimit())
                ->setFrameLength(Mage::getStoreConfig('design/pagination/pagination_frame'))
                ->setJump(Mage::getStoreConfig('design/pagination/pagination_frame_skip'))
                ->setCollection($this->getCollection());

             return $pagerBlock->toHtml();
         }

         return '';

     }
     
             
}