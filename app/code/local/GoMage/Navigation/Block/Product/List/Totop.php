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
 * @since        Class available since Release 3.0
 */

class GoMage_Navigation_Block_Product_List_Totop extends Mage_Core_Block_Template
{
	public function __construct()
    {
        parent::__construct();
        if ($this->showToTopButton()){
        	$this->setTemplate('gomage/navigation/catalog/product/list/back_to_top.phtml');
        }                               
    } 
	 
	public function showToTopButton(){
		return Mage::getStoreConfig('gomage_navigation/general/back_to_top');
	}
	                    
}
