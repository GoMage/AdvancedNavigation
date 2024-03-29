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
 
class GoMage_Navigation_Block_Navigation_Right extends GoMage_Navigation_Block_Navigation_Abstract {
	
	const NAVIGATION_PLACE	= self::RIGTH_COLUMN;//replace to admin html const
	const CONFIG_KEY		= 'gomage_navigation/rightcolumnsettings';
	
	public function canDisplay() {
		if ($this->can_display === null) {
			$shop_by = Mage::getStoreConfig('gomage_navigation/general/show_shopby');
			
			$this->can_display = (bool) (
				$this->isActive() &&
				(!$this->showInShopBy()) &&
				(
					$shop_by == GoMage_Navigation_Model_Adminhtml_System_Config_Source_Shopby::RIGHT_COLUMN_CONTENT	|| 
					$shop_by == GoMage_Navigation_Model_Adminhtml_System_Config_Source_Shopby::RIGHT_COLUMN
				)
			);
		}
		
        return $this->can_display;
    }
	
	protected function _prePrepareLayout() {
		if ($this->isGMN() && $this->canDisplay()) {		
			if (in_array(Mage::app()->getFrontController()->getRequest()->getControllerName(), array('category', 'result'))) {		
				$this->setTemplate('gomage/navigation/catalog/navigation/right.phtml')
					->unsetData('cache_lifetime')
					->unsetData('cache_tags');
			}     
        } else if ($content = $this->getLayout()->getBlock('content')) {
			$content->unsetChild('gomage.navigation.right');
		}
	}
}
