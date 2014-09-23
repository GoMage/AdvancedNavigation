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
 * @since        Class available since Release 2.0
 */
class GoMage_Navigation_Block_Layer_Searchview extends GoMage_Navigation_Block_Layer_View
{

    /**
     * Internal constructor
     */
    protected function _construct()
    {
        parent::_construct();
        Mage::register('current_layer', $this->getLayer(), true);
    }

    /**
     * Get layer object
     *
     * @return Mage_Catalog_Model_Layer
     */
    public function getLayer()
    {
        if (Mage::helper('gomage_navigation')->isEnterprise()) {
            return Mage::getSingleton('enterprise_search/search_layer');
        } else {
            return Mage::getSingleton('catalogsearch/layer');
        }
    }

    /**
     * Check availability display layer block
     *
     * @return bool
     */
    public function canShowBlock()
    {
        $availableResCount = (int)Mage::app()->getStore()
            ->getConfig(Mage_CatalogSearch_Model_Layer::XML_PATH_DISPLAY_LAYER_COUNT);

        if (!$availableResCount
            || ($availableResCount >= $this->getLayer()->getProductCollection()->getSize())
        ) {
            return parent::canShowBlock();
        }
        return false;
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        if (Mage::helper('gomage_navigation')->isEnterprise()) {
            $isCatalog = true;
            if (Mage::helper('gomage_navigation')->getRequest()->getParam('q') != null) {
                $isCatalog = false;
            }

            $helper = Mage::helper('enterprise_search');
            if ($helper->isThirdPartSearchEngine() && $helper->getIsEngineAvailableForNavigation($isCatalog) && Mage::helper('gomage_navigation')->isGomageNavigation()) {

                $children = $this->getChild();
                foreach ($children as $child) {
                    $child->addFacetCondition();
                }
            }
        }
    }
}
