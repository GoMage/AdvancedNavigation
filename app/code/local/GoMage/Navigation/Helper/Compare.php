<?php
/**
 * GoMage Advanced Navigation Extension
 *
 * @deprecated
 *
 * @category     Extension
 * @copyright    Copyright (c) 2010-2016 GoMage (https://www.gomage.com)
 * @author       GoMage
 * @license      https://www.gomage.com/license-agreement/  Single domain license
 * @terms of use https://www.gomage.com/terms-of-use
 * @version      Release: 4.9.3
 * @since        Class available since Release 1.0
 */

class GoMage_Navigation_Helper_Compare extends Mage_Catalog_Helper_Product_Compare 
{
	public function getEncodedUrl($url = null)
	{
		if (!$url) {
			$url = $this->getCurrentUrl();
		}
		
		$url = Mage::helper('gomage_navigation/url')->removeRequestParam($url, 'ajax');
		
		return $this->urlEncode($url);
	}
}