<?php
 /**
 * GoMage Advanced Navigation Extension
 *
 * @category     Extension
 * @copyright    Copyright (c) 2010-2011 GoMage (http://www.gomage.com)
 * @author       GoMage
 * @license      http://www.gomage.com/license-agreement/  Single domain license
 * @terms of use http://www.gomage.com/terms-of-use
 * @version      Release: 2.3
 * @since        Class available since Release 2.3
 */


class GoMage_Navigation_Block_Head extends Mage_Core_Block_Template
{    
    protected function _prepareLayout()
    { 
        parent::_prepareLayout();
        if(Mage::helper('gomage_navigation')->isGomageNavigation()){ 
            $this->getLayout()->getBlock('head')->addCss('css/gomage/advanced-navigation.css'); 
            $this->getLayout()->getBlock('head')->addjs('gomage/category-navigation.js');
        }       
    }
}