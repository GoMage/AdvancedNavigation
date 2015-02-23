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

class GoMage_Navigation_Helper_Url {
	public function wrapp($url, $options = array()) {
		foreach ($options as $index => $value) {
			switch ($value) {
				case '' :
					
				break;
			}
		}
		
		return $url;
	}
	
	public function categoryUrl(Varien_Object $category) {
        if ($category instanceof Mage_Catalog_Model_Category) {
            $urlPath	= $category->getUrlPath();
        } else {
			$category	= Mage::getModel('catalog/category')->load($category->getId());
            $urlPath	= $category->getUrlPath();
        }		
		
		$url = Mage::getUrl('', array('_direct' => $urlPath, '_secure' => true));
		$url = Mage::getModel('core/url')->sessionUrlVar($url);
		
        return $url;
    }
}