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
class GoMage_Navigation_Block_Layer_Filter_Category extends Mage_Catalog_Block_Layer_Filter_Category
{
    protected $_block_side = null;

    protected $_activeFilters = array();

    /**
     * Initialize filter template
     *
     */

    public function setBlockSide($block_side)
    {
        $this->_block_side = $block_side;
    }

    public function getBlockSide()
    {
        if ($this->_block_side) {
            return $this->_block_side;
        }

        return GoMage_Navigation_Model_Adminhtml_System_Config_Source_Filter_Attributelocation::LEFT_BLOCK;
    }

    public function getConfigTab()
    {
        switch ($this->getBlockSide()) {
            default:
                $tab = 'category';
                break;
            case(GoMage_Navigation_Model_Adminhtml_System_Config_Source_Filter_Attributelocation::LEFT_BLOCK):
                $tab = 'category';
                break;
            case(GoMage_Navigation_Model_Adminhtml_System_Config_Source_Filter_Attributelocation::RIGHT_BLOCK):
                $tab = 'rightcolumnsettings';
                break;
            case(GoMage_Navigation_Model_Adminhtml_System_Config_Source_Filter_Attributelocation::CONTENT):
                $tab = 'contentcolumnsettings';
                break;
        }

        return $tab;
    }

    public function setCustomTemplate()
    {
        $type = Mage::getStoreConfig('gomage_navigation/' . $this->getConfigTab() . '/filter_type');

        switch ($type) {
            default:
                $this->_template = ('gomage/navigation/layer/filter/category/default.phtml');
                break;
            case(GoMage_Navigation_Model_Layer::FILTER_TYPE_IMAGE):
                $this->_template = ('gomage/navigation/layer/filter/image.phtml');
                break;
            case(GoMage_Navigation_Model_Layer::FILTER_TYPE_DROPDOWN):
                $this->_template = ('gomage/navigation/layer/filter/dropdown.phtml');
                break;
        }
    }

    public function getFilter()
    {
        return $this->_filter;
    }

    public function __construct()
    {
        parent::__construct();

        if (Mage::helper('gomage_navigation')->isGomageNavigation()
            &&
            (Mage::getStoreConfigFlag('gomage_navigation/category/active')
                ||
                Mage::getStoreConfigFlag('gomage_navigation/rightcolumnsettings/active')
                ||
                Mage::getStoreConfigFlag('gomage_navigation/contentcolumnsettings/active')
            )
        ) {

            $type = Mage::getStoreConfig('gomage_navigation/category/filter_type');

            switch ($type) {
                default:
                    $this->_template = ('gomage/navigation/layer/filter/category/default.phtml');
                    break;
                case(GoMage_Navigation_Model_Layer::FILTER_TYPE_IMAGE):
                    $this->_template = ('gomage/navigation/layer/filter/image.phtml');
                    break;
                case(GoMage_Navigation_Model_Layer::FILTER_TYPE_DROPDOWN):
                    $this->_template = ('gomage/navigation/layer/filter/dropdown.phtml');
                    break;
            }

        }

    }

    public function getItems()
    {
        if (Mage::helper('gomage_navigation')->isGomageNavigation() &&
            Mage::getStoreConfigFlag('gomage_navigation/category/active')
        ) {
            if (!$this->ajaxEnabled()) {
                $items = parent::getItems();;
                foreach ($items as $key => $item) {
                    if ($category = Mage::getModel('catalog/category')->load($item->getValue())) {
                        $items[$key]->setUrl($category->getUrl());
                    }
                }
                return $items;
            }
        }

        return parent::getItems();
    }

    public function getPopupId()
    {
        return 'category';
    }

    public function ajaxEnabled()
    {
        if (Mage::app()->getFrontController()->getRequest()->getRouteName() == 'catalogsearch') {
            $is_ajax = true;
        } else {
            $is_ajax = Mage::registry('current_category') &&
                Mage::registry('current_category')->getisAnchor() &&
                (Mage::registry('current_category')->getDisplayMode() != Mage_Catalog_Model_Category::DM_PAGE);
        }

        $is_ajax = $is_ajax && Mage::getStoreConfigFlag('gomage_navigation/' . $this->getConfigTab() . '/ajax_enabled');

        return $is_ajax;
    }

    public function canShowMinimized($side)
    {
        $helper = Mage::helper('gomage_navigation');

        if ('true' === $helper->getRequest()->getParam('cat' . '-' . $side . '_is_open')) {
            return false;
        } elseif ('false' === $helper->getRequest()->getParam('cat' . '-' . $side . '_is_open')) {
            return true;
        }

        return (bool)Mage::getStoreConfigFlag('gomage_navigation/' . $this->getConfigTab() . '/show_minimized');
    }

    public function canShowPopup()
    {
        return (bool)Mage::getStoreConfigFlag('gomage_navigation/' . $this->getConfigTab() . '/show_help');
    }

    public function getPopupText()
    {
        return trim(Mage::getStoreConfig('gomage_navigation/' . $this->getConfigTab() . '/popup_text'));
    }

    public function getPopupWidth()
    {
        return (int)Mage::getStoreConfig('gomage_navigation/' . $this->getConfigTab() . '/popup_width');
    }

    public function getPopupHeight()
    {
        return (int)Mage::getStoreConfig('gomage_navigation/' . $this->getConfigTab() . '/popup_height');
    }

    public function canShowCheckbox()
    {
        if (Mage::helper('gomage_navigation')->isGomageNavigation()
            &&
            Mage::getStoreConfigFlag('gomage_navigation/' . $this->getConfigTab() . '/active')
        ) {
            return (bool)Mage::getStoreConfigFlag('gomage_navigation/' . $this->getConfigTab() . '/show_checkbox');
        }
    }

    public function canShowLabels()
    {
        return (bool)Mage::getStoreConfigFlag('gomage_navigation/' . $this->getConfigTab() . '/show_image_name');
    }

    public function getImageWidth()
    {
        return (int)Mage::getStoreConfig('gomage_navigation/' . $this->getConfigTab() . '/image_width');
    }

    public function getImageHeight()
    {
        return (int)Mage::getStoreConfig('gomage_navigation/' . $this->getConfigTab() . '/image_height');
    }

    public function getImageAlign()
    {
        switch (Mage::getStoreConfig('gomage_navigation/' . $this->getConfigTab() . '/image_align')) {
            default:
                $image_align = 'default';
                break;
            case (1):
                $image_align = 'horizontally';
                break;
            case (2):
                $image_align = '2-columns';
                break;
        }

        return $image_align;
    }

    public function canShowResetFirler()
    {
        return (bool)Mage::getStoreConfig('gomage_navigation/' . $this->getConfigTab() . '/filter_reset');
    }

    public function isActiveFilter($label)
    {
        return false;
    }

    public function getFilterType()
    {
        return Mage::getStoreConfig('gomage_navigation/' . $this->getConfigTab() . '/filter_type');
    }

    public function getInBlockHeight()
    {
        return Mage::getStoreConfig('gomage_navigation/' . $this->getConfigTab() . '/inblock_height');
    }

    public function getInblockType()
    {
        return Mage::getStoreConfig('gomage_navigation/' . $this->getConfigTab() . '/inblock_type');
    }

    public function getMaxInBlockHeight()
    {
        return Mage::getStoreConfig('gomage_navigation/' . $this->getConfigTab() . '/max_inblock_height');
    }

    public function addFacetCondition()
    {
        if (Mage::helper('gomage_navigation')->isEnterprise()) {
            $this->_filter->addFacetCondition();
        }
        return $this;
    }

}
