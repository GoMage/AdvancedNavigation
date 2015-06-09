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

class GoMage_Navigation_Block_Navigation_CMS_Right extends GoMage_Navigation_Block_Navigation_Right {
	
	public function isActive() {	
		return (bool) Mage::getSingleton('cms/page')->getData('navigation_right_column');
	}
	
    protected function _prePrepareLayout() {
		if (
			$this->isGMN() && 
			$this->canDisplay() &&
			$this->isCMSPage()
		) {			
			$this->setTemplate('gomage/navigation/catalog/navigation/right.phtml')
				->unsetData('cache_lifetime')
				->unsetData('cache_tags');
		} else if ($content = $this->getLayout()->getBlock('content')) {
			$content->unsetChild('gomage.navigation.cms.right');
		}
	}
}