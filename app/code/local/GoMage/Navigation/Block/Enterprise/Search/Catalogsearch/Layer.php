<?php
/**
 * GoMage Advanced Navigation Extension
 *
 * @category     Extension
 * @copyright    Copyright (c) 2010-2016 GoMage (https://www.gomage.com)
 * @author       GoMage
 * @license      https://www.gomage.com/license-agreement/  Single domain license
 * @terms of use https://www.gomage.com/terms-of-use
 * @version      Release: 4.9.3
 * @since        Class available since Release 1.0
 */
class GoMage_Navigation_Block_Enterprise_Search_Catalogsearch_Layer extends GoMage_Navigation_Block_Enterprise_Search_Catalog_Layer_View
{
	public function getLayer()
    {
        $helper = Mage::helper('enterprise_search');
        
		if ($helper->isThirdPartSearchEngine() && $helper->isActiveEngine()) {
            return Mage::getSingleton('gomage_navigation/enterprise_search_search_layer');
        }

        return Mage::getSingleton('gomage_navigation/catalogsearch_layer');
    }
}