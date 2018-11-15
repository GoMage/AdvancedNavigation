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

class GoMage_Navigation_Model_Attribute extends Mage_Core_Model_Abstract
{    
    public function _construct()
    {
        parent::_construct();
        $this->_init('gomage_navigation/attribute');
    }
                      
}