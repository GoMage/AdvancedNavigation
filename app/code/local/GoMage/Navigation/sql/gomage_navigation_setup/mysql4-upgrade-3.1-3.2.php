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
 * @since        Release available since Release 3.2
 */

$installer = $this;

$installer->startSetup();

$installer->getConnection()->addColumn($this->getTable('gomage_navigation_attribute'), 'category_ids_filter',
    "VARCHAR(250) NOT NULL");
   
$installer->endSetup(); 