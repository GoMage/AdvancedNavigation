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

class GoMage_Navigation_Model_Resource_Eav_Mysql4_Layer_Filter_Category extends Mage_Catalog_Model_Resource_Eav_Mysql4_Layer_Filter_Attribute
{
	
	public function prepareSelect($filter, $value, $select){
		
        $alias = 'category_idx';
        
        $value = (array)$value;
        
        foreach($value as $_value){
			$where[] = intval($_value);
		}

        if ( Mage::helper('gomage_navigation')->isEnterprise() )
        {
            $store_id = Mage::app()->getStore()->getId();
            $select->join(
                array($alias => Mage::getSingleton('core/resource')->getTableName('catalog/category_product_index')),
                $alias.'.category_id IN ('.implode(',',$where).') AND '.$alias.'.product_id = e.entity_id AND '.$alias.'.store_id="' . $store_id . '"',
                array()
            );
        }
        else
        {
            $select->join(
                    array($alias => Mage::getSingleton('core/resource')->getTableName('catalog/category_product_index')),
                    $alias.'.category_id IN ('.implode(',',$where).') AND '.$alias.'.product_id = e.entity_id',
                    array()
                );
        }
	}
     
     /**
     * Apply attribute filter to product collection
     *
     * @param Mage_Catalog_Model_Layer_Filter_Attribute $filter
     * @param int $value
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Layer_Filter_Attribute
     */
     
     
    public function applyFilterToCollection($filter, $value)
    {
        $collection = $filter->getLayer()->getProductCollection();
        
        $this->prepareSelect($filter, $value, $collection->getSelect());
        
        $base_select = $filter->getLayer()->getBaseSelect();
        
        foreach($base_select as $code=>$select){
        	
        	
        	if('cat' != $code){
        	
        		$this->prepareSelect($filter, $value, $select);
        	
        	}
        	
        }
        
        return $this;
    }

    /**
     * Retrieve array with products counts per attribute option
     *
     * @param Mage_Catalog_Model_Layer_Filter_Attribute $filter
     * @return array
     */
    public function getCount($filter)
    {
    	
        $categories = $filter->getLayer()->getCurrentCategory()->getAllChildren(true);
    	
    	$connection = $this->_getReadAdapter();
    	
		$base_select = $filter->getLayer()->getBaseSelect();
		        
        if(isset($base_select['cat'])){
        	
        	
        	$select = $base_select['cat'];        	
        
        }else{
        	
        	$select = clone $filter->getLayer()->getProductCollection()->getSelect();
        	
        }
		
		$where = array();
		
		$catIds = array();
		
		
		foreach($categories as $category){
			
			$catIds[] = $category;
			
		}
		
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->reset(Zend_Db_Select::ORDER);
        $select->reset(Zend_Db_Select::LIMIT_COUNT);
        $select->reset(Zend_Db_Select::LIMIT_OFFSET);
        $select->reset(Zend_Db_Select::GROUP);
							
		$select->join(
            array('child_cat_index' => Mage::getSingleton('core/resource')->getTableName('catalog/category_product_index')),
            'child_cat_index.category_id IN ('.implode(',',$catIds).') AND child_cat_index.product_id = e.entity_id',
            array('value'=>'category_id')
        );

        $fields = array('count'=>'COUNT(DISTINCT child_cat_index.product_id)');		
        $select->columns($fields);
        $select->group('child_cat_index.category_id');
        
        $_collection = clone $filter->getLayer()->getProductCollection();
    	$searched_entity_ids = $_collection->load()->getSearchedEntityIds();
        if ($searched_entity_ids && is_array($searched_entity_ids) && count($searched_entity_ids)){
        	$select->where('e.entity_id IN (?)', $searched_entity_ids);	
        } 
        
        return $connection->fetchPairs($select);
        
    }
}
