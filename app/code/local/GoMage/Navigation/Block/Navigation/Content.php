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

class GoMage_Navigation_Block_Navigation_Content extends Mage_Core_Block_Template
{

    protected function _prepareLayout()
    {

        $content = $this->getLayout()->getBlock('content');

        if ($content && Mage::helper('gomage_navigation')->isGomageNavigation() &&
            Mage::getStoreConfig('gomage_navigation/contentcolumnsettings/active'))
        {
            $content->unsetChild('gomage.navigation.content');
            $page = Mage::getSingleton('cms/page');
            if ($page->getData('page_id'))
            {
                if ($page->getData('navigation_content_column'))
                {
                    $navigation_content = $this->getLayout()->createBlock('gomage_navigation/navigation', 'gomage.navigation.content')
                        ->setTemplate('gomage/navigation/catalog/navigation/content.phtml');
                    $navigation_content->SetNavigationPlace(GoMage_Navigation_Block_Navigation::CONTENT_COLUMN);
                    $content->insert($navigation_content, '', false);
                }
            }
            else
            {
                if (!Mage::getStoreConfig('gomage_navigation/contentcolumnsettings/show_shopby')){
                    $navigation_content = $this->getLayout()->createBlock('gomage_navigation/navigation', 'gomage.navigation.content')
                        ->setTemplate('gomage/navigation/catalog/navigation/content.phtml');
                    $navigation_content->SetNavigationPlace(GoMage_Navigation_Block_Navigation::CONTENT_COLUMN);
                    $content->insert($navigation_content, '', false);
                }
            }
        }

        parent::_prepareLayout();

    }
}