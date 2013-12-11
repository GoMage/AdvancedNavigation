<?php
 /**
 * GoMage Advanced Navigation Extension
 *
 * @category     Extension
 * @copyright    Copyright (c) 2010-2013 GoMage (http://www.gomage.com)
 * @author       GoMage
 * @license      http://www.gomage.com/license-agreement/  Single domain license
 * @terms of use http://www.gomage.com/terms-of-use
 * @version      Release: 4.2
 * @since        Class available since Release 1.0
 */

class GoMage_Navigation_Block_Adminhtml_Cms_Page_Edit_Tab_Navigation 
       extends Mage_Adminhtml_Block_Widget_Form
       implements Mage_Adminhtml_Block_Widget_Tab_Interface   
{
    
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;
    
    public function __construct()
    {
        parent::__construct();
        $this->setShowGlobalIcon(true);
    }
            
    protected function _prepareForm() 
    {
        if ($this->_isAllowedAction('save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('page_');

        $model = Mage::registry('cms_page');

        $navigationFieldset = $form->addFieldset('navigation_fieldset', array(
            'legend' => Mage::helper('gomage_navigation')->__('Advanced Navigation'),
            'class'  => 'fieldset-wide',
            'disabled'  => $isElementDisabled
        ));

        $navigationFieldset->addField('navigation_left_column', 'select', array(
            'label'     => Mage::helper('gomage_navigation')->__('Show in Left Column'),
            'title'     => Mage::helper('gomage_navigation')->__('Show in Left Column'),
            'name'      => 'navigation_left_column',            
            'options'   => $this->getAvailableNavigationStatuses(),
            'disabled'  => $isElementDisabled,
        ));

        $navigationFieldset->addField('navigation_content_column', 'select', array(
                'label'     => Mage::helper('gomage_navigation')->__('Show in Central Column'),
                'title'     => Mage::helper('gomage_navigation')->__('Show in Central Column'),
                'name'      => 'navigation_content_column',
                'options'   => $this->getAvailableNavigationStatuses(),
                'disabled'  => $isElementDisabled,
            ));

        $navigationFieldset->addField('navigation_right_column', 'select', array(
            'label'     => Mage::helper('gomage_navigation')->__('Show in Right Column'),
            'title'     => Mage::helper('gomage_navigation')->__('Show in Right Column'),
            'name'      => 'navigation_right_column',            
            'options'   => $this->getAvailableNavigationStatuses(),
            'disabled'  => $isElementDisabled,
        ));
        
        $navigationFieldset->addField('navigation_category_id', 'select', array(
            'label'     => Mage::helper('gomage_navigation')->__('Category'),
            'title'     => Mage::helper('gomage_navigation')->__('Category'),
            'name'      => 'navigation_category_id',            
            'options'   => $this->getAvailableCategories(),
            'disabled'  => $isElementDisabled,
        ));
        
        Mage::dispatchEvent('adminhtml_cms_page_edit_tab_navigation_prepare_form', array('form' => $form));

        $form->setValues($model->getData());

        $this->setForm($form);
        
        return parent::_prepareForm();
    }

    protected function  _getCategories($categories, $prefix = '-') {
        $array = '';
        foreach($categories as $category) {
            $array .= $prefix . '_' . $category->getId() . '_' . $category->getName() . ';';
            if($category->hasChildren()) {
                $children = Mage::getModel('catalog/category')->getCategories($category->getId());
                $array .=  $this->_getCategories($children, $prefix . '-');
            }
        }
        return  $array;
    }
    
    public function getAvailableCategories(){
    	$options = array(
            0 => Mage::helper('gomage_navigation')->__('-Select-'),
        );
        
        $websites = Mage::helper('gomage_navigation')->getAvailavelWebsites();
        
        if(!empty($websites)){        
        	$store_ids = array();
            foreach ($websites as $website_id){
                $website = Mage::getModel('core/website')->load($website_id);
                $store_ids = array_unique(array_merge($store_ids, $website->getStoreIds()));
            }
 			if(!count($store_ids)){
            	$store_ids = array(Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID); 
        	}
            
        	foreach ($store_ids as $store_id)
	        {
	            $collection = Mage::getModel('catalog/category')->getCollection()
	                            ->setStoreId($store_id)                       
	                            ->addAttributeToSelect('name')
                                ->addAttributeToSort('name', 'asc') ;
	    	                            
	            $_root_category = Mage::getModel('core/store')->load($store_id)->getRootCategoryId();
	                    
	            $_root_level = Mage::getModel('catalog/category')->load($_root_category)->getLevel();                
	                            
	            $collection->addFieldToFilter(array(
	                    array('attribute'=>'level', 'gt'=>$_root_level)                            
	            ));

                $categories = Mage::getModel('catalog/category')->getCategories($_root_category);

                $string = $this->_getCategories($categories, '-');
                $catArray = explode(";", $string);

                foreach ($catArray as $catString) {
                    $catArrayOptions = explode("_", $catString);

                    $padding = $catArrayOptions[0];

                    if ( isset($catArrayOptions[1]) )
                    {
                        $catId = $catArrayOptions[1];
                        $name = $catArrayOptions[2];

                        if (!isset($options[$catId]) && $name)
                        {
                            $options[$catId] = $padding . $name;
                        }
                    }
                }
	        }
        }
        
        $result = new Varien_Object($options);        

        return $result->getData();
        
    }
    
    public function getAvailableNavigationStatuses()
    {
        $options = array(
            self::STATUS_DISABLED => Mage::helper('gomage_navigation')->__('No'),
        );
        
        $websites = Mage::helper('gomage_navigation')->getAvailavelWebsites();
        
        if(!empty($websites)){
        	$options[self::STATUS_ENABLED] = Mage::helper('gomage_navigation')->__('Yes');
        }
        
        $statuses = new Varien_Object($options);        

        return $statuses->getData();
    }
       
    public function getTabLabel()
    {
        return Mage::helper('gomage_navigation')->__('Advanced Navigation');
    }

    public function getTabTitle()
    {
        return Mage::helper('gomage_navigation')->__('Advanced Navigation');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }
    
    protected function _isAllowedAction($action)
    {
        return Mage::getSingleton('admin/session')->isAllowed('cms/page/' . $action);
    }
    
} 
