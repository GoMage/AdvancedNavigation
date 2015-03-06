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

class GoMage_Navigation_Helper_Config {
	
	protected $store_root_category_id	= null;
	protected $current_category			= null;
	
	public function storeRootCategoryId() {
		if ($this->store_root_category_id === null) {
			return $this->store_root_category_id = (int) Mage::app()->getStore()->getRootCategoryId();
		}
		
		return $this->store_root_category_id;
	}
	
	public function curentCategory() {
		if ($this->current_category === null) {
			if (Mage::registry('current_category')) {
				$this->current_category = Mage::registry('current_category');
			} else {
				if ($this->isStatic()) {		
					$category_id = (int) Mage::getSingleton('cms/page')->getData('navigation_category_id');
				} else {
					$category_id = $this->storeRootCategoryId();
				}
				
				$category_model			= Mage::getModel('catalog/category');
				$this->current_category	= $category_model->load($category_id);
			}
		}
		
		return $this->current_category;
	}
	
	public function isStatic() {
		return (bool) (Mage::getSingleton('cms/page')->getData('page_id'));
	}
	
	public function staticContenBlock() { 
		$static_conten_block = true;
		
		if ($this->isStatic()) {
			$static_conten_block = (bool) Mage::getSingleton('cms/page')->getData('navigation_content_column');
		}
		
		return $static_conten_block;
	}
}