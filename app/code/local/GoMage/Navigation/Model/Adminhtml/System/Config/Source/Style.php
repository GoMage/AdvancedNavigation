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
	
class GoMage_Navigation_Model_Adminhtml_System_Config_Source_Style{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray(){
    	
    	$helper = Mage::helper('gomage_navigation');
    	
        return array(
            array('value'=>'default', 'label' => $helper->__('Default')),
        	array('value'=>'white', 'label' => $helper->__('White')),
        	array('value'=>'gray', 'label' => $helper->__('Gray')),
        	array('value'=>'blue', 'label' => $helper->__('Blue')),
        	array('value'=>'red', 'label' => $helper->__('Red')),
        	
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
            'default' => $helper->__('Default'),
            'white' => $helper->__('White'),
            'gray' => $helper->__('Gray'),
        	'blue' => $helper->__('Blue'),
        	'red' => $helper->__('Red'),
        	
        );
    }

}