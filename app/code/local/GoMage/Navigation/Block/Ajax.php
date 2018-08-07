<?php
/**
 * GoMage Advanced Navigation Extension
 *
 * @category     Extension
 * @copyright    Copyright (c) 2010-2016 GoMage (https://www.gomage.com)
 * @author       GoMage
 * @license      https://www.gomage.com/license-agreement/  Single domain license
 * @terms of use https://www.gomage.com/terms-of-use
 * @version      Release: 4.9.2
 * @since        Class available since Release 1.0
 */

class GoMage_Navigation_Block_Ajax extends Mage_Core_Block_Abstract {
	
	protected $eval_js = array();
	
	public function addEvalJs($str, $param = 'eval_js') {
		
		$this->setData($param, $this->getData($param) . ";" . $str);
	
	}

}