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

class GoMage_Navigation_Model_Adminhtml_System_Config_Source_Mageversionold{

    public function toOptionArray(){

        $helper = Mage::helper('gomage_navigation');
        if ( $helper->getIsAnymoreVersion(1, 5, 2) )
        {
            return array(
                array('value'=>0, 'label' => $helper->__('Old')),
            );
        }
        else
        {
            return array(
                array('value'=>1, 'label' => $helper->__('New')),
            );
        }
    }
}