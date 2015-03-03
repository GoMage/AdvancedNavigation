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
	
	protected $current_category = null;
	
	public function isStatic() {
		return (bool) (Mage::getSingleton('cms/page')->getData('page_id'));
	}
	
	public function curentCategory() {
		if ($this->current_category === null) {
			if (Mage::registry('current_category')) {
				$this->current_category = Mage::registry('current_category');
			} else {
				if ($this->isStatic()) {		
					$category_id = (int) Mage::getSingleton('cms/page')->getData('navigation_category_id');
				} else {
					$category_id = Mage::app()->getStore()->getRootCategoryId();
				}
				
				$category_model			= Mage::getModel('catalog/category');
				$this->current_category	= $category_model->load($category_id);
			}
		}
		
		return $this->current_category;
	}
		
	public function staticActiveBlocks() { /*In GM-AN v. 5.0 this expression must be removed*/
		$active_blocks = array(
			'content'	=> false,
			'left'		=> false,
			'right'		=> false,		
		);
		
		if ($this->isStatic()) {
			$active_blocks = array(
				'content'	=> (bool) Mage::getSingleton('cms/page')->getData('navigation_content_column'),
				'left'		=> (bool) Mage::getSingleton('cms/page')->getData('navigation_left_column'),
				'right'		=> (bool) Mage::getSingleton('cms/page')->getData('navigation_right_column'),		
			);
		}
		
		return $active_blocks;
	}
}