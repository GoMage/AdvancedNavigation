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

class GoMage_Navigation_Block_Product_List_More extends Mage_Core_Block_Template
{
	public function __construct()
    {
        parent::__construct();
        if ($this->showMoreButton()){
        	$this->setTemplate('gomage/navigation/catalog/product/list/more.phtml');
        }                               
    } 
	 
	public function showMoreButton(){
		return Mage::getStoreConfig('gomage_navigation/general/autoscrolling') && Mage::helper('gomage_navigation')->isGomageNavigationAjax();
	}
	
	public function getMoreUrl(){		
		$url = '';						
		$pager = $this->getPagerBlock();		
		if ($pager){
			if (!$pager->isLastPage()){
				$url = $pager->getNextPageUrl();
			}
		}		
		return $url;
	}
	
  	public function getPagerBlock()
    {
    	$toolbar = $this->getLayout()->getBlock('product_list_toolbar');
        if (!$toolbar || !$toolbar->getCollection()) return false;				
        $pagerBlock = $toolbar->getChild('product_list_toolbar_pager');

        if ($pagerBlock instanceof Varien_Object) {

            /* @var $pagerBlock Mage_Page_Block_Html_Pager */
            $pagerBlock->setAvailableLimit($toolbar->getAvailableLimit());

            $pagerBlock->setUseContainer(false)
                ->setShowPerPage(false)
                ->setShowAmounts(false)
                ->setLimitVarName($toolbar->getLimitVarName())
                ->setPageVarName($toolbar->getPageVarName())
                ->setLimit($toolbar->getLimit())
                ->setFrameLength(Mage::getStoreConfig('design/pagination/pagination_frame'))
                ->setJump(Mage::getStoreConfig('design/pagination/pagination_frame_skip'))
                ->setCollection($toolbar->getCollection());

            return $pagerBlock;
        }

        return false;
    }
                    
}
