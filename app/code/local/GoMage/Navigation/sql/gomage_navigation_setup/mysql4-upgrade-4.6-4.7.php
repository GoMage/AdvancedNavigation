<?php
 /**
 * GoMage Advanced Navigation Extension
 *
 * @category     Extension
 * @copyright    Copyright (c) 2010-2016 GoMage (https://www.gomage.com)
 * @author       GoMage
 * @license      https://www.gomage.com/license-agreement/  Single domain license
 * @terms of use https://www.gomage.com/terms-of-use
 * @version      Release: 4.9.1
 * @since        Release available since Release 4.7
 */

$installer = $this;

$installer->startSetup();

$pageTable = $installer->getTable('cms/page');
$installer->getConnection()->addColumn($pageTable, 'navigation',
    "TINYINT(1) NOT NULL DEFAULT 0");

$installer->endSetup(); 