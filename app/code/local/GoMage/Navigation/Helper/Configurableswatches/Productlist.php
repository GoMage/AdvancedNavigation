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
 * @since        Class available since Release 4.7
 */
 
class GoMage_Navigation_Helper_ConfigurableSwatches_Productlist extends Mage_ConfigurableSwatches_Helper_Productlist
{
	/**
     * Convert a catalog layer block with the right templates
     *
     * @param string $blockName
     * @return void
     */
    public function convertLayerBlock($blockName)
    {
		if (!Mage::helper('gomage_navigation')->isGomageNavigation()) {
			return parent::convertLayerBlock($blockName);
		}
		
        if (Mage::helper('configurableswatches')->isEnabled()
            && ($block = Mage::app()->getLayout()->getBlock($blockName))
            && $block instanceof Mage_Catalog_Block_Layer_View
        ) {

            // First, set a new template for the attribute that should show as a swatch
            if ($layer = $block->getLayer()) {
                foreach ($layer->getFilterableAttributes() as $attribute) {
                    if (Mage::helper('configurableswatches')->attrIsSwatchType($attribute)) {
                        $block->getChild($attribute->getAttributeCode() . '_filter')
                            ->setTemplate('gomage/navigation/layer/filter/swatches.phtml');
                    }
                }
            }

            // Then set a specific renderer block for showing "currently shopping by" for the swatch attribute
            // (block class takes care of determining which attribute is applicable)
            if ($stateRenderersBlock = $block->getChild('state_renderers')) {
                $swatchRenderer = Mage::app()->getLayout()
                    ->addBlock('configurableswatches/catalog_layer_state_swatch', 'product_list.swatches');
                $swatchRenderer->setTemplate('configurableswatches/catalog/layer/state/swatch.phtml');
                $stateRenderersBlock->append($swatchRenderer);
            }
        }
    }
}