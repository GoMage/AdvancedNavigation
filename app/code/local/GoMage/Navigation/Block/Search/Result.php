<?php
 /**
 * GoMage Advanced Navigation Extension
 *
 * @category     Extension
 * @copyright    Copyright (c) 2010-2014 GoMage (http://www.gomage.com)
 * @author       GoMage
 * @license      http://www.gomage.com/license-agreement/  Single domain license
 * @terms of use http://www.gomage.com/terms-of-use
 * @version      Release: 4.6
 * @since        Class available since Release 1.0
 */
/*todo*/
class GoMage_Navigation_Block_Search_Result extends Mage_CatalogSearch_Block_Result
{
    public function setListCollection() {
        $this->getListBlock()
           ->setCollection($this->_getProductCollection());

       return $this;
    }
    
    protected function _getProductCollection()
    {
        if (is_null($this->_productCollection)) {
			$helper = Mage::helper('enterprise_search');
			
        	if ($helper->isThirdPartSearchEngine() && $helper->isActiveEngine()){
        		$this->_productCollection = Mage::getSingleton('gomage_navigation/enterprise_search_search_layer')->getProductCollection();	
        	} else {
        		$this->_productCollection = Mage::getSingleton('gomage_navigation/catalogsearch_layer')->getProductCollection();
        	}
        }

        return $this->_productCollection;
    }      
}
