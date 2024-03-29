 /**
 * GoMage Advanced Navigation Extension
 *
 * @category     Extension
 * @copyright    Copyright (c) 2010-2016 GoMage (https://www.gomage.com)
 * @author       GoMage
 * @license      https://www.gomage.com/license-agreement/  Single domain license
 * @terms of use https://www.gomage.com/terms-of-use
 * @version      Release: 4.9.3
 * @since        Available since Release 2.0
 */

Validation.add('gomage-validate-number',
				'Please use only numbers (0-9) in this field.', function(v) {
					return Validation.get('IsEmpty').test(v)
							|| (!isNaN(parseNumber(v)) && !/^\s+$/
									.test(parseNumber(v)));
				});

function Gomage_Navigation_fireEvent(element,event){
    if (document.createEventObject){
	    // dispatch for IE
	    var evt = document.createEventObject();
	    return element.fireEvent('on'+event,evt);
    }
    else{
	    // dispatch for firefox + others
	    var evt = document.createEvent("HTMLEvents");
	    evt.initEvent(event, true, true ); // event type,bubbling,cancelable
	    return !element.dispatchEvent(evt);
    }
}