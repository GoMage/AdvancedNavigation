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
 * @since        Class available since Release 4.0
 */
class GoMage_Navigation_Model_Enterprise_Search_Search_Layer extends Enterprise_Search_Model_Search_Layer
{
    protected $attribute_options_images = null;

    /**
     * Add filters to attribute collection
     *
     * @param   Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Attribute_Collection $collection
     * @return  Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Attribute_Collection
     */
    protected function _prepareAttributeCollection($collection)
    {
        $collection->addIsFilterableInSearchFilter();

        $tableAlias = 'gomage_nav_attr';

        $collection->getSelect()->joinLeft(
            array($tableAlias => Mage::getSingleton('core/resource')->getTableName('gomage_navigation_attribute')),
            "`main_table`.`attribute_id` = `{$tableAlias}`.`attribute_id`",
            array('filter_type',
                'inblock_type',
                'round_to',
                'show_currency',
                'image_align',
                'image_width',
                'image_height',
                'show_minimized',
                'show_image_name',
                'visible_options',
                'show_help',
                'show_checkbox',
                'popup_width',
                'popup_height',
                'filter_reset',
                'is_ajax',
                'inblock_height',
                'max_inblock_height',
                'filter_button',
                'category_ids_filter',
                'range_options',
                'range_manual',
                'range_auto',
                'attribute_location')
        );

        $tableAliasStore = 'gomage_nav_attr_store';

        $collection->getSelect()->joinLeft(
            array($tableAliasStore => Mage::getSingleton('core/resource')->getTableName('gomage_navigation_attribute_store')),
            "`main_table`.`attribute_id` = `{$tableAliasStore}`.`attribute_id` and `{$tableAliasStore}`.store_id = " . Mage::app()->getStore()->getStoreId(),
            array('popup_text')
        );

        foreach ($collection as $attribute) {
            $attribute->setOptionImages($this->getAttributeOptionsImages($attribute->getId()));
        }

        return $collection;
    }

    /**
     * Prepare attribute for use in layered navigation
     *
     * @param   Mage_Eav_Model_Entity_Attribute $attribute
     * @return  Mage_Eav_Model_Entity_Attribute
     */
    protected function _prepareAttribute($attribute)
    {
        $attribute = parent::_prepareAttribute($attribute);
        $attribute->setIsFilterable(Mage_Catalog_Model_Layer_Filter_Attribute::OPTIONS_ONLY_WITH_RESULTS);
        
		return $attribute;
    }

    public function getAttributeOptionsImages($attribute_id)
    {
        if (is_null($this->attribute_options_images)) {
            $this->attribute_options_images = array();

            $options = Mage::getModel('gomage_navigation/attribute_option')
                ->getCollection();

            foreach ($options as $option) {
                if (!isset($this->attribute_options_images[$option->getData('attribute_id')])) {
                    $this->attribute_options_images[$option->getData('attribute_id')] = array();
                }
                $this->attribute_options_images[$option->getData('attribute_id')][$option->getData('option_id')] =
                    $option->getData('filename');

            }

        }
		
        return isset($this->attribute_options_images[$attribute_id]) ? $this->attribute_options_images[$attribute_id] : array();
    }
}