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

require_once(Mage::getBaseDir('lib') . DS . 'GoMage' . DS . 'MobileDetect' . DS . 'Mobile_Detect.php');

class GoMage_Navigation_Helper_Data extends Mage_Core_Helper_Abstract
{

    public function getConfigData($node)
    {
        return Mage::getStoreConfig('gomage_navigation/' . $node);
    }

    public function getAllStoreDomains()
    {
        $domains = array();

        foreach (Mage::app()->getWebsites() as $website) {
            $url = $website->getConfig('web/unsecure/base_url');
            if ($domain = trim(preg_replace('/^.*?\\/\\/(.*)?\\//', '$1', $url))) {
                $domains[] = $domain;
            }

            $url = $website->getConfig('web/secure/base_url');

            if ($domain = trim(preg_replace('/^.*?\\/\\/(.*)?\\//', '$1', $url))) {
                $domains[] = $domain;
            }
        }
        return array_unique($domains);
    }

    public function getAvailabelWebsites()
    {
        return $this->_w();
    }

    public function getAvailavelWebsites()
    {
        return $this->_w();
    }

    protected function _w()
    {
        if (!Mage::getStoreConfig('gomage_activation/advancednavigation/installed') ||
            (intval(Mage::getStoreConfig('gomage_activation/advancednavigation/count')) > 10)
        ) {
            return array();
        }

        $time_to_update = 60 * 60 * 24 * 15;

        $r = Mage::getStoreConfig('gomage_activation/advancednavigation/ar');
        $t = Mage::getStoreConfig('gomage_activation/advancednavigation/time');
        $s = Mage::getStoreConfig('gomage_activation/advancednavigation/websites');

        $last_check = str_replace($r, '', Mage::helper('core')->decrypt($t));

        $allsites = explode(',', str_replace($r, '', Mage::helper('core')->decrypt($s)));
        $allsites = array_diff($allsites, array(""));

        if (($last_check + $time_to_update) < time()) {
            $this->a(Mage::getStoreConfig('gomage_activation/advancednavigation/key'),
                intval(Mage::getStoreConfig('gomage_activation/advancednavigation/count')),
                implode(',', $allsites)
            );
        }

        return $allsites;
    }

    public function a($k, $c = 0, $s = '')
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, sprintf('https://www.gomage.com/index.php/gomage_downloadable/key/check'));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'key=' . urlencode($k) . '&sku=advanced-navigation&domains=' . urlencode(implode(',', $this->getAllStoreDomains())) . '&ver=' . urlencode('4.2'));
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

        $content = curl_exec($ch);

        $r = Zend_Json::decode($content);
        $e = Mage::helper('core');
        if (empty($r)) {

            $value1 = Mage::getStoreConfig('gomage_activation/advancednavigation/ar');

            $groups = array(
                'advancednavigation' => array(
                    'fields' => array(
                        'ar'       => array(
                            'value' => $value1
                        ),
                        'websites' => array(
                            'value' => (string)Mage::getStoreConfig('gomage_activation/advancednavigation/websites')
                        ),
                        'time'     => array(
                            'value' => (string)$e->encrypt($value1 . (time() - (60 * 60 * 24 * 15 - 1800)) . $value1)
                        ),
                        'count'    => array(
                            'value' => $c + 1)
                    )
                )
            );

            Mage::getModel('adminhtml/config_data')
                ->setSection('gomage_activation')
                ->setGroups($groups)
                ->save();

            Mage::getConfig()->reinit();
            Mage::app()->reinitStores();

            return;
        }

        $value1 = '';
        $value2 = '';

        if (isset($r['d']) && isset($r['c'])) {
            $value1 = $e->encrypt(base64_encode(Zend_Json::encode($r)));

            if (!$s) {
                $s = Mage::getStoreConfig('gomage_activation/advancednavigation/websites');
            }

            $s      = array_slice(explode(',', $s), 0, $r['c']);
            $value2 = $e->encrypt($value1 . implode(',', $s) . $value1);
        }
        $groups = array(
            'advancednavigation' => array(
                'fields' => array(
                    'ar'        => array(
                        'value' => $value1
                    ),
                    'websites'  => array(
                        'value' => (string)$value2
                    ),
                    'time'      => array(
                        'value' => (string)$e->encrypt($value1 . time() . $value1)
                    ),
                    'installed' => array(
                        'value' => 1
                    ),
                    'count'     => array(
                        'value' => 0)

                )
            )
        );

        Mage::getModel('adminhtml/config_data')
            ->setSection('gomage_activation')
            ->setGroups($groups)
            ->save();

        Mage::getConfig()->reinit();
        Mage::app()->reinitStores();

    }

    public function ga()
    {
        return Zend_Json::decode(base64_decode(Mage::helper('core')->decrypt(Mage::getStoreConfig('gomage_activation/advancednavigation/ar'))));
    }

    public function isGomageNavigation()
    {
        if ($this->isMobileDevice() && Mage::getStoreConfigFlag('gomage_navigation/general/disable_mobile')) {
            return false;
        }
        return in_array(Mage::app()->getStore()->getWebsiteId(), $this->getAvailavelWebsites()) &&
        Mage::getStoreConfigFlag('gomage_navigation/general/mode');
    }

    public function isGomageNavigationAjax()
    {
        return $this->isGomageNavigation() &&
        Mage::getStoreConfigFlag('gomage_navigation/general/pager') &&
        (Mage::registry('current_category') ||
            (Mage::app()->getFrontController()->getRequest()->getRouteName() == 'catalogsearch' &&
                Mage::app()->getFrontController()->getRequest()->getControllerName() != 'advanced'));
    }

    public function isGomageNavigationClearAjax()
    {
        return $this->isGomageNavigation() &&
        (Mage::registry('current_category') ||
            (Mage::app()->getFrontController()->getRequest()->getRouteName() == 'catalogsearch' &&
                Mage::app()->getFrontController()->getRequest()->getControllerName() != 'advanced'));
    }

    public function formatColor($value)
    {
        if ($value = preg_replace('/[^a-zA-Z0-9\s]/', '', $value)) {
            $value = '#' . $value;
        }
        return $value;
    }

    public function isFrendlyUrl()
    {
        return $this->isGomageNavigation() &&
        Mage::getStoreConfigFlag('gomage_navigation/filter_settings/frendlyurl') &&
        !$this->isGoMage_SeoBoosterEnabled();
    }

    public function isGoMage_SeoBoosterEnabled()
    {
        $_modules      = Mage::getConfig()->getNode('modules')->children();
        $_modulesArray = (array)$_modules;
        if (!isset($_modulesArray['GoMage_SeoBooster'])) {
            return false;
        }
        return $_modulesArray['GoMage_SeoBooster']->is('active');
    }

    public function getFilterUrl($route = '', $params = array(), $filter = false)
    {
        if (!$this->isFrendlyUrl()) {
            $url = Mage::getUrl($route, $params);

            $arr = parse_url($url);

            $queryString = false;
            if (isset($arr['query'])) {
                parse_str(htmlspecialchars_decode($arr['query']), $par);

                if (isset($par['ajax'])) {
                    unset($par['ajax']);
                }

                $queryString = http_build_query($par);
            }

            $port = '';
            if (isset($arr['port']) && $arr['port'] !== '80') {
                $port = ':' . $arr['port'];
            }
            $url = $arr['scheme'] . '://' . $arr['host'] . $port . $arr['path'] . '?';

            if ($queryString) {
                $url .= $queryString;
            }

            return $url;
        }

        $model         = Mage::getModel('core/url');
        $request_query = $model->getRequest()->getQuery();
        $attr          = Mage::registry('gan_filter_attributes');

        foreach ($model->getRequest()->getQuery() as $param => $value) {

            $attributeModel = Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product', $param);

            if ($param == 'cat') {
                $values         = explode(',', $value);
                $prepare_values = array();
                foreach ($values as $_value) {
                    $category = Mage::getModel('catalog/category')->load($_value);
                    if ($category && $category->getId()) {
                        if (Mage::getStoreConfigFlag('gomage_navigation/filter_settings/expend_frendlyurl')) {
                            $parent_ids       = $category->getParentIds();
                            $parent_category  = Mage::getModel('catalog/category')->load(end($parent_ids));
                            $prepare_values[] = $parent_category->getData('url_key') . '|' . $category->getData('url_key');
                        } else {
                            $prepare_values[] = $category->getData('url_key');
                        }
                    }
                }
                $model->getRequest()->setQuery($param, implode(',', $prepare_values));
            } elseif (isset($attr[$param]) && !in_array($attr[$param]['type'], array('price', 'decimal'))) {
                $values         = explode(',', $value);
                $prepare_values = array();
                foreach ($values as $_value) {
                    foreach ($attr[$param]['options'] as $_k => $_v) {
                        if ($_v == $_value) {
                            $prepare_values[] = $_k;
                            break;
                        }
                    }
                }
                $model->getRequest()->setQuery($param, implode(',', $prepare_values));
            } else {
                if ($attributeModel->getFrontendInput() == $param) {
                    $attribute = Mage::getModel('catalog/resource_eav_attribute')->load($attributeModel->getId());
                    if (is_array($value)) {
                        if (isset($value['from'])) {
                            $params['_query'][$attribute->getAttributeCode() . '_from'] = $value['from'];
                        }
                        if (isset($value['to'])) {
                            $params['_query'][$attribute->getAttributeCode() . '_to'] = $value['to'];
                        }
                    } elseif (($attribute->getRangeOptions() == GoMage_Navigation_Model_Adminhtml_System_Config_Source_Filter_Optionsrange::MANUALLY
                            ||
                            $attribute->getRangeOptions() == GoMage_Navigation_Model_Adminhtml_System_Config_Source_Filter_Optionsrange::AUTO)
                        &&
                        $attribute->getFilterType() == GoMage_Navigation_Model_Layer::FILTER_TYPE_DEFAULT
                    ) {
                        $values = explode(',', $value);

                        $params['_query'][$attribute->getAttributeCode() . '_from'] = $values[0];
                        $params['_query'][$attribute->getAttributeCode() . '_to']   = $values[1];
                        unset($params['_query'][$attribute->getAttributeCode()]);
                    } else {
                        $values         = explode(',', $value);
                        $prepare_values = array();
                        foreach ($values as $_value) {
                            foreach ($attr[$param]['options'] as $_k => $_v) {
                                if ($_v == $_value) {
                                    $prepare_values[] = $_k;
                                    break;
                                }
                            }
                        }
                        $model->getRequest()->setQuery($param, implode(',', $prepare_values));
                    }
                }
            }
        }

        if (isset($params['_query'])) {
            foreach ($params['_query'] as $param => $value) {
                if ($value) {

                    $attributeModel = Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product', $param);

                    if ($param == 'cat') {
                        $values         = explode(',', $value);
                        $prepare_values = array();
                        foreach ($values as $_value) {
                            $category = Mage::getModel('catalog/category')->load($_value);
                            if ($category && $category->getId()) {
                                if (Mage::getStoreConfigFlag('gomage_navigation/filter_settings/expend_frendlyurl')) {
                                    $parent_ids       = $category->getParentIds();
                                    $parent_category  = Mage::getModel('catalog/category')->load(end($parent_ids));
                                    $prepare_values[] = $parent_category->getData('url_key') . '|' . $category->getData('url_key');
                                } else {
                                    $prepare_values[] = $category->getData('url_key');
                                }
                            }
                        }
                        $params['_query'][$param] = implode(',', $prepare_values);
                    } elseif (isset($attr[$param]) && !in_array($attr[$param]['type'], array('price', 'decimal'))) {
                        $values         = explode(',', $value);
                        $prepare_values = array();
                        foreach ($values as $_value) {
                            foreach ($attr[$param]['options'] as $_k => $_v) {
                                if ($_v == $_value) {
                                    $prepare_values[] = $_k;
                                    break;
                                }
                            }
                        }
                        $params['_query'][$param] = implode(',', $prepare_values);
                    } else {
                        if ($attributeModel->getFrontendInput() == $param) {
                            $attribute = Mage::getModel('catalog/resource_eav_attribute')->load($attributeModel->getId());
                            if (($attribute->getRangeOptions() == GoMage_Navigation_Model_Adminhtml_System_Config_Source_Filter_Optionsrange::MANUALLY
                                    ||
                                    $attribute->getRangeOptions() == GoMage_Navigation_Model_Adminhtml_System_Config_Source_Filter_Optionsrange::AUTO)
                                &&
                                $attribute->getFilterType() == GoMage_Navigation_Model_Layer::FILTER_TYPE_DEFAULT
                            ) {
                                if (strpos($value, ';')) {
                                    $values = explode(';', $value);
                                } else {
                                    $values = explode(',', $value);
                                }

                                $params['_query'][$attribute->getAttributeCode() . '_from'] = $values[0];
                                $params['_query'][$attribute->getAttributeCode() . '_to']   = $values[1];
                                unset($params['_query'][$attribute->getAttributeCode()]);
                            } else {
                                $values         = explode(',', $value);
                                $prepare_values = array();
                                foreach ($values as $_value) {
                                    foreach ($attr[$param]['options'] as $_k => $_v) {
                                        if ($_v == $_value) {
                                            $prepare_values[] = $_k;
                                            break;
                                        }
                                    }
                                }
                                $params['_query'][$param] = implode(',', $prepare_values);
                            }
                        }
                    }
                }
            }
        }

        $url = $model->getUrl($route, $params);

        $arr = parse_url($url);

        $queryString = false;
        if (isset($arr['query'])) {
            parse_str(htmlspecialchars_decode($arr['query']), $par);
            if (isset($par['ajax'])) {
                unset($par['ajax']);
            }

            $queryString = http_build_query($par);
        }

        $url = $arr['scheme'] . '://' . $arr['host'] . $arr['path'] . '?';

        if ($queryString) {
            $url .= $queryString;
        }

        foreach ($request_query as $param => $value) {
            $model->getRequest()->setQuery($param, $value);
        }

        return $url;
    }

    public function formatUrlValue($value, $default)
    {
        $oldLocale  = setlocale(LC_COLLATE, "0");
        $localeCode = Mage::app()->getLocale()->getLocaleCode();
        setlocale(LC_COLLATE, $localeCode . '.UTF8', 'C.UTF-8', 'en_US.utf8');
        $value = iconv(mb_detect_encoding($value), 'ASCII//TRANSLIT', $value);
        setlocale(LC_COLLATE, $oldLocale);

        $value = strtolower($value);
        $value = preg_replace('#[^0-9a-z]+#i', '-', Mage::helper('catalog/product_url')->format($value));
        $value = trim($value, '-');

        return $value ? $value : $default;
    }

    public function isMobileDevice()
    {
        $detect = new Mobile_Detect();
        return $detect->isMobile();
    }

    public function isGooglebot()
    {
        if (preg_match("/Google/", Mage::helper('core/http')->getHttpUserAgent()) || preg_match("/bot/", Mage::helper('core/http')->getHttpUserAgent())) {
            $ip   = Mage::helper('core/http')->getRemoteAddr();
            $name = gethostbyaddr($ip);
            if (preg_match("/Googlebot/", $name) || preg_match("/bot/", $name)) {
                $hosts = gethostbynamel($name);
                foreach ($hosts as $host) {
                    if ($host == $ip) {
                        return true;
                    }
                }
            }
        } else {
            return true;
        }

        return false;
    }

    public function getFilterItemCount($filter)
    {
        $count = 0;
        if ($filter && $filter->getItems()) {
            foreach ($filter->getItems() as $item) {
                $count += $item->getCount();
            }
        }

        if ($count == 0 && $filter->getFilter()->getRequestVarValue() == 'stock_status') {
            return 1;
        }

        return $count;
    }

    public function getFilter()
    {
        $filter = Mage::getStoreConfig('gomage_navigation/filter/filter_btn_txt');

        if ($filter == '') {
            $filter = $this->__('Filter');
        }

        return $filter;
    }

    public function getClearAll()
    {
        $clear = Mage::getStoreConfig('gomage_navigation/filter/clear_btn_txt');

        if ($clear == '') {
            $clear = $this->__('Clear All');
        }

        return $clear;
    }

    public function getMore()
    {
        $more = Mage::getStoreConfig('gomage_navigation/filter/more_btn_txt');

        if ($more == '') {
            $more = $this->__('More');
        }

        return $more;
    }

    public function getLess()
    {
        $less = Mage::getStoreConfig('gomage_navigation/filter/less_btn_txt');

        if ($less == '') {
            $less = $this->__('Less');
        }

        return $less;
    }

    public function getShowmore()
    {
        $showmore = Mage::getStoreConfig('gomage_navigation/filter/showmore_btn_txt');

        if ($showmore == '') {
            $showmore = $this->__('Show more products');
        }

        return $showmore;
    }

    public function getBacktotop()
    {
        $backtotop = Mage::getStoreConfig('gomage_navigation/filter/backtotop_btn_txt');

        if ($backtotop == '') {
            $backtotop = $this->__('Back to Top');
        }

        return $backtotop;
    }

    public function isEnterprise()
    {
        if (Mage::getConfig()->getModuleConfig('Enterprise_Enterprise') && Mage::getConfig()->getModuleConfig('Enterprise_AdminGws') && Mage::getConfig()->getModuleConfig('Enterprise_Checkout') && Mage::getConfig()->getModuleConfig('Enterprise_Customer')) {
            return true;
        }

        return false;
    }

    public function getSide($type)
    {
        switch ($type) {
            case GoMage_Navigation_Model_Adminhtml_System_Config_Source_Filter_Attributelocation::LEFT_BLOCK:
                return 'left';
                break;

            case GoMage_Navigation_Model_Adminhtml_System_Config_Source_Filter_Attributelocation::CONTENT:
                return 'content';
                break;

            case GoMage_Navigation_Model_Adminhtml_System_Config_Source_Filter_Attributelocation::RIGHT_BLOCK:
                return 'right';
                break;

            default:
                return 'left';
                break;
        }
    }

    public function getClearLinkUrl($_filter)
    {
        if ($_filter->getFilter()->getRequestVar() != 'cat' && $_filter->getFilter()->getRequestVar() != 'stock_status') {
            if ($_filter->getFilter()->getAttributeModel()->getFrontendInput()) {
                $attribute = $_filter->getFilter()->getAttributeModel();

                if ((in_array($attribute->getFilterType(), array(GoMage_Navigation_Model_Layer::FILTER_TYPE_SLIDER,
                                GoMage_Navigation_Model_Layer::FILTER_TYPE_SLIDER_INPUT,
                                GoMage_Navigation_Model_Layer::FILTER_TYPE_INPUT_SLIDER)
                        ) && !Mage::helper('gomage_navigation')->isMobileDevice())
                    ||
                    ($attribute->getFilterType() == GoMage_Navigation_Model_Layer::FILTER_TYPE_DEFAULT
                        &&
                        $attribute->getRangeOptions() != GoMage_Navigation_Model_Adminhtml_System_Config_Source_Filter_Optionsrange::NO)
                ) {
                    $params                 = array();
                    $params['_nosid']       = true;
                    $params['_current']     = true;
                    $params['_use_rewrite'] = true;
                    $params['_escape']      = false;

                    $url = $this->getFilterUrl('*/*/*', $params);

                    $clean_url = $this->getFilterUrl('*/*/*', array('_current' => true, '_nosid' => true, '_use_rewrite' => true, '_query' => array(), '_escape' => false));

                    if (strpos($clean_url, "?") !== false) {
                        $clean_url = substr($clean_url, 0, strpos($clean_url, '?'));
                    }

                    $params = str_replace($clean_url, "", $url);
                    $params = str_replace("?", "", $params);

                    $parArray    = explode("&", $params);
                    $newParArray = array();


                    foreach ($parArray as $par) {
                        $expar = explode("=", $par);
                        if ($expar[0] != $attribute->getAttributeCode() . '_from'
                            &&
                            $expar[0] != $attribute->getAttributeCode() . '_to'
                        ) {
                            $newParArray[] = $par;
                        }
                    }

                    if ($newParArray) {
                        if ($_filter->getAjaxEnabled()) {
                            return $clean_url . '?' . implode("&", $newParArray) . '&ajax=1';
                        } else {
                            return $clean_url . '?' . implode("&", $newParArray);
                        }
                    } else {
                        if ($_filter->getAjaxEnabled()) {
                            return $clean_url . '?ajax=1';
                        } else {
                            return $clean_url;
                        }
                    }
                }
            } else {
                return $_filter->getClearLinkUrl();
            }
        } else {
            return $_filter->getClearLinkUrl();
        }
    }

    public function notify()
    {
        $frequency = intval(Mage::app()->loadCache('gomage_notifications_frequency'));
        if (!$frequency) {
            $frequency = 24;
        }
        $last_update = intval(Mage::app()->loadCache('gomage_notifications_last_update'));

        if (($frequency * 60 * 60 + $last_update) > time()) {
            return false;
        }

        $timestamp = $last_update;
        if (!$timestamp) {
            $timestamp = time();
        }

        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, sprintf('https://www.gomage.com/index.php/gomage_notification/index/data'));
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, 'sku=advanced-navigation&timestamp=' . $timestamp . '&ver=' . urlencode('4.2'));
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

            $content = curl_exec($ch);

            $result = Zend_Json::decode($content);

            if ($result && isset($result['frequency']) && ($result['frequency'] != $frequency)) {
                Mage::app()->saveCache($result['frequency'], 'gomage_notifications_frequency');
            }

            if ($result && isset($result['data'])) {
                if (!empty($result['data'])) {
                    Mage::getModel('adminnotification/inbox')->parse($result['data']);
                }
            }
        } catch (Exception $e) {
        }

        Mage::app()->saveCache(time(), 'gomage_notifications_last_update');

    }

    public function getIsAnymoreVersion($major, $minor, $revision = 0)
    {
        $version_info = Mage::getVersion();
        $version_info = explode('.', $version_info);

        if ($version_info[0] > $major) {
            return true;
        } elseif ($version_info[0] == $major) {
            if ($version_info[1] > $minor) {
                return true;
            } elseif ($version_info[1] == $minor) {
                if ($version_info[2] >= $revision) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

}

