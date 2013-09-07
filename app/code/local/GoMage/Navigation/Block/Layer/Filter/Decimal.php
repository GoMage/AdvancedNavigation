<?php
 /**
 * GoMage Advanced Navigation Extension
 *
 * @category     Extension
 * @copyright    Copyright (c) 2010-2011 GoMage (http://www.gomage.com)
 * @author       GoMage
 * @license      http://www.gomage.com/license-agreement/  Single domain license
 * @terms of use http://www.gomage.com/terms-of-use
 * @version      Release: 2.0
 * @since        Class available since Release 1.0
 */

class GoMage_Navigation_Block_Layer_Filter_Decimal extends Mage_Catalog_Block_Layer_Filter_Decimal
{
	
	public function getRemoveUrl($ajax = false)
    {
        $query = array($this->_filter->getRequestVar()=>null);
        $params['_nosid']       = true;
        $params['_current']     = true;
        $params['_use_rewrite'] = true;
        $params['_query']       = $query;
        $params['_escape']      = false;
        
        $params['_query']['ajax'] = null;
        
        if($ajax){
        	
        	$params['_query']['ajax'] = true;
        	
        	
        }
        
        
        return Mage::getUrl('*/*/*', $params);
    }
    
    public function canShowPopup(){
		
		return (bool) ($this->getAttributeModel()->getShowHelp() > 0);
		
	}
	
    public function canShowResetFirler(){
		
		return (bool) ($this->getAttributeModel()->getFilterReset() > 0);
		
	}
	
	public function getPopupId(){
		
		return $this->getAttributeModel()->getAttributeCode();
		
	}
	
	public function ajaxEnabled(){
		
		if ($this->getAttributeModel()->getIsAjax() === '0') return 0;
		else return 1; 
	    		
	}
	
	public function getPopupText(){
		
		return trim($this->getAttributeModel()->getPopupText());
		
	}
	
	public function getPopupWidth(){
		
		return (int) $this->getAttributeModel()->getPopupWidth();
		
	}
	
	public function getPopupHeight(){
		
		return (int) $this->getAttributeModel()->getPopupHeight();
		
	}
	
	public function canShowMinimized(){
		
		if('true' === Mage::app()->getFrontController()->getRequest()->getParam($this->_filter->getRequestVar().'_is_open')){
		
			return false;
		
		}elseif('false' === Mage::app()->getFrontController()->getRequest()->getParam($this->_filter->getRequestVar().'_is_open')){
			
			return true;
			
		}
		
		
		return (bool) ($this->getAttributeModel()->getShowMinimized() > 0);
		
	}
	
	
	
	public function canShowCheckbox(){
		
		return (bool) $this->getAttributeModel()->getShowCheckbox();
		
	}
	
	public function canShowLabels(){
		
		return (bool) $this->getAttributeModel()->getShowImageName();
		
	}
	
    /**
     * Initialize filter template
     *
     */
	
    protected function _prepareFilter(){
    	
    	parent::_prepareFilter();
    	
    	if(Mage::getStoreConfigFlag('gomage_navigation/general/mode') > 0){
        	
        	switch($this->getAttributeModel()->getFilterType()): 
        	
	        	default:
	        	
	        		$this->_template = ('gomage/navigation/layer/filter/default.phtml');
	        	
	        	break;
	        	
	        	case(GoMage_Navigation_Model_Layer::FILTER_TYPE_INPUT):
	        	
	        		$this->_template = ('gomage/navigation/layer/filter/input.phtml');
	        	
	        	break;
	        	
	        	case(GoMage_Navigation_Model_Layer::FILTER_TYPE_SLIDER):
	        	
	        		$this->_template = ('gomage/navigation/layer/filter/slider.phtml');
	        	
	        	break;
	        	
	        	case(GoMage_Navigation_Model_Layer::FILTER_TYPE_SLIDER_INPUT):
	        	
	        		$this->_template = ('gomage/navigation/layer/filter/slider-input.phtml');
	        	
	        	break;
	        	
	        	case(GoMage_Navigation_Model_Layer::FILTER_TYPE_DROPDOWN):
	        	
	        		$this->_template = ('gomage/navigation/layer/filter/dropdown.phtml');
	        	
	        	break;
        	
        	endswitch;
        	
        }
    	
    }
}
