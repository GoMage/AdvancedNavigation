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
 * @since        Class available since Release 2.0
 */
class GoMage_Navigation_Model_Catalog_Layer_Filter_Decimal extends Mage_Catalog_Model_Layer_Filter_Decimal
{	
	protected $_resource;
	
	/**
     * Initialize filter items
     *
     * @return  Mage_Catalog_Model_Layer_Filter_Abstract
     */
    protected function _initItems()
    {
        $data  = $this->_getItemsData();
        $items = array();
        
		foreach ($data as $itemData) {
            $items[] = $this->_createItem(
                $itemData['label'],
                $itemData['value'],
                $itemData['count'],
                $itemData['active'], 
                isset($itemData['from_to']) ? $itemData['from_to'] : ''
            );
        }
        
		$this->_items = $items;
        
		return $this;
    }

    /**
     * Create filter item object
     *
     * @param   string $label
     * @param   mixed $value
     * @param   int $count
     * @return  Mage_Catalog_Model_Layer_Filter_Item
     */
    protected function _createItem($label, $value, $count = 0, $status = false, $from_to = '')
    {
        return Mage::getModel('gomage_navigation/catalog_layer_filter_item')
            ->setFilter($this)
            ->setLabel($label)
            ->setValue($value)
            ->setCount($count)
            ->setActive($status)
            ->setFromTo($from_to);
    }

    /**
     * Apply decimal range filter to product collection
     *
     * @param Zend_Controller_Request_Abstract $request
     * @param Mage_Catalog_Block_Layer_Filter_Decimal $filterBlock
     * @return Mage_Catalog_Model_Layer_Filter_Decimal
     */
    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock)
    {
        switch ($this->getAttributeModel()->getFilterType()) {
            case (GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_INPUT) :
                $from = $request->getParam($this->getRequestVarValue() . '_from', false);
                $to = $request->getParam($this->getRequestVarValue() . '_to', false);

                if ($to !== false) {
                    $to += 0.01;
                }

                if ($from || $to) {
                    $value = array('from' => $from, 'to' => $to);

                    $this->_getResource()->applyFilterToCollection($this, $value);

                    $store = Mage::app()->getStore();
                    $fromPrice = $store->formatPrice($from);
                    $toPrice = $store->formatPrice($to);

                    $this->getLayer()->getState()->addFilter(
                        $this->_createItem(Mage::helper('catalog')->__('%s - %s', $fromPrice, $toPrice), $value)
                    );
                } else {
                    return $this;
                }
                break;
            case (GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_SLIDER) :
            case (GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_SLIDER_INPUT) :
            case (GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_INPUT_SLIDER) :
                if (Mage::helper('gomage_navigation')->isMobileDevice()) {
                    /**
                     * Filter must be string: $index,$range
                     */
                    $filter = $request->getParam($this->getRequestVarValue());

                    if (!$filter) {
                        return $this;
                    }

                    $filter = explode(',', $filter);

                    if (count($filter) < 2) {
                        return $this;
                    }

                    $length = count($filter);
                    $value = array();

                    for ($i = 0; $i < $length; $i += 2) {
                        $value[] = array(
                            'index' => $filter[$i],
                            'range' => $filter[$i + 1],
                        );
                    }

                    if (!empty($value)) {
                        $this->setRange((int)$value[0]['range']);
                        $this->_getResource()->applyFilterToCollection($this, $value);

                        foreach ($value as $_value) {
                            $this->getLayer()->getState()->addFilter(
                                $this->_createItem($this->_renderItemLabel($_value['range'], $_value['index']), $_value)
                            );
                        }
                    }
                } else {
                    $from = $request->getParam($this->getRequestVarValue() . '_from', false);
                    $to = $request->getParam($this->getRequestVarValue() . '_to', false);

                    if ($to !== false) {
                        $to += 0.01;
                    }

                    if ($from || $to) {
                        $value = array('from' => $from, 'to' => $to);

                        $this->_getResource()->applyFilterToCollection($this, $value);

                        $store = Mage::app()->getStore();
                        $fromPrice = $store->formatPrice($from);
                        $toPrice = $store->formatPrice($to);

                        $this->getLayer()->getState()->addFilter(
                            $this->_createItem(Mage::helper('catalog')->__('%s - %s', $fromPrice, $toPrice), $value)
                        );
                    } else {
                        return $this;
                    }
                }
                break;

            default :
                $attribute = $this->getAttributeModel();
                if ($attribute->getFrontendInput() === 'price'
                    && ($attribute->getRangeOptions() == GoMage_Navigation_Model_Adminhtml_System_Config_Source_Filter_Optionsrange::MANUALLY
                        || $attribute->getRangeOptions() == GoMage_Navigation_Model_Adminhtml_System_Config_Source_Filter_Optionsrange::AUTO)
                    && $attribute->getFilterType() == GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_DEFAULT
                ) {
                    $from = $request->getParam($this->getRequestVarValue() . '_from', false);
                    $to = $request->getParam($this->getRequestVarValue() . '_to', false);

                    if ($to !== false) {
                        $to += 0.01;
                    }

                    if ($from || $to) {
                        $value = array('from' => $from, 'to' => $to);

                        $this->_getResource()->applyFilterToCollection($this, $value);

                        $store = Mage::app()->getStore();
                        $fromPrice = $store->formatPrice($from);
                        $toPrice = $store->formatPrice($to);

                        $this->getLayer()->getState()->addFilter(
                            $this->_createItem(Mage::helper('catalog')->__('%s - %s', $fromPrice, $toPrice), $value)
                        );
                    } else {
                        $filter = $request->getParam($this->getRequestVarValue());

                        if (!$filter) {
                            return $this;
                        }

                        $filter = explode(',', $filter);

                        if (count($filter) < 2) {
                            return $this;
                        }

                        $length = count($filter);
                        $value = array();

                        for ($i = 0; $i < $length; $i += 2) {
                            $value[] = array(
                                'from' => $filter[$i],
                                'to' => $filter[$i + 1],
                            );
                        }

                        if (!empty($value)) {
                            foreach ($value as $_value) {
                                $this->_getResource()->applyFilterToCollection($this, $_value);

                                $store = Mage::app()->getStore();
                                $fromPrice = $store->formatPrice($_value['from']);
                                $toPrice = $store->formatPrice($_value['to']);

                                $this->getLayer()->getState()->addFilter(
                                    $this->_createItem(Mage::helper('catalog')->__('%s - %s', $fromPrice, $toPrice),
                                        $_value)
                                );
                            }
                        }
                    }
                } else {
                    /**
                     * Filter must be string: $index,$range
                     */
                    $filter = $request->getParam($this->getRequestVarValue());

                    if (!$filter) {
                        return $this;
                    }

                    $filter = explode(',', $filter);

                    if (count($filter) < 2) {
                        return $this;
                    }

                    $length = count($filter);
                    $value = array();

                    for ($i = 0; $i < $length; $i += 2) {
                        $value[] = array(
                            'index' => $filter[$i],
                            'range' => $filter[$i + 1],
                        );
                    }

                    if (!empty($value)) {
                        $this->setRange((int)$value[0]['range']);
                        $this->_getResource()->applyFilterToCollection($this, $value);

                        foreach ($value as $_value) {
                            $this->getLayer()->getState()->addFilter(
                                $this->_createItem($this->_renderItemLabel($_value['range'], $_value['index']), $_value)
                            );
                        }
                    }
                }
                break;
        }


        return $this;
    }

    /**
     * Retrieve data for build decimal filter items
     *
     * @return array
     */
    protected function _getItemsData()
    {
        $key = $this->_getCacheKey();
        $selected = $this->_getSelectedOptions();
        $data = $this->getLayer()->getAggregator()->getCacheData($key);
        $filterMode = Mage::helper('gomage_navigation')->isGomageNavigation();

        if ($data === null) {
            $attribute = $this->getAttributeModel();

            if ($attribute->getRangeOptions() == GoMage_Navigation_Model_Adminhtml_System_Config_Source_Filter_Optionsrange::AUTO
                && $attribute->getFilterType() == GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_DEFAULT
                && $attribute->getRangeAuto()
                && $attribute->getRangeAuto() != '=,'
            ) {
                $auto = $attribute->getRangeAuto();
                $autoArray = explode(",", $auto);

                $sort = array();

                foreach ($autoArray as $rangeAuto) {
                    $rangeArray = explode("=", $rangeAuto);

                    if ($rangeArray[0] != '') {
                        $sort[$rangeArray[0]] = $rangeAuto;
                    }
                }

                ksort($sort);
                $limitStart = 0;
                $first = true;

                foreach ($sort as $rangeAuto) {
                    $rangeArray = explode("=", $rangeAuto);
                    $theRange = trim($rangeArray[1]);
                    $limitEnd = trim($rangeArray[0]);

                    $this->setData('price_range', $theRange);

                    $range = $theRange;
                    $dbRanges = $this->getRangeItemCounts($range);

                    foreach ($dbRanges as $index => $count) {
                        if ($limitStart < $index * $range && $limitEnd >= $index * $range
                            &&
                            $count > 0
                        ) {
                            if ($first || (!$first && $index > 0)) {
                                $first = false;
                                $value = $index . ',' . $range;

                                if (in_array($value, $selected) && !$filterMode) {
                                    continue;
                                }

                                if (in_array($value, $selected) && $filterMode) {
                                    $active = true;
                                } else {
                                    $active = false;
                                }

                                $store = Mage::app()->getStore();
                                $from = (($index - 1) * $range);
                                $fromPrice = $store->formatPrice(($index - 1) * $range);
                                $to = $index * $range  - 0.01;
                                $toPrice = $store->formatPrice($to);
                                $label = Mage::helper('catalog')->__('%s - %s', $fromPrice, $toPrice);
                                $priceFrom = Mage::helper('gomage_navigation')->getRequest()
                                    ->getParam($attribute->getAttributeCode() . '_from', false);

                                if ($priceFrom) {
                                    $priceFrom = explode(',', $priceFrom);
                                } else {
                                    $priceFrom = array();
                                }

                                $price_to = Mage::helper('gomage_navigation')->getRequest()
                                    ->getParam($attribute->getAttributeCode() . '_to', false);

                                if ($price_to) {
                                    $price_to = explode(',', $price_to);
                                } else {
                                    $price_to = array();
                                }

                                $active = $active || (in_array($from, $priceFrom) && in_array($to, $price_to));
                                $priceValue = '';

                                if (count($priceFrom) && count($price_to)) {
                                    if (!in_array($from, $priceFrom)) {
                                        $priceFrom[] = $from;
                                    }

                                    if (!in_array($to, $price_to)) {
                                        $price_to[] = $to;
                                    }

                                    $priceValue = implode(',', $priceFrom) . ';' . implode(',', $price_to);
                                }

                                $data[] = array(
                                    'label' => $label,
                                    'value' => ($priceValue ? $priceValue : $from . ',' . $to),
                                    'count' => $count,
                                    'active' => $active,
                                    'from_to' => $from . ',' . $to,
                                );
                            }

                        }
                    }
                    $limitStart = $limitEnd;
                }
            } else {
                $range = $this->getRange();
                $dbRanges = $this->getRangeItemCounts($range);
                $data = array();

                if ($selected) {
                    foreach ($selected as $value) {
                        $value = explode(',', $value);

                        $dbRanges[$value[0]] = 0;
                    }

                    ksort($dbRanges);
                }

                foreach ($dbRanges as $index => $count) {
                    $value = $index . ',' . $range;

                    if (in_array($value, $selected) && !$filterMode) {
                        continue;
                    }

                    if (in_array($value, $selected) && $filterMode) {
                        $active = true;
                    } else {
                        $active = false;

                        if (!empty($selected) && $this->getAttributeModel()->getFilterType() != GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_DROPDOWN) {
                            $value = implode(',', array_merge($selected, (array)$value));
                        }
                    }

                    $data[] = array(
                        'label' => $this->_renderItemLabel($range, $index),
                        'value' => $value,
                        'count' => $count,
                        'active' => $active,
                    );
                }
            }

            $tags = array(
                Mage_Catalog_Model_Product_Type_Price::CACHE_TAG,
            );
            $tags = $this->getLayer()->getStateTags($tags);
            $this->getLayer()->getAggregator()->saveCacheData($data, $key, $tags);
        }

        return $data;
    }
	
	/**
     * Retrieve resource instance
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Layer_Filter_Decimal
     */
    protected function _getResource()
    {
        if (is_null($this->_resource)) {
            $this->_resource = Mage::getModel('gomage_navigation/resource_eav_mysql4_layer_filter_decimal');
        }
		
        return $this->_resource;
    }
	
	/**
     * Get filter value for reset current filter state
     *
     * @return null|string
     */
    public function getResetValue($value_to_remove = null)
    {
        if ($value_to_remove && ($current_value = Mage::helper('gomage_navigation')->getRequest()->getParam($this->_requestVar))) {
            if (is_array($value_to_remove)) {
                if (isset($value_to_remove['index']) && isset($value_to_remove['range'])) {
                    $value_to_remove = $value_to_remove['index'] . ',' . $value_to_remove['range'];
                } else {
                    return null;
                }
            }

            $current_value = $this->_getSelectedOptions();

            if (false !== ($position = array_search($value_to_remove, $current_value))) {
                unset($current_value[$position]);

                if (!empty($current_value)) {
                    return implode(',', $current_value);
                }
            }
        }

        return null;
    }
	
	/*****/
	
	public function hasAttributeModel()
    {   
		return $this->hasData('attribute_model');
    }
	
	public function getRequestVarValue()
    {
        return $this->_requestVar;
    }
	
	public function getMinValueInt()
    {
        return floor($this->getMinValue());
    }

    public function getMaxValueInt()
    {
        return ceil($this->getMaxValue());
    }
	
	protected function _getSelectedOptions()
    {
        if (is_null($this->_selected_options)) {
            $selected = array();

            if ($value = Mage::helper('gomage_navigation')->getRequest()->getParam($this->getRequestVarValue())) {
                $_selected	= array_merge($selected, explode(',', $value));
                $length		= count($_selected);

                for ($i = 0; $i < $length; $i += 2) {
                    $selected[] = $_selected[$i] . ',' . $_selected[$i + 1];
                }
            }

            $this->_selected_options = $selected;
        }
        
		return $this->_selected_options;
    }

    /**
     * @inheritdoc
     */
    protected function _renderItemLabel($range, $value)
    {
        $from = Mage::app()->getStore()->formatPrice(($value - 1) * $range, false);
        $to = Mage::app()->getStore()->formatPrice($value * $range - 0.01, false);

        return Mage::helper('catalog')->__('%s - %s', $from, $to);
    }
}
