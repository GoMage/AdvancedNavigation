<?php
/**
 * GoMage Advanced Navigation Extension
 *
 * @category     Extension
 * @copyright    Copyright (c) 2010-2013 GoMage (http://www.gomage.com)
 * @author       GoMage
 * @license      http://www.gomage.com/license-agreement/  Single domain license
 * @terms of use http://www.gomage.com/terms-of-use
 * @version      Release: 4.0
 * @since        Class available since Release 1.0
 */

class GoMage_Navigation_Helper_Data extends Mage_Core_Helper_Abstract{


    public function getConfigData($node){
        return Mage::getStoreConfig('gomage_navigation/'.$node);
    }

    public function getAllStoreDomains(){

        $domains = array();

        foreach (Mage::app()->getWebsites() as $website) {

            $url = $website->getConfig('web/unsecure/base_url');

            if($domain = trim(preg_replace('/^.*?\\/\\/(.*)?\\//', '$1', $url))){

                $domains[] = $domain;

            }

            $url = $website->getConfig('web/secure/base_url');

            if($domain = trim(preg_replace('/^.*?\\/\\/(.*)?\\//', '$1', $url))){

                $domains[] = $domain;

            }

        }

        return array_unique($domains);


    }

    public function getAvailabelWebsites(){
        return $this->_w();
    }

    public function getAvailavelWebsites(){
        return $this->_w();
    }

    protected function _w(){

        if(!Mage::getStoreConfig('gomage_activation/advancednavigation/installed') ||
            (intval(Mage::getStoreConfig('gomage_activation/advancednavigation/count')) > 10))
        {
            return array();
        }

        $time_to_update = 60*60*24*15;

        $r = Mage::getStoreConfig('gomage_activation/advancednavigation/ar');
        $t = Mage::getStoreConfig('gomage_activation/advancednavigation/time');
        $s = Mage::getStoreConfig('gomage_activation/advancednavigation/websites');

        $last_check = str_replace($r, '', Mage::helper('core')->decrypt($t));

        $allsites = explode(',', str_replace($r, '', Mage::helper('core')->decrypt($s)));
        $allsites = array_diff($allsites, array(""));

        if(($last_check+$time_to_update) < time()){
            $this->a(Mage::getStoreConfig('gomage_activation/advancednavigation/key'),
                intval(Mage::getStoreConfig('gomage_activation/advancednavigation/count')),
                implode(',', $allsites));
        }

        return $allsites;

    }

    public function a($k, $c = 0, $s = ''){

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, sprintf('https://www.gomage.com/index.php/gomage_downloadable/key/check'));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'key='.urlencode($k).'&sku=advanced-navigation&domains='.urlencode(implode(',', $this->getAllStoreDomains())).'&ver='.urlencode('4.1'));
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

        $content = curl_exec($ch);

        $r	= Zend_Json::decode($content);
        $e = Mage::helper('core');
        if(empty($r)){

            $value1 = Mage::getStoreConfig('gomage_activation/advancednavigation/ar');

            $groups = array(
                'advancednavigation'=>array(
                    'fields'=>array(
                        'ar'=>array(
                            'value'=>$value1
                        ),
                        'websites'=>array(
                            'value'=>(string)Mage::getStoreConfig('gomage_activation/advancednavigation/websites')
                        ),
                        'time'=>array(
                            'value'=>(string)$e->encrypt($value1.(time()-(60*60*24*15-1800)).$value1)
                        ),
                        'count'=>array(
                            'value'=>$c+1)
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



        if(isset($r['d']) && isset($r['c'])){
            $value1 = $e->encrypt(base64_encode(Zend_Json::encode($r)));


            if (!$s) $s = Mage::getStoreConfig('gomage_activation/advancednavigation/websites');

            $s = array_slice(explode(',', $s), 0, $r['c']);

            $value2 = $e->encrypt($value1.implode(',', $s).$value1);

        }
        $groups = array(
            'advancednavigation'=>array(
                'fields'=>array(
                    'ar'=>array(
                        'value'=>$value1
                    ),
                    'websites'=>array(
                        'value'=>(string)$value2
                    ),
                    'time'=>array(
                        'value'=>(string)$e->encrypt($value1.time().$value1)
                    ),
                    'installed'=>array(
                        'value'=>1
                    ),
                    'count'=>array(
                        'value'=>0)

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

    public function ga(){
        return Zend_Json::decode(base64_decode(Mage::helper('core')->decrypt(Mage::getStoreConfig('gomage_activation/advancednavigation/ar'))));
    }

    public function isGomageNavigation(){
        if ($this->isMobileDevice() && Mage::getStoreConfigFlag('gomage_navigation/general/disable_mobile')){
            return false;
        }
        return in_array(Mage::app()->getStore()->getWebsiteId(), $this->getAvailavelWebsites()) &&
        Mage::getStoreConfigFlag('gomage_navigation/general/mode');
    }

    public function isGomageNavigationAjax(){

        return $this->isGomageNavigation() &&
        Mage::getStoreConfigFlag('gomage_navigation/general/pager')
        &&
        (Mage::registry('current_category') ||
            (Mage::app()->getFrontController()->getRequest()->getRouteName() == 'catalogsearch' &&
                Mage::app()->getFrontController()->getRequest()->getControllerName() != 'advanced'));
    }

    public function isGomageNavigationClearAjax(){

        return $this->isGomageNavigation()&&
        (Mage::registry('current_category') ||
            (Mage::app()->getFrontController()->getRequest()->getRouteName() == 'catalogsearch' &&
                Mage::app()->getFrontController()->getRequest()->getControllerName() != 'advanced'));
    }

    public function formatColor($value){
        if ($value = preg_replace('/[^a-zA-Z0-9\s]/', '', $value)){
            $value = '#' . $value;
        }
        return $value;
    }

    public function isFrendlyUrl(){
        return $this->isGomageNavigation() && Mage::getStoreConfigFlag('gomage_navigation/filter_settings/frendlyurl');
    }

    public function getFilterUrl($route = '', $params = array(), $filter = false){

        if (!$this->isFrendlyUrl()){
            $url =  Mage::getUrl($route, $params);

            $arr = parse_url($url);
            $pars = explode('&amp;',$arr['query']);

            $newParams = array();
            foreach( $pars as $_param)
            {
                $_param = str_replace("ajax=1&", "", $_param);
                $_param = str_replace("ajax=1", "", $_param);

                if ( $_param != '' )
                {
                    $newParams[] = $_param;
                }

            }
            $url = $arr['scheme'] . '://' . $arr['host'] . $arr['path'] . '?';

            if ( $newParams )
            {
                $arr['query'] = implode('&amp;', $newParams);
                $url .= $arr['query'];
            }

            return $url;
        }

        $model = Mage::getModel('core/url');
        $request_query = $model->getRequest()->getQuery();
        $attr = Mage::registry('gan_filter_attributes');



        foreach($model->getRequest()->getQuery() as $param => $value){
            if ($param == 'cat'){
                $values = explode(',', $value);
                $prepare_values = array();
                foreach($values as $_value){
                    $category = Mage::getModel('catalog/category')->load($_value);
                    if ($category && $category->getId()){
                        $prepare_values[] = $category->getData('url_key');
                    }
                }
                $model->getRequest()->setQuery($param, implode(',', $prepare_values));
            }elseif (isset($attr[$param]) && !in_array($attr[$param]['type'], array('price', 'decimal'))){
                $values = explode(',', $value);
                $prepare_values = array();
                foreach($values as $_value){
                    foreach($attr[$param]['options'] as $_k => $_v){
                        if ($_v == $_value){
                            $prepare_values[] = $_k;
                            break;
                        }
                    }
                }
                $model->getRequest()->setQuery($param, implode(',', $prepare_values));
            }
            else if ( $param == 'price' && $filter != 'Price')
            {
                $attributeId = Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product','price');
                $attribute = Mage::getModel('catalog/resource_eav_attribute')->load($attributeId);

                if ( ($attribute->getRangeOptions() == GoMage_Navigation_Model_Adminhtml_System_Config_Source_Filter_Optionsrange::MANUALLY
                        ||
                        $attribute->getRangeOptions() == GoMage_Navigation_Model_Adminhtml_System_Config_Source_Filter_Optionsrange::AUTO)
                    &&
                    $attribute->getFilterType() == GoMage_Navigation_Model_Layer::FILTER_TYPE_DEFAULT )
                {
                    $values = explode(',', $value);

                    $params['_query']['price_from'] = $values[0];
                    $params['_query']['price_to'] = $values[1];
                    unset($params['_query']['price']);
                }
            }

        }

        foreach ($params['_query'] as $param => $value){
            if ($value){
                if ($param == 'cat'){
                    $values = explode(',', $value);
                    $prepare_values = array();
                    foreach($values as $_value){
                        $category = Mage::getModel('catalog/category')->load($_value);
                        if ($category && $category->getId()){
                            $prepare_values[] = $category->getData('url_key');
                        }
                    }
                    $params['_query'][$param] = implode(',', $prepare_values);
                }elseif (isset($attr[$param]) && !in_array($attr[$param]['type'], array('price', 'decimal'))){
                    $values = explode(',', $value);
                    $prepare_values = array();
                    foreach($values as $_value){
                        foreach($attr[$param]['options'] as $_k => $_v){
                            if ($_v == $_value){
                                $prepare_values[] = $_k;
                                break;
                            }
                        }
                    }
                    $params['_query'][$param] = implode(',', $prepare_values);
                }
                else if ( $param == 'price' && $filter != 'Price' )
                {
                    $attributeId = Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product','price');
                    $attribute = Mage::getModel('catalog/resource_eav_attribute')->load($attributeId);

                    if ( ($attribute->getRangeOptions() == GoMage_Navigation_Model_Adminhtml_System_Config_Source_Filter_Optionsrange::MANUALLY
                            ||
                            $attribute->getRangeOptions() == GoMage_Navigation_Model_Adminhtml_System_Config_Source_Filter_Optionsrange::AUTO)
                        &&
                        $attribute->getFilterType() == GoMage_Navigation_Model_Layer::FILTER_TYPE_DEFAULT )
                    {
                        if (strpos($value, ';')){
                            $values = explode(';', $value);
                        }else {
                            $values = explode(',', $value);
                        }


                        $params['_query']['price_from'] = $values[0];
                        $params['_query']['price_to'] = $values[1];
                        unset($params['_query']['price']);
                    }
                }

            }
        }

        $url = $model->getUrl($route, $params);

        $arr = parse_url($url);
        $pars = explode('&amp;',$arr['query']);

        $newParams = array();
        foreach( $pars as $_param)
        {
            $_param = str_replace("ajax=1&", "", $_param);
            $_param = str_replace("ajax=1", "", $_param);

            if ( $_param != '' )
            {
                $newParams[] = $_param;
            }

        }
        $url = $arr['scheme'] . '://' . $arr['host'] . $arr['path'] . '?';

        if ( $newParams )
        {
            $arr['query'] = implode('&amp;', $newParams);
            $url .= $arr['query'];
        }


        foreach($request_query as $param => $value){
            $model->getRequest()->setQuery($param, $value);
        }

        return $url;

    }

    public function formatUrlValue($value){
        $value = preg_replace('#[^0-9a-z]+#i', '_', Mage::helper('catalog/product_url')->format($value));
        $value = strtolower($value);
        $value = trim($value, '-');

        return $value;
    }

    public function isMobileDevice(){
        $user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);
        if (!$user_agent || strpos($user_agent, 'ipad')) return false;

        $regex_match="/(nokia|iphone|android|motorola|^mot-|softbank|foma|docomo|kddi|up.browser|up.link|";
        $regex_match.="htc|dopod|blazer|netfront|helio|hosin|huawei|novarra|CoolPad|webos|techfaith|palmsource|";
        $regex_match.="blackberry|alcatel|amoi|ktouch|nexian|samsung|^sam-|s[cg]h|^lge|ericsson|philips|sagem|wellcom|bunjalloo|maui|";
        $regex_match.="symbian|smartphone|midp|wap|phone|windows ce|iemobile|^spice|^bird|^zte-|longcos|pantech|gionee|^sie-|portalmmm|";
        $regex_match.="jigs browser|hiptop|^ucweb|^benq|haier|^lct|operas*mobi|opera*mini|320x320|240x320|176x220";
        $regex_match.=")/i";
        return preg_match($regex_match, strtolower($user_agent));

    }

    public function IsGooglebot(){
        if(preg_match("/Google/",$_SERVER['HTTP_USER_AGENT']) || preg_match("/bot/",$_SERVER['HTTP_USER_AGENT'])){
            $ip = $_SERVER['REMOTE_ADDR'];
            $name = gethostbyaddr($ip);
            if(preg_match("/Googlebot/",$name) || preg_match("/bot/",$name)){
                $hosts = gethostbynamel($name);
                foreach($hosts as $host){
                    if ($host == $ip){
                        return true;
                    }
                }
                return false;
            }else{
                return false;
            }
        }else{
            return true;
        }
        return false;
    }

    public function getFilterItemCount($filter)
    {
        $count = 0;
        if ( $filter && $filter->getItems() )
        {
            foreach( $filter->getItems() as $item )
            {
                $count += $item->getCount();
            }
        }

        if ( $count == 0 && $filter->getName() == 'Stock' )
        {
            return 1;
        }

        return $count;
    }

    public function getFilter()
    {
        $filter = Mage::getStoreConfig('gomage_navigation/filter/filter_btn_txt');

        if ( $filter == '' )
        {
            $filter = $this->__('Filter');
        }

        return $filter;
    }

    public function getClearAll()
    {
        $clear = Mage::getStoreConfig('gomage_navigation/filter/clear_btn_txt');

        if ( $clear == '' )
        {
            $clear = $this->__('Clear All');
        }

        return $clear;
    }

    public function getMore()
    {
        $more = Mage::getStoreConfig('gomage_navigation/filter/more_btn_txt');

        if ( $more == '' )
        {
            $more = $this->__('More');
        }

        return $more;
    }

    public function getLess()
    {
        $less = Mage::getStoreConfig('gomage_navigation/filter/less_btn_txt');

        if ( $less == '' )
        {
            $less = $this->__('Less');
        }

        return $less;
    }

    public function getShowmore()
    {
        $showmore = Mage::getStoreConfig('gomage_navigation/filter/showmore_btn_txt');

        if ( $showmore == '' )
        {
            $showmore = $this->__('Show more products');
        }

        return $showmore;
    }

    public function getBacktotop()
    {
        $backtotop = Mage::getStoreConfig('gomage_navigation/filter/backtotop_btn_txt');

        if ( $backtotop == '' )
        {
            $backtotop = $this->__('Back to Top');
        }

        return $backtotop;
    }

    public function isEnterprise()
    {
        try{
            $enterprise = Mage::getModel('enterprise_enterprise/observer');
        } catch (Exception $e){
            $enterprise = false;
        }
        return (bool)$enterprise;
    }

    public function getSide($type)
    {
        switch ($type){
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

    public function getClearLinkUrl($_filter){


        if ( $_filter->getName() == 'Price' || $_filter->getName() == 'Special Price')
        {
            if ( $_filter->getName() == 'Price' )
            {
                $code = 'price';
            }
            else
            {
                $code = 'special_price';
            }

            $attributeId = Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product',$code);
            $attribute = Mage::getModel('catalog/resource_eav_attribute')->load($attributeId);

            if ( in_array($attribute->getFilterType(), array( GoMage_Navigation_Model_Layer::FILTER_TYPE_SLIDER,
                        GoMage_Navigation_Model_Layer::FILTER_TYPE_SLIDER_INPUT,
                        GoMage_Navigation_Model_Layer::FILTER_TYPE_INPUT_SLIDER))
                ||
                ($attribute->getFilterType() == GoMage_Navigation_Model_Layer::FILTER_TYPE_DEFAULT
                    &&
                    $attribute->getRangeOptions() != GoMage_Navigation_Model_Adminhtml_System_Config_Source_Filter_Optionsrange::NO))
            {

                $params = array();
                $params['_nosid']       = true;
                $params['_current']     = true;
                $params['_use_rewrite'] = true;
                $params['_escape']      = false;

                $url = Mage::helper('gomage_navigation')->getFilterUrl('*/*/*', $params);

                $clean_url = Mage::helper('gomage_navigation')->getFilterUrl('*/*/*', array('_current'=>true, '_nosid'=>true, '_use_rewrite'=>true, '_query'=>array(), '_escape'=>false));

                if ( strpos($clean_url, "?") !== false )
                {
                    $clean_url = substr($clean_url, 0, strpos($clean_url, '?'));
                }

                $params = str_replace($clean_url, "", $url);


                $params = str_replace("?", "", $params);

                $parArray = explode("&", $params);
                $newParArray = array();

                foreach( $parArray as $par )
                {
                    $expar = explode("=", $par);
                    if ( $expar[0] != $code . '_from'
                        &&
                        $expar[0] != $code . '_to' )
                    {
                        $newParArray[] = $par;
                    }
                }

                if ( $newParArray )
                {
                    if ( $_filter->getAjaxEnabled() )
                    {
                        return $clean_url . '?' . implode("&", $newParArray) . '&ajax=1';
                    }
                    else
                    {
                        return $clean_url . '?' . implode("&", $newParArray);
                    }
                }
                else
                {
                    if ( $_filter->getAjaxEnabled() )
                    {
                        return $clean_url . '?ajax=1';
                    }
                    else
                    {
                        return $clean_url;
                    }
                }
            }
            else
            {
                return $_filter->getClearLinkUrl();
            }
        }
        else
        {
            return $_filter->getClearLinkUrl();
        }
    }

    public function notify(){

        $frequency = intval(Mage::app()->loadCache('gomage_notifications_frequency'));
        if (!$frequency){
            $frequency = 24;
        }
        $last_update = intval(Mage::app()->loadCache('gomage_notifications_last_update'));

        if (($frequency * 60 * 60 + $last_update) > time()) {
            return false;
        }

        $timestamp = $last_update;
        if (!$timestamp){
            $timestamp = time();
        }

        try{
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, sprintf('https://www.gomage.com/index.php/gomage_notification/index/data'));
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, 'sku=advanced-navigation&timestamp='.$timestamp.'&ver='.urlencode('4.1'));
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

            $content = curl_exec($ch);

            $result	= Zend_Json::decode($content);

            if ($result && isset($result['frequency']) && ($result['frequency'] != $frequency)){
                Mage::app()->saveCache($result['frequency'], 'gomage_notifications_frequency');
            }

            if ($result && isset($result['data'])){
                if (!empty($result['data'])){
                    Mage::getModel('adminnotification/inbox')->parse($result['data']);
                }
            }
        } catch (Exception $e){}

        Mage::app()->saveCache(time(), 'gomage_notifications_last_update');

    }

    public function getIsAnymoreVersion($major, $minor, $revision = 0)
    {
        $version_info = Mage::getVersion();
        $version_info = explode('.', $version_info);

        if ($version_info[0] > $major)
        {
            return true;
        }
        elseif ($version_info[0] == $major)
        {
            if ($version_info[1] > $minor)
            {
                return true;
            }
            elseif ($version_info[1] == $minor)
            {
                if ($version_info[2] >= $revision)
                {
                    return true;
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }
    

}

