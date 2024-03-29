/**
 * GoMage Advanced Navigation Extension
 *
 * @category     Extension
 * @copyright    Copyright (c) 2010-2016 GoMage (https://www.gomage.com)
 * @author       GoMage
 * @license      https://www.gomage.com/license-agreement/  Single domain license
 * @terms of use https://www.gomage.com/terms-of-use
 * @version      Release: 4.9.3
 * @since        Available since Release 4.0
 */

GomageNavigationClass = Class.create({
    loadimage: null,
    loadimagealign: null,
    back_to_top_action: null,
    gomage_navigation_loadinfo_text: null,
    gomage_navigation_urlhash: null,
    gan_page_id: 0,
    gan_static_navigation_url: null,
    gan_more_type_ajax: false,
    gan_shop_by_area: null,
    navigationLoadInProccess: false,
    navigationOpenFilters: {},
    navigation_eval_js: null,
    gan_slider_datas: new Array(),
    help_icon_open_type: null,
    p_count: 0,
    mouse: false,
    help: false,
    scrolling_speed: 0,
    gomage_seobooster_enabled: false,

    print_exceptions: false,

    initialize: function (data) {
        if (data) {
            if (typeof data.loadimage != 'undefined') {
                this.loadimage = data.loadimage;
            }

            if (typeof data.loadimagealign != 'undefined') {
                this.loadimagealign = data.loadimagealign;
            }

            if (typeof data.back_to_top_action != 'undefined') {
                this.back_to_top_action = data.back_to_top_action;
            }

            if (typeof data.gomage_navigation_loadinfo_text != 'undefined') {
                this.gomage_navigation_loadinfo_text = data.gomage_navigation_loadinfo_text;
            }

            if (typeof data.gomage_navigation_urlhash != 'undefined') {
                this.gomage_navigation_urlhash = data.gomage_navigation_urlhash;
            }

            if (typeof data.gan_page_id != 'undefined') {
                this.gan_page_id = data.gan_page_id;
            }

            if (typeof data.gan_static_navigation_url != 'undefined') {
                this.gan_static_navigation_url = data.gan_static_navigation_url;
            }
            if (typeof data.gan_more_type_ajax != 'undefined') {
                this.gan_more_type_ajax = data.gan_more_type_ajax;
            }

            if (typeof data.gan_shop_by_area != 'undefined') {
                this.gan_shop_by_area = data.gan_shop_by_area;
            }

            if (typeof data.help_icon_open_type != 'undefined') {
                this.help_icon_open_type = data.help_icon_open_type;
            }

            if (typeof data.scrolling_speed != 'undefined') {
                this.scrolling_speed = data.scrolling_speed;
            }

            if (typeof data.gomage_seobooster_enabled != 'undefined') {
                this.gomage_seobooster_enabled = data.gomage_seobooster_enabled;
            }
        }

        setTimeout("GomageNavigation.ganLoadForPlain()", 500);

        if ("onhashchange" in window) { /*event supported?*/
            window.onhashchange = function () {
                GomageNavigation.ganPrepareUrl();
            }
        } else { /*event not supported:*/
            var storedHash = window.location.hash;
            window.setInterval(
                function () {
                    if (window.location.hash != storedHash) {
                        GomageNavigation.ganPrepareUrl();
                    }
                },
                100
            );
        }
    },

    printE: function (message) {
        if (this.print_exceptions) console.log(message);
    },

    click: function (element) {
        if ($(element)) {
            if (this.gomage_seobooster_enabled){
                setLocation(element.href);
                return;
            }

            var url_clean = this.decode($(element).readAttribute('data-url'));
            var param = this.decode($(element).readAttribute('data-param'));
            var ajax_clean = $(element).readAttribute('data-ajax');

            var url = this.formUrl(url_clean, param, ajax_clean);

            if (ajax_clean == 1) {
                this.setNavigationUrl(url);
            } else {
                url = url.replace("&ajax=1", "");
                setLocation(url.replace("ajax=1", ""));
            }
        } else {
            return false;
        }
    },

    formUrl: function (url_clean, param, ajax) {
        var url = url_clean;

        if (ajax == 1) {
            if (param != '') {
                url = url_clean + param + '&ajax=1';
            }
        } else {
            url = url_clean + param;
        }

        return url;
    },

    getElementsByClassName: function (classname, node) {
        var a = [];
        var re = new RegExp('\\b' + classname + '\\b');
        var els = node.getElementsByTagName("*");

        for (var i = 0, j = els.length; i < j; i++) {
            if (re.test(els[i].className)) {
                a.push(els[i]);
            }
        }

        return a;
    },

    generateUrlWithParams: function (url, params) {
        var query = {},
            keys = url.split('?')[1].split('&'),
            key = '',
            glue = '',
            strQuery = '',
            i = -1;

        while (++i < keys.length) {
            key = keys[i].split('=');
            query[key[0]] = key[1];
        }

        for (attrname in query) {
            if (!params.hasOwnProperty(attrname)) params[attrname] = query[attrname];
        }

        params['ajax'] = 0;

        for (key in params) {
            strQuery += glue + key + '=' + params[key];
            glue = '&';
        }

        if (strQuery != '') {
            url = url.split('?')[0] + '?' + strQuery;
        }

        return url;
    },

    setHashUrl: function (url, params) {
        var hash = new Object();
        var hashes = url.slice(url.indexOf('?') + 1).split('&');
        var hash_str = '';
        var current_url = window.location.href;

        if (current_url.indexOf('#') > 0) {
            current_url = current_url.substr(0, current_url.indexOf('#'));
        }

        var current_url_params = current_url.slice(current_url.indexOf('?') + 1).split('&');

        hashes = hashes.filter(function (n) {
            return n && (current_url_params.indexOf(n) == -1);
        });

        for (var i = 0; i < hashes.length; i++) {
            vars = hashes[i].split('=');

            if (vars[0] != 'ajax' && vars[0] != 'q') {
                hash[vars[0]] = this.decode(vars[1]);
            }
        }

        if (params) {
            for (var key in params) {
                if (params.hasOwnProperty(key)) {
                    hash[key] = params[key];
                }
            }
        }

        for (var key in hash) {
            if (hash.hasOwnProperty(key)) {
                hash_str += key + '=' + hash[key] + '&';
            }
        }

        if (hash_str) {
            hash_str += 'gan_data=true';
        }

        window.location.hash = hash_str;
    },

    startLoadNavigationData: function (more_products) {
        if (this.navigationLoadInProccess) {
            return false;
        }

        this.navigationLoadInProccess = true;

        if (more_products && typeof(this.gan_more_type_ajax) != 'undefined') {
            var more_button = $('gan-more-button');
            var loadinfo = document.createElement('div');
            loadinfo.innerHTML = '<img src="' + this.loadimage + '" />' + this.gomage_navigation_loadinfo_text;
            loadinfo.id = "navigation_loadinfo_more";
            new Insertion.After(more_button, loadinfo);
            more_button.hide();

            return this.navigationLoadInProccess;
        }

        var overlay = $('advanced-navigation-overlay');

        if ($$('div.category-products').length > 0) {
            var element = $$('div.category-products')[0];
        } else if ($$('div.col-main p.note-msg').length > 0) {
            var element = $$('div.col-main p.note-msg')[0];
        } else if (typeof(this.gan_static_navigation_url) != 'undefined' && this.gan_static_navigation_url && $$('div.main').length > 0) {
            var element = $$('div.main')[0];
        } else {
            this.navigationLoadInProccess = false;

            return;
        }

        if (!overlay) {
            overlay = $(document.createElement('div'));
            overlay.id = 'advanced-navigation-overlay';
            document.body.appendChild(overlay);
        }

        var offsets = element.cumulativeOffset();
        overlay.setStyle({
            'top': offsets[1] + 'px',
            'left': offsets[0] + 'px',
            'width': element.offsetWidth + 'px',
            'height': element.offsetHeight + 'px',
            'position': 'absolute',
            'display': 'block',
            'zIndex': '2000' /*Value mast be more than any element from content have*/
        });

        var loadinfo = document.createElement('div');

        if (this.loadimagealign == 'bottom') {
            loadinfo.innerHTML = this.gomage_navigation_loadinfo_text + '<img src="' + this.loadimage + '" alt="" class="align-' + this.loadimagealign + '"/>';
        } else {
            loadinfo.innerHTML = '<img src="' + this.loadimage + '" alt="" class="align-' + this.loadimagealign + '"/>' + this.gomage_navigation_loadinfo_text;
        }

        loadinfo.id = "navigation_loadinfo";
        loadinfo.className = "gan-loadinfo";
        document.body.appendChild(loadinfo);

        return this.navigationLoadInProccess;
    },

    stopLoadNavigationData: function () {
        var overlay = $('advanced-navigation-overlay');

        if (overlay) {
            overlay.style.display = 'none';
        }

        if ($('navigation_loadinfo')) {
            document.body.removeChild($('navigation_loadinfo'));
        }

        if ($('navigation_loadinfo_more')) {
            $('navigation_loadinfo_more').remove();
        }

        this.ganInitScrollToTop();
        this.ganInitSliders();

        if ($('gan-more-button')) {
            $('gan-more-button').show();
        }

        if ($$('.pager') == '') {
            this.loopPage();
        }

        return this.navigationLoadInProccess = false;
    },

    submitNavigationForm: function (form, url, is_ajax) {
        url = this.decode(url);
        var last = url.charAt(url.length - 1);

        if (last == '?') {
            url = url.replace("?", "?ajax=1");
        } else {
            if (this.strpos(url, 'ajax=1') == false) {
                if (this.strpos(url, '?') == false) {
                    url = url + '?ajax=1';
                } else {
                    url = url + '&ajax=1';
                }
            }
        }

        var from = $(form).down('input.navigation-from');
        var to = $(form).down('input.navigation-to');
        var re = /^[0-9]*$/;

        if (!re.test($(from).value)) {
            alert('Please use only numbers (0-9) in this field.');

            return false;
        }

        if (!re.test($(to).value)) {
            alert('Please use only numbers (0-9) in this field.');

            return false;
        }

        if ((parseFloat($(from).value) > parseFloat($(to).value)) ||
            ((parseFloat($(from).value) == 0 && parseFloat($(to).value) == 0))) {
            alert('Filter range is invalid.');

            return false;
        }

        var form_values = {};
        form_values[$(from).name] = $(from).value;
        form_values[$(to).name] = $(to).value;
        is_ajax = typeof(is_ajax) != 'undefined' ? is_ajax : true;
        var url = url.replace(/&amp;/ig, '&');
        var elements = form.elements;
        var params = Object.clone(this.navigationOpenFilters);

        for (var i = 0; i < elements.length; i++) {
            element = elements[i];

            switch (element.nodeName) {
                case 'INPUT':
                case 'SELECT':
                case 'TEXTAREA':
                    if (element.value) {
                        params[element.name] = element.value;
                    }

                    break;
            }
        }

        if (!url) {
            url = form.action;
        }

        if (!is_ajax) {
            setLocation(this.generateUrlWithParams(url, params));
        } else if (this.startLoadNavigationData()) {
            var query = {},
                new_query = {},
                keys = url.split('?')[1].split('&'),
                key = '',
                glue = '',
                strQuery = '',
                i = -1;

            while (++i < keys.length) {
                key = keys[i].split('=');
                query[key[0]] = key[1];
            }

            for (attrname in query) {
                if (!form_values.hasOwnProperty(attrname))
                    new_query[attrname] = query[attrname];
            }

            for (key in new_query) {
                strQuery += glue + key + '=' + new_query[key];
                glue = '&';
            }

            if (strQuery != '') {
                url = url.split('?')[0] + '?' + strQuery;
            }

            if (this.gomage_navigation_urlhash && !this.gomage_seobooster_enabled) {
                this.setHashUrl(url, params);
            }

            var request = new Ajax.Request(url,
                {
                    method: 'GET',
                    parameters: params,
                    onSuccess: function (transport) {
                        var response = eval('(' + (transport.responseText || false) + ')');

                        if (GomageNavigation.gomage_seobooster_enabled && response.current_url) {
                            history.replaceState({}, document.title, response.current_url);
                        }

                        if ($('gan_tmp_shop_by')) {
                            $('gan_tmp_shop_by').remove();
                        }

                        if (response.navigation_shop_left != '') {
                            if ($('block-layered-nav-left')) {
                                GomageNavigation.replaceNavigationBlock('block-layered-nav-left', response.navigation_shop_left);
                            } else {
                                GomageNavigation.addNavigationBlock(response.navigation_shop_left, 'left');
                            }
                        }

                        if (response.navigation != '') {
                            if ($('block-layered-nav-content')) {
                                GomageNavigation.replaceNavigationBlock('block-layered-nav-content', response.navigation);
                            } else {
                                GomageNavigation.addNavigationBlock(response.navigation, 'content');
                            }
                        }

                        if (response.navigation_shop_right != '') {
                            if ($('block-layered-nav-right')) {
                                GomageNavigation.replaceNavigationBlock('block-layered-nav-right', response.navigation_shop_right);
                            } else {
                                GomageNavigation.addNavigationBlock(response.navigation_shop_right, 'right');
                            }
                        }

                        GomageNavigation.replaceNavigationBlock('gan-content-nav-main-container', response.navigation_content);
                        GomageNavigation.replaceNavigationBlock('gan-left-nav-main-container', response.navigation_left);
                        GomageNavigation.replaceNavigationBlock('gan-right-nav-main-container', response.navigation_right);

                        GomageNavigation.replaceProductsBlock(response);
                        GomageNavigation.ganReplaceMoreButton(response.navigation_more);

                        try {
                            if (response.eval_js) {
                                eval(response.eval_js);
                                GomageNavigation.ganInitSliders();
                                GomageNavigation.navigation_eval_js = response.eval_js;
                            }
                        } catch (e) {
                            GomageNavigation.printE(e.message);
                        }

                        try {
                            if (response.eval_js_procart) {
                                eval(response.eval_js_procart);
                            }
                        } catch (e) {
                            GomageNavigation.printE(e.message);
                        }

                        GomageNavigation.stopLoadNavigationData();

                    },
                    onFailure: function () {
                        GomageNavigation.stopLoadNavigationData();
                    }
                }
            );
        }
    },

    clearUrlAjax: function (url, more_products, need_scroll) {
        this.setNavigationUrl(url, more_products, need_scroll);
        if (url.indexOf('?') > 0) {
            url = url.substr(0, url.indexOf('?'));
        }

        window.history.pushState({}, document.title, url);
    },

    setNavigationUrl: function (url, more_products, need_scroll) {
        url = this.decode(url);
        var last = url.charAt(url.length - 1);

        if (last == '?') {
            url = url.replace("?", "?ajax=1");
        } else {
            if (this.strpos(url, 'ajax=1') == false) {
                if (this.strpos(url, '?') == false) {
                    url = url + '?ajax=1';
                } else {
                    url = url + '&ajax=1';
                }
            }
        }

        is_ajax = typeof(is_ajax) != 'undefined' ? is_ajax : true;
        var url = url.replace(/&amp;/ig, '&');

        if (this.gomage_navigation_urlhash && !this.gomage_seobooster_enabled) {
            this.setHashUrl(url);
        }

        var params = this.navigationOpenFilters;
        if (this.gan_page_id > 0) {
            this.navigationOpenFilters['gan_page_id'] = this.gan_page_id;
        }

        if (this.startLoadNavigationData(more_products)) {
            var request = new Ajax.Request(url,
                {
                    method: 'GET',
                    parameters: params,
                    onSuccess: function (transport) {
                        try {
                            var response = eval('(' + (transport.responseText || false) + ')');
                        } catch (e) {
                            GomageNavigation.printE(e.message);
                        }

                        if (GomageNavigation.gomage_seobooster_enabled && response.current_url) {
                            history.replaceState({}, document.title, response.current_url);
                        }

                        if ($('gan_tmp_shop_by')) {
                            $('gan_tmp_shop_by').remove();
                        }

                        if (more_products) {
                            GomageNavigation.ganAddMoreProducts(response);
                        } else {
                            if (response.navigation_shop_left != '') {
                                if ($('block-layered-nav-left')) {
                                    GomageNavigation.replaceNavigationBlock('block-layered-nav-left', response.navigation_shop_left);
                                } else {
                                    GomageNavigation.addNavigationBlock(response.navigation_shop_left, 'left');
                                }
                            }

                            if (response.navigation != '') {
                                if ($('block-layered-nav-content')) {
                                    GomageNavigation.replaceNavigationBlock('block-layered-nav-content', response.navigation);
                                } else {
                                    GomageNavigation.addNavigationBlock(response.navigation, 'content');
                                }
                            }

                            if (response.navigation_shop_right != '') {
                                if ($('block-layered-nav-right')) {
                                    GomageNavigation.replaceNavigationBlock('block-layered-nav-right', response.navigation_shop_right);
                                } else {
                                    GomageNavigation.addNavigationBlock(response.navigation_shop_right, 'right');
                                }
                            }

                            GomageNavigation.replaceNavigationBlock('gan-content-nav-main-container', response.navigation_content);
                            GomageNavigation.replaceNavigationBlock('gan-left-nav-main-container', response.navigation_left);
                            GomageNavigation.replaceNavigationBlock('gan-right-nav-main-container', response.navigation_right);

                            GomageNavigation.replaceProductsBlock(response, need_scroll);
                            GomageNavigation.ganReplaceMoreButton(response.navigation_more);
                        }

                        try {
                            if (response.eval_js) {
                                eval(response.eval_js);
                                GomageNavigation.ganInitSliders();
                                GomageNavigation.navigation_eval_js = response.eval_js;
                            }
                        } catch (e) {
                            GomageNavigation.printE(e.message);
                        }

                        try {
                            if (response.eval_js_procart) {
                                eval(response.eval_js_procart);
                            }
                        } catch (e) {
                            GomageNavigation.printE(e.message);
                        }

                        if (typeof(GomageNavigation.gan_static_navigation_url) != 'undefined' && GomageNavigation.gan_static_navigation_url) {
                            GomageNavigation.gan_static_navigation_url = undefined;
                        }

                        try {
                            $j(window).trigger('delayed-resize');
                        } catch (e) {
                            GomageNavigation.printE(e.message);
                        }

                        GomageNavigation.stopLoadNavigationData();
                    },
                    onFailure: function () {
                        setLocation(url);
                        /*trying redirect to url*/
                    }
                }
            );
        }
    },

    replaceProductsBlock: function (response, need_scroll) {
        var content = response.product_list;

        if (typeof(this.gan_static_navigation_url) != 'undefined' && this.gan_static_navigation_url) {
            if ($$('div.col-main').length > 0) {
                var col_main = $$('div.col-main')[0];
                col_main.innerHTML += '<div class="category-view">' + content + '</div>';
                this.runContentScripts(content);
            }
            return;
        }

        var replace_toolbar = false;

        if ($$('div.category-products').length > 0) {
            element = $$('div.category-products')[0];
            if (element.select('div.toolbar').length == 0) {
                replace_toolbar = true;
            }
        } else if ($$('div.col-main p.note-msg').length > 0) {
            element = $$('div.col-main p.note-msg')[0];
        } else {
            return;
        }

        if (content && content.toElement) {
            content = content.toElement();
        } else if (!Object.isElement(content)) {
            content = Object.toHTML(content);
            var tempElement = document.createElement('div');
            content.evalScripts.bind(content).defer();
            content = content.stripScripts();
            tempElement.innerHTML = content;
            el = this.getElementsByClassName('category-products', tempElement);

            if (el.length > 0) {
                content = el[0];
            } else {
                el = this.getElementsByClassName('note-msg', tempElement);

                if (el.length > 0) {
                    content = el[0];

                    if (this.gan_shop_by_area == 1) {
                        var shop_by_content = Object.toHTML(response.navigation);
                        shop_by_content.evalScripts.bind(shop_by_content).defer();
                        shop_by_content = shop_by_content.stripScripts();
                        var tempElement = document.createElement('div');
                        tempElement.innerHTML = shop_by_content;
                        var shop_by = this.getElementsByClassName('block-layered-nav', tempElement);

                        if (shop_by.length > 0) {
                            shop_by = shop_by[0];
                            shop_by.id = 'gan_tmp_shop_by';
                            new Insertion.Before(element, shop_by);
                        }
                    }
                } else {
                    return;
                }
            }
        }

        element.parentNode.replaceChild(content, element);

        if (replace_toolbar && $$('div.category-products').length > 0) {
            this.replaceToolbal(response.product_list);
        }

        if (typeof(need_scroll) != 'undefined' && need_scroll) {
            if ($$('div.category-products').length > 0) {
                var category_products = $$('div.category-products')[0];
                category_products.scrollTo();
            }
        }

        this.runContentScripts(response.product_list);

    },

    runContentScripts: function (content) {
        var js_scripts = content.extractScripts();
        for (var i = 0; i < js_scripts.length; i++) {
            if (typeof(js_scripts[i]) != 'undefined') {
                globalEval(js_scripts[i]);
            }
        }
        if (typeof(ProductMediaManager) != 'undefined') {
            ProductMediaManager.init();
        }
    },

    replaceToolbal: function (content) {
        var content_toolbar = Object.toHTML(content);
        content_toolbar.evalScripts.bind(content_toolbar).defer();
        content_toolbar = content_toolbar.stripScripts();
        var toolbars = $$('div.toolbar');

        for (var i = 0; i < toolbars.length; i++) {
            var tempElement = document.createElement('div');
            tempElement.innerHTML = content_toolbar;
            var toolbar = this.getElementsByClassName('toolbar', tempElement);

            if (toolbar.length > 0) {
                toolbar = toolbar[0];
                el = toolbars[i];
                el.parentNode.replaceChild(toolbar, el);
            }
        }
    },

    addNavigationBlock: function (content, side) {
        var block = (side == 'content') ? 'div.col-main' : 'div.col-' + side;

        if ($$(block).length > 0) {
            if ($('gan-' + side + '-nav-main-container')) {
                var content = Object.toHTML(content);
                content.evalScripts.bind(content).defer();
                content = content.stripScripts();
                var tempElement = document.createElement('div');
                tempElement.innerHTML = content;
                element = this.getElementsByClassName('block-layered-nav', tempElement);

                if (element.length > 0) {
                    element = element[0];
                    new Insertion.After($('gan-' + side + '-nav-main-container'), element);
                }
            } else {
                var col_content = $$(block)[0];
                col_content.innerHTML = content + col_content.innerHTML;
            }
        }

        var element = $$('div.block-layered-nav-' + side)[0];

        if (typeof(element) == 'undefined') {
            return;
        }

        if (content && content.toElement) {
            content = content.toElement();
        } else if (!Object.isElement(content)) {
            content = Object.toHTML(content);
            var tempElement = document.createElement('div');
            content.evalScripts.bind(content).defer();
            tempElement.innerHTML = content;
            content = tempElement;
        }

        element.parentNode.replaceChild(content, element);
    },

    replaceNavigationBlock: function (element, content) {
        var element = $(element);

        if (content && element) {
            if (content && content.toElement) {
                content = content.toElement();
            } else if (!Object.isElement(content)) {
                content = Object.toHTML(content);

                var tempElement = document.createElement('div');

                content.evalScripts.bind(content).defer();
                tempElement.innerHTML = content;
                content = tempElement;
            }

            element.parentNode.replaceChild(content, element);
        }
    },

    initSlider: function (code, min, max, curr_min, curr_max, url, is_ajax, steps) {

        if (min == max) {
            max++;
            if (curr_min == curr_max) {
                curr_max++;
            }

        }

        var handles = [code + '-handle-from', code + '-handle-to'];

        var init_hidden = false;
        if ($('advancednavigation-filter-content-' + code) && !$('advancednavigation-filter-content-' + code).visible()) {
            init_hidden = true;
            $('advancednavigation-filter-content-' + code).show();
        }

        if (steps.length != 0) {
            var params = {
                values: steps,
                axis: 'horizontal',
                alignY: 0,
                range: $R(min, max),
                sliderValue: [curr_min, curr_max],
                restricted: true,
                spans: [code + "-square_slider_span"]
            };
        }
        else {
            var params = {
                axis: 'horizontal',
                alignY: 0,
                range: $R(min, max),
                sliderValue: [curr_min, curr_max],
                restricted: true,
                spans: [code + "-square_slider_span"]
            };
        }

        var s1 = new Control.AdvancedNavigationSlider(handles, code + '-track', params);
        if (init_hidden) {
            $('advancednavigation-filter-content-' + code).hide();
        }
        s1.options.onChange = function (value) {

            if (isNaN(value[0]) || isNaN(value[1])) {
                return false;
            }

            var filter = code;

            if (GomageNavigation.strpos(code, '-left')) {
                filter = code.replace('-left', '');
            }
            else if (GomageNavigation.strpos(code, '-right')) {
                filter = code.replace('-right', '');
            }
            else if (GomageNavigation.strpos(code, '-content')) {
                filter = code.replace('-content', '');
            }

            $(code + '-filter-form').elements[filter + '_from'].value = parseInt(value[0]);
            $(code + '-filter-form').elements[filter + '_to'].value = parseInt(value[1]);

            if (min == value[0] && max == value[1]) {

                /*setNavigationUrl(url);*/
                GomageNavigation.submitNavigationForm($(code + '-filter-form'), url, is_ajax);

            } else {

                GomageNavigation.submitNavigationForm($(code + '-filter-form'), url, is_ajax);

            }

            var htmlvalue = parseInt(value[0]) + ' - ' + parseInt(value[1]);

            if (value[0] >= 0 && value[1] >= 0) {

                $(code + '-value-from').innerHTML = parseInt(1 * value[0]);
                $(code + '-value-to').innerHTML = parseInt(1 * value[1]);

            }

            $(code + '-value').innerHTML = htmlvalue;

        };

        s1.options.onSlide = function (value) {

            if (isNaN(value[0]) || isNaN(value[1])) {
                return false;
            }

            var htmlvalue = parseInt(value[0]) + ' - ' + parseInt(value[1]);


            if (value[0] >= 0 && value[1] >= 0) {
                $(code + '-value-from').innerHTML = parseInt(1 * value[0]);
                $(code + '-value-to').innerHTML = parseInt(1 * value[1]);
            }
            $(code + '-value').innerHTML = htmlvalue;
        };

        s1.draw = function (event) {
            var pointer = (event.touches ? [event.touches[0].clientX, event.touches[0].clientY] : [Event.pointerX(event), Event.pointerY(event)]);
            var offsets = Position.cumulativeOffset(this.track);
            pointer[0] -= this.offsetX + offsets[0];
            pointer[1] -= this.offsetY + offsets[1];
            this.event = event;

            var value = this.translateToValue(this.isVertical() ? pointer[1] : pointer[0]);

            if (isNaN(value)) return false;

            this.setValue(value);

            if (this.initialized && this.options.onSlide)
                this.options.onSlide(this.values.length > 1 ? this.values : this.value, this);
        };

        var slider_data = new Object();
        var htmlvalue = parseInt(s1.values[0]) + ' - ' + parseInt(s1.values[1]);


        slider_data.code = code;
        slider_data.from = parseInt(s1.values[0]);
        slider_data.to = parseInt(s1.values[1]);
        slider_data.htmlvalue = htmlvalue;

        this.gan_slider_datas.push(slider_data);
    },

    checkValue: function (form_id, min, max) {
        var form_min = $(form_id).down('input.navigation-from').value;
        var form_max = $(form_id).down('input.navigation-to').value;

        if (form_min < min) {
            $(form_id).down('input.navigation-from').value = min;
        }

        if (form_max > max) {
            $(form_id).down('input.navigation-to').value = max;
        }
    },

    strpos: function (haystack, needle, offset) {
        var i = (haystack).indexOf(needle, (offset || 0));
        return i === -1 ? false : i;
    },

    ganAddMoreProducts: function (response) {

        this.replaceToolbal(response.product_list);
        if ($$('div.category-products').length > 0) {

            var category_products = $$('div.category-products')[0];
            category_products.select('ul.products-grid').each(
                function (e) {
                    e.removeClassName("last");
                }
            );
            category_products.select('ol.products-list li.item').each(
                function (e) {
                    e.removeClassName("last");
                }
            );
        }

        var content = Object.toHTML(response.product_list);
        var tempElement = document.createElement('div');
        content.evalScripts.bind(content).defer();
        content = content.stripScripts();
        tempElement.innerHTML = content;

        var element = this.getElementsByClassName('category-products', tempElement);
        if (element.length > 0) {
            element = element[0];
            var more_button = $('gan-more-button');
            var container = more_button.up();

            var elements = this.getElementsByClassName('products-grid', element);
            elements.each(function (e) {
                container.insertBefore(e, more_button);
            });
            var elements = this.getElementsByClassName('products-list', element);
            elements.each(function (e) {
                $('products-list').innerHTML += e.innerHTML;
            });

        }

        this.ganReplaceMoreButton(response.navigation_more);
    },

    ganReplaceMoreButton: function (navigation_more) {
        var more_button = $('gan-more-button');
        var element = null;

        if (navigation_more) {
            var content = Object.toHTML(navigation_more);
            content.evalScripts.bind(content).defer();
            content = content.stripScripts();
            var tempElement = document.createElement('div');
            tempElement.innerHTML = content;
            element = this.getElementsByClassName('gan-more-button', tempElement);
            if (element.length > 0) {
                element = element[0];
            }
        }

        if (more_button) {
            if (element) {
                more_button.parentNode.replaceChild(element, more_button);
            } else {
                more_button.remove();
            }
        } else {
            if (element && $$('div.toolbar-bottom').length > 0 && $$('div.category-products').length > 0) {
                var category_products = $$('div.category-products')[0];
                if (category_products.select('div.toolbar').length == 0) {
                    category_products.appendChild(element);
                } else {
                    var toolbar_bottom = $$('div.toolbar-bottom')[0];
                    var container = toolbar_bottom.up();
                    container.insertBefore(element, toolbar_bottom);
                }
            }
        }
    },

    encode: function (string) {
        return escape(this._utf8_encode(string));
    },

    decode: function (string) {
        return this._utf8_decode(unescape(string));
    },

    _utf8_encode: function (string) {
        string = string.replace(/\r\n/g, "\n");
        var utftext = "";

        for (var n = 0; n < string.length; n++) {

            var c = string.charCodeAt(n);

            if (c < 128) {
                utftext += String.fromCharCode(c);
            }
            else if ((c > 127) && (c < 2048)) {
                utftext += String.fromCharCode((c >> 6) | 192);
                utftext += String.fromCharCode((c & 63) | 128);
            }
            else {
                utftext += String.fromCharCode((c >> 12) | 224);
                utftext += String.fromCharCode(((c >> 6) & 63) | 128);
                utftext += String.fromCharCode((c & 63) | 128);
            }

        }

        return utftext;
    },

    _utf8_decode: function (utftext) {
        var string = "";
        var i = 0;
        var c = c1 = c2 = 0;

        while (i < utftext.length) {

            c = utftext.charCodeAt(i);

            if (c < 128) {
                string += String.fromCharCode(c);
                i++;
            }
            else if ((c > 191) && (c < 224)) {
                c2 = utftext.charCodeAt(i + 1);
                string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
                i += 2;
            }
            else {
                c2 = utftext.charCodeAt(i + 1);
                c3 = utftext.charCodeAt(i + 2);
                string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
                i += 3;
            }

        }

        return string;
    },

    /*category-navigation*/

    ganLoadForPlain: function () {

        mainNav("gan_nav_left", {"show_delay": "100", "hide_delay": "100"});
        mainNav("gan_nav_top", {"show_delay": "100", "hide_delay": "100"});
        $$('div.gan-ob-noheight').each(function (ob) {
            try {
                var gan_plain = ob.up('div.gan-plain');
                if (gan_plain && gan_plain.getHeight()) {
                    var border = gan_plain.getStyle('border-width');
                    if (border) {
                        border = border.replace(/px/g, '');
                        border = parseInt(border);
                    } else {
                        border = 0;
                    }
                    ob.setStyle({height: gan_plain.getHeight() - border * 2 + 'px'});
                }
            } catch (e) {
            }
        });
        mainNav("gan_nav_right", {"show_delay": "100", "hide_delay": "100"});
        mainNav("gan_nav_content", {"show_delay": "100", "hide_delay": "100"});

        this.ganInitSliders();
        if (typeof(this.gomage_navigation_urlhash) != 'undefined' && this.gomage_navigation_urlhash) {
            this.ganPrepareUrl(true);
        }
        /*$(document.body).scrollTo();*/
        this.ganInitMoreButton();
        this.ganInitScrollToTop();

        if (typeof(this.gan_static_navigation_url) != 'undefined' && this.gan_static_navigation_url) {
            this.setNavigationUrl(this.gan_static_navigation_url);
        }
    },

    ganPrepareUrl: function (first) {
        var hash_str = window.location.hash;

        if (hash_str) {

            var url = window.location.href;
            url = url.replace(hash_str, '');

            var hashes = hash_str.slice(1).split('&');
            var vars = new Array();
            var hash = new Object();
            var gan_data = false;
            var hash_str = '';

            for (var i = 0; i < hashes.length; i++) {
                vars = hashes[i].split('=');

                if (vars[0] == 'gan_data') {
                    gan_data = true;
                    continue;
                }

                if (vars[0] != 'ajax' && vars[0] != 'gan_data' && vars[0] != 'q') {
                    hash[vars[0]] = vars[1];
                }
            }

            for (var key in hash) {
                if (hash.hasOwnProperty(key)) {
                    if (key == 'p') {
                        if (first == true && $$('.pager') == '') {
                            this.p_count = hash[key];
                            hash[key] = 1;
                        }
                    }
                    hash_str += key + '=' + hash[key] + '&';
                }
            }

            if (typeof(this.setNavigationUrl) == 'function' && gan_data && hash_str) {

                hash_str += 'ajax=1';
                if (url.indexOf('?') != -1) {
                    url = url + '&' + hash_str;
                } else {
                    url = url + '?' + hash_str;
                }

                this.setNavigationUrl(url);
            }
        }
    },

    getCurrentPage: function () {
        var hash_str = window.location.hash;

        if (hash_str) {
            var url = window.location.href;
            url = url.replace(hash_str, '');

            var hashes = hash_str.slice(1).split('&');
            var vars = new Array();
            var hash = new Object();
            var gan_data = false;
            var hash_str = '';

            for (var i = 0; i < hashes.length; i++) {
                vars = hashes[i].split('=');

                if (vars[0] == 'gan_data') {
                    gan_data = true;
                    continue;
                }

                if (vars[0] != 'ajax' && vars[0] != 'gan_data' && vars[0] != 'q') {
                    hash[vars[0]] = vars[1];
                }
            }

            for (var key in hash) {
                if (hash.hasOwnProperty(key)) {
                    if (key == 'p') {
                        return hash[key] - 0;
                    }
                }
            }
        }
    },

    loopPage: function () {
        if (this.getCurrentPage() < this.p_count) {
            $$('div.toolbar-bottom')[0].scrollTo();
        }
        else {
            this.p_count = 0;
        }
    },

    ganInitSliders: function () {

        for (var i = 0; i < this.gan_slider_datas.length; i++) {
            $(this.gan_slider_datas[i].code + '-value-from').innerHTML = this.gan_slider_datas[i].from;
            $(this.gan_slider_datas[i].code + '-value-to').innerHTML = this.gan_slider_datas[i].to;
            $(this.gan_slider_datas[i].code + '-value').innerHTML = this.gan_slider_datas[i].htmlvalue;
        }
        this.gan_slider_datas = new Array();
    },


    showNavigationNote: function (id, control, side) {

        var arr = $(control).cumulativeOffset();
        var nbl = [0, 0];
        var container = '';
        var diff = 0;

        if (side != 'cat_left' && side != 'cat_right' && side != 'cat_content') {
            container = 'narrow-by-list-' + side;
        } else {
            if (side == 'cat_left') {
                container = 'gan_nav_left';
                if (!$(container)) {
                    container = 'gan_nav_left_dropdown';
                }
            }
            else if (side == 'cat_right') {
                container = 'gan_nav_right';
                if (!$(container)) {
                    container = 'gan_nav_right_dropdown';
                }
            }
            else {
                container = 'gan_nav_content';
                if (!$(container)) {
                    container = 'gan_nav_content_dropdown';
                }
            }

            if ($(container) && $(container).up('.block-content') && $(container).up('.block-content').previous()) {
                var previous = $(container).up('.block-content').previous();
                if (previous.hasClassName('block-title')) {
                    previous.insert({top: $(id)});
                }
            }
        }

        nbl = $(container).cumulativeOffset();
        var layered_width = $(container).getWidth();
        var note_width = $(id).getWidth();
        var merge_width = layered_width - note_width;

        if ($('gan-block-content-' + side)) {
            var gbc = $('gan-block-content-' + side).cumulativeOffset();
            diff = parseInt(nbl[1]) - parseInt(gbc[1]);
        } else if ($(container) && $(container).up('.block-content') && $(container).up('.block-content').previous()) {
            var previous = $(container).up('.block-content').previous();
            if (previous.hasClassName('block-title')) {
                var gbc = previous.cumulativeOffset();
                diff = parseInt(nbl[1]) - parseInt(gbc[1]);
            }
        }

        if (parseInt(document.viewport.getWidth()) > 782) {
            $(id).style.left = (parseInt(arr[0]) - parseInt(nbl[0])) + 'px';
        }
        else {
            $(id).style.left = (parseInt(arr[0]) - parseInt(nbl[0]) - parseInt(layered_width) + parseInt(merge_width)) + 'px';
        }

        $(id).style.top = (parseInt(arr[1]) - parseInt(nbl[1])) + diff + 'px';
        $(id).style.display = 'block';

        this.help = true;
        return false;
    },

    hideNavigationNote: function (close, id) {

        if (!this.mouse || close) {
            $$('.filter-note-content').each(function (e) {

                if (id != false) {
                    $(id).style.display = 'none';
                }
                else {
                    e.style.display = 'none';
                }
            });
        }
    },

    mouseStatus: function (m) {
        this.mouse = m;
    },

    navigationOpenFilter: function (request_var) {

        if (this.help_icon_open_type == 'over') {
            this.help = false;
        }

        if (this.help == false) {
            var id = 'advancednavigation-filter-content-' + request_var;

            if ($(id).style.display == 'none' || $(id).hasClassName('no-display')) {

                $(id).style.display = 'block';

                if (this.navigation_eval_js) {
                    eval(this.navigation_eval_js);
                    this.ganInitSliders();
                }

                this.navigationOpenFilters[request_var + '_is_open'] = true;

                if ($(id + '-title')) {
                    $(id + '-title').removeClassName('filter-content-hide');
                    $(id + '-title').addClassName('filter-content-show');
                }
            } else {

                $(id).style.display = 'none';

                this.navigationOpenFilters[request_var + '_is_open'] = false;

                if ($(id + '-title')) {
                    $(id + '-title').removeClassName('filter-content-show');
                    $(id + '-title').addClassName('filter-content-hide');
                }
            }
        }

        this.help = false;

    },

    showAllNavigationAttribute: function (control, request_var) {
        $(control).up('ol').select('li:hidden').each(
            function (e) {
                e.show();
            }
        );
        $(control).up('li').hide();
        this.navigationOpenFilters[request_var + '_show_all'] = true;
    },

    hideNavigationAttribute: function (control, count, request_var) {
        var i = 0;
        $(control).up('ol').select('li').each(
            function (e) {
                i++;
                if (i > count) {
                    e.hide();
                }
                if (e.select('a.gan-attr-more').length > 0) {
                    e.show();
                }
            }
        );
        this.navigationOpenFilters[request_var + '_show_all'] = false;
    },

    ganShowAccordionItem: function (control) {
        $(control).up('ul.gan-accordion-list').select('li.accordion-active').each(
            function (e) {
                e.removeClassName("accordion-active");
            }
        );
        $(control).up('li.level-top').addClassName('accordion-active');
    },

    ganInitMoreButton: function () {
        var more_button = $('gan-more-button');

        if (more_button) {
            if ($$('div.toolbar-bottom').length > 0 && $$('div.category-products').length > 0) {
                var category_products = $$('div.category-products')[0];
                if (category_products.select('div.toolbar').length == 0) {
                    category_products.appendChild(more_button);
                } else {
                    var toolbar_bottom = $$('div.toolbar-bottom')[0];
                    var container = toolbar_bottom.up();
                    container.insertBefore(more_button, toolbar_bottom);
                }
            } else {
                more_button.remove();
            }
        }

        if (this.gan_more_type_ajax == true) {
            Event.observe(window, "scroll", function () {
                var more_button = $('gan-more-button');
                if (document.viewport) {
                    var top = document.viewport.getScrollOffsets().top;
                    var height = document.viewport.getHeight();
                    var document_height = Math.max(Math.max(document.body.scrollHeight, document.documentElement.scrollHeight), Math.max(document.body.offsetHeight, document.documentElement.offsetHeight), Math.max(document.body.clientHeight, document.documentElement.clientHeight));
                    if ((document_height - top) <= (3 * height)) {
                        if (more_button && more_button.visible() && more_button.select('button').length > 0) {
                            var onclick_str = more_button.select('button')[0].attributes["onclick"].nodeValue;
                            globalEval(onclick_str);
                        }
                    }
                }
            });
        }
    },

    ganInitScrollToTop: function () {

        if ($$('div.category-products').length > 0 && $('gan-totop-button') && !$('product-designer')) {

            var left = $$('div.category-products')[0].getDimensions().width + $$('div.category-products')[0].cumulativeOffset().left + 20;

            $('gan-totop-button').setStyle({'left': left + 'px'});

            Event.observe(window, "scroll", function () {

                var top = document.viewport.getScrollOffsets().top;
                if (top > (document.viewport.getHeight() * 0.8)) {
                    $('gan-totop-button').show();
                } else {
                    $('gan-totop-button').hide();
                }
            });
            Event.observe(window, "resize", function () {
                var left = $$('div.category-products')[0].getDimensions().width + $$('div.category-products')[0].cumulativeOffset().left + 20;
                $('gan-totop-button').setStyle({'left': left + 'px'});
            });
        }
    },

    ganScrollToTop: function () {

        var scroll_flag = false;
        if ((this.scrolling_speed - 0) > 0) {
            scroll_flag = true;
            var scroll_speed = (this.scrolling_speed - 0) / 1000;
        }

        if (this.back_to_top_action == 0) {
            var category_view = $$('body')[0];

            if (scroll_flag) {
                this.customScrollTo(category_view, {duration: scroll_speed});
            }
            else {
                category_view.scrollTo();
            }

        }
        else {
            if ($$('div.category-view').length > 0) {
                var category_view = $$('div.category-view')[0];

                if (scroll_flag) {
                    this.customScrollTo(category_view, {duration: scroll_speed});
                }
                else {
                    category_view.scrollTo();
                }

            } else if ($$('div.category-products').length > 0) {
                var category_products = $$('div.category-products')[0];

                if (scroll_flag) {
                    this.customScrollTo(category_products, {duration: scroll_speed});
                }
                else {
                    category_products.scrollTo();
                }
            }
        }
    },
    customScrollTo: function (element) {
        var options = arguments[1] || {},
            scrollOffsets = document.viewport.getScrollOffsets(),
            elementOffsets = $(element).cumulativeOffset();

        if (options.offset) elementOffsets[1] += options.offset;

        return new CustomEffect.Tween(null,
            scrollOffsets.top,
            elementOffsets[1],
            options,
            function (p) {
                scrollTo(scrollOffsets.left, p.round());
            }
        );
    },


    ganSHBlockContent: function (control) {
        if ($('gan-block-content-content')) {
            if ($('gan-block-content-content').hasClassName("gan-hidden")) {
                $('gan-block-content-content').removeClassName("gan-hidden");
                $('gan-block-content-content').show();
                $(control).innerHTML = 'Hide';
                this.navigationOpenFilters['gan_bcontent_hide'] = false;
            } else {
                $('gan-block-content-content').addClassName("gan-hidden");
                $('gan-block-content-content').hide();
                $(control).innerHTML = 'Show';
                this.navigationOpenFilters['gan_bcontent_hide'] = true;
            }
        }
    }
});

var globalEval = function globalEval(src) {
    if (window.execScript) {
        window.execScript(src);
        return;
    }
    var fn = function () {
        window.eval.call(window, src);
    };
    fn();
};

Event.observe(window, 'resize', function () {
    GomageNavigation.ganInitSliders();
});
