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
 * @since        Class available since Release 3.2
 */
	
class GoMage_Navigation_Model_Adminhtml_System_Config_Source_Filter_Type_Stock{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray(){
    	
    	$helper = Mage::helper('gomage_navigation');
    	
        return array(
            array('value'=>GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_DEFAULT, 'label' => $helper->__('Default')),
        	array('value'=>GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_DROPDOWN, 'label' => $helper->__('Dropdown')),
        	array('value'=>GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_DEFAULT_INBLOCK, 'label' => $helper->__('In Block'))
        );
    	
    }
    
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionHash(){
    	
    	$helper = Mage::helper('gomage_navigation');
    	
        return array(
            GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_DEFAULT	=> $helper->__('Default'),
        	GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_DROPDOWN => $helper->__('Dropdown'),
        	GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_DEFAULT_INBLOCK => $helper->__('In Block')
        );
    }

}