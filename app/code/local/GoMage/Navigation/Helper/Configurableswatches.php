<?php

/**
 * GoMage Advanced Navigation Extension
 *
 * @category     Extension
 * @copyright    Copyright (c) 2010-2016 GoMage (https://www.gomage.com)
 * @author       GoMage
 * @license      https://www.gomage.com/license-agreement/  Single domain license
 * @terms of use https://www.gomage.com/terms-of-use
 * @version      Release: 4.9.2
 * @since        Class available since Release 4.7
 */
class GoMage_Navigation_Helper_Configurableswatches extends Mage_Core_Helper_Abstract
{

    /**
     * @param  int $attribute_id
     * @return bool
     */
    public function isSwatchAttribute($attribute_id)
    {
        if (!($swatch_id = Mage::getStoreConfig('configswatches/general/product_list_attribute'))) {
            return false;
        }

        return $attribute_id == $swatch_id;
    }

}