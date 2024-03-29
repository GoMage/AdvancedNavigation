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
class GoMage_Navigation_Model_Enterprise_Search_Catalog_Layer_Filter_Decimal extends Enterprise_Search_Model_Catalog_Layer_Filter_Decimal
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
                $itemData['active']
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
    protected function _createItem($label, $value, $count = 0, $status = false)
    {
        return Mage::getModel('gomage_navigation/catalog_layer_filter_item')
            ->setFilter($this)
            ->setLabel($label)
            ->setValue($value)
            ->setCount($count)
            ->setActive($status);
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
                $_from = $request->getParam($this->getRequestVarValue() . '_from', false);
                $_to   = $request->getParam($this->getRequestVarValue() . '_to', false);

                if ($_from || $_to) {
                    $value = array('from' => $_from, 'to' => $_to);
                    
					$this->_getResource()->applyFilterToCollection($this, $value);
                    
					$store     = Mage::app()->getStore();
                    $fromPrice = $store->formatPrice($_from);
                    $toPrice   = $store->formatPrice($_to);

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

                    $filter	= explode(',', $filter);
                   
				    if (count($filter) < 2) {
                        return $this;
                    }

                    $length	= count($filter);
                    $value	= array();

                    for ($i = 0; $i < $length; $i += 2) {
                        $value[] = array(
                            'index' => $filter[$i],
                            'range' => $filter[$i + 1],
                        );
                    }
					
                    if (!empty($value)) {
                        $this->setRange((int) $value[0]['range']);
                        $this->_getResource()->applyFilterToCollection($this, $value);

                        foreach ($value as $_value) {
                            $this->getLayer()->getState()->addFilter(
                                $this->_createItem($this->_renderItemLabel($_value['range'], $_value['index']), $_value)
                            );
                        }
                    }
                } else {
                    $_from = $request->getParam($this->getRequestVarValue() . '_from', false);
                    $_to   = $request->getParam($this->getRequestVarValue() . '_to', false);

                    if ($_from || $_to) {
                        $value = array('from' => $_from, 'to' => $_to);

                        $this->_getResource()->applyFilterToCollection($this, $value);

                        $store     = Mage::app()->getStore();
                        $fromPrice = $store->formatPrice($_from);
                        $toPrice   = $store->formatPrice($_to);

                        $this->getLayer()->getState()->addFilter(
                            $this->_createItem(Mage::helper('catalog')->__('%s - %s', $fromPrice, $toPrice), $value)
                        );
                    } else {
                        return $this;
                    }
                }
			break;

            default :
                /**
                 * Filter must be string: $index,$range
                 */
                $filter = $request->getParam($this->getRequestVarValue());
                
				if (!$filter) {
                    return $this;
                }

                $filter	= explode(',', $filter);
				
                if (count($filter) < 2) {
                    return $this;
                }

                $length	= count($filter);
                $value	= array();

                for ($i = 0; $i < $length; $i += 2) {
                    $value[] = array(
                        'index' => $filter[$i],
                        'range' => $filter[$i + 1],
                    );
                }
				
                if (!empty($value)) {
                    $this->setRange((int) $value[0]['range']);
                    $this->_getResource()->applyFilterToCollection($this, $value);

                    foreach ($value as $_value) {
                        $this->getLayer()->getState()->addFilter(
                            $this->_createItem($this->_renderItemLabel($_value['range'], $_value['index']), $_value)
                        );
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
		$selected		= $this->_getSelectedOptions();
		$range			= $this->getRange();
        $attribute_code	= $this->getAttributeModel()->getAttributeCode();
        $facets			= $this->getLayer()->getProductCollection()->getFacetedData('attr_decimal_' . $attribute_code);
        $data			= array();
		
        if (!empty($facets)) {
            foreach ($facets as $key => $count) {
                preg_match('/TO ([\d\.]+)\]$/', $key, $rangeKey);
                $rangeKey	= $rangeKey[1] / $range;
				$rangeKey	= round($rangeKey);
				$active		= false;
                $value		= $rangeKey . ',' . $range;
				
				if (in_array($value, $selected)) {
                    $active = true;
                } else {
                    $active = false;

                    if (!empty($selected) && $this->getAttributeModel()->getFilterType() != GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_DROPDOWN) {
                        $value = implode(',', array_merge($selected, (array) $value));
                    }
                }
				
				if ($count > 0) {                 
                    $data[] = array(
                        'label'		=> $this->_renderItemLabel($range, $rangeKey),
                        'value'		=> $value,
                        'count'		=> $count,
						'active'	=> $active,
                    );
                }
            }
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
	
	/**
     * Add params to faceted search
     *
     * @return Enterprise_Search_Model_Catalog_Layer_Filter_Decimal
     */
    public function addFacetCondition()
    {
        $range    = $this->getRange();
        $maxValue = $this->getMaxValue();
        
		if ($maxValue > 0) {
            $facets = array();
			
			if (!($maxValue % $range)) {
				$maxValue = $maxValue + 0.001;
			}
			
            $facetCount = ceil($maxValue / $range);
            
			for ($i = 0; $i < $facetCount; $i++) {
                $facets[] = array(
                    'from' => $i * $range,
                    'to'   => ($i + 1) * $range - 0.001
                );
            }

            $attributeCode = $this->getAttributeModel()->getAttributeCode();
            $field         = 'attr_decimal_' . $attributeCode;

            $this->getLayer()->getProductCollection()->setFacetCondition($field, $facets);
        }

        return $this;
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
}
