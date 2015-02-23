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
	public function isStatic() {
		return (bool) (Mage::getSingleton('cms/page')->getData('page_id'));
	}
	
	public function staticActiveBlocks() {
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