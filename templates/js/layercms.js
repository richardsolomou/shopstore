// Creates a namespace object with the variable name if one doesn't already exist.
var layercms = layercms || {};

// A function that establishes some variables which point to internal functions
// that eventually return data to the user's screen.
layercms.webscrp = (function() {

	// Strict Mode is a new feature in ECMAScript 5 that allows placing functions
	// in a 'strict' operating context. This strict context prevents certain actions
	// from being taken and throws more exceptions.  It prevents, or throws errors,
	// when relatively 'unsafe' actions are taken (such as gaining access to the global object)
	// and disables features that are confusing or poorly thought out.
	'use strict';

	// Declares the variables that are to be used in this main function.
	var	toggleShow,
		toggleHide,
		toggleShowHide,
		loader,
		getFormValue,
		doAdd,
		doEdit,
		getAddForm,
		getEditForm,
		getAdministratorParams,
		getCategoryParams,
		getCurrencyParams,
		getCustomerParams,
		getProductParams,
		getReviewParams,
		getStoreParams,
		doDelete,
		loaded;

	// Gets an element by ID and adds the 'displayBlock' class to it, making it visible.
	toggleShow = function (div) { document.getElementById(div).classList.add('displayBlock'); };

	// Gets an element by ID and removes the 'displayBlock' class from it, making it invisible.
	toggleHide = function (div) { document.getElementById(div).classList.remove('displayBlock'); };

	// Gets an element by ID and toggles the 'displayBlock' class on it, making it visible
	// if the class is currently invisible, or invisible if the class is already visible.
	toggleShowHide = function (div) { document.getElementById(div).classList.toggle('displayBlock'); };

	// This is a universal function for AJAX XMLHttpRequest which gets the method to be used
	// to call a page (GET/POST/DELETE), the URI to be called, some optional parameters to be
	// sent with it, and the target div where the the response text will be displayed.
	loader = function (method, url, params, target) {

		var xhr;

		if (typeof XMLHttpRequest !== 'undefined') {
			xhr = new XMLHttpRequest();
		} else {
			var versions = ['MSXML2.XmlHttp.5.0',   
							'MSXML2.XmlHttp.4.0',  
							'MSXML2.XmlHttp.3.0',   
							'MSXML2.XmlHttp.2.0',  
							'Microsoft.XmlHttp'];
			for(var i = 0, len = versions.length; i < len; i++) {
				try {
					xhr = new ActiveXObject(versions[i]);
					break;
				} catch(e){}
			}
		}

		xhr.onreadystatechange = function () {
			if (xhr.readyState == 0 || xhr.readyState == 1 || xhr.readyState == 2 || xhr.readyState == 3) {
				document.getElementById(target).classList.add('loading');
			} else if (xhr.readyState == 4) {
				if (xhr.status == 200) {
					document.getElementById(target).classList.remove('loading');
					document.getElementById(target).innerHTML = xhr.responseText;
				}
			}
		}
		
		xhr.open(method, url, true);

		if (method == 'POST') {
			xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
		}

		xhr.send(params);
		
		window.console.log('loader: ' + method + '\r\n' + url + '\r\n' + params + '\r\n' + target + '\r\n' + xhr.responseText);
		
		toggleShow(target);

	};

	getFormValue = function (form, name) {
		if (form && form[name] && form[name].value) return "&" + name + "=" + form[name].value;
		return "";
	};

	// Creates and assigns the variables required to be sent to the loader function and gets all
	// the input fields from the form page and assigns them to variables to be sent as parameters.
	doAdd = function (obj, formID) {

		var method = 'POST';
		var url = 'insert/';
		var params;
		var target = 'operationAlert';

		var form = document.getElementById(formID);
		
		switch (obj) {
			case 'administrators':
				break;
			case 'categories':
				params = getFormValue(form, 'category_name') + getFormValue(form, 'category_parent_ID');
				break;
			case 'currencies':
				params = getFormValue(form, 'currency_name') + getFormValue(form, 'currency_code') + getFormValue(form, 'currency_symbol');
				break;
			case 'customers':
				params = getFormValue(form, 'customer_username') + getFormValue(form, 'customer_password') + getFormValue(form, 'customer_firstname') + getFormValue(form, 'customer_lastname') + getFormValue(form, 'customer_address1') + getFormValue(form, 'customer_address2') + getFormValue(form, 'customer_postcode') + getFormValue(form, 'customer_phone') + getFormValue(form, 'customer_email');
				break;
			case 'products':
				params = getFormValue(form, 'category_ID') + getFormValue(form, 'product_name') + getFormValue(form, 'product_description') + getFormValue(form, 'product_condition') + getFormValue(form, 'product_price') + getFormValue(form, 'product_stock') + getFormValue(form, 'product_image');
				break;
			case 'reviews':
				params = getFormValue(form, 'product_ID') + getFormValue(form, 'review_subject') + getFormValue(form, 'review_description') + getFormValue(form, 'review_rating') + getFormValue(form, 'customer_ID')
				break;
			case 'settings':
				params = getFormValue(form, 'setting_column') + getFormValue(form, 'setting_value');
			default:
				params = '';
		}

		params = 'operation=add' + params;

		window.console.log('doAdd: ' + method + '\r\n' + url + '\r\n' + params + '\r\n' + target);

		loader(method, url, params, target);

	};

	// Creates and assigns the variables required to be sent to the loader function and gets all
	// the input fields from the form page and assigns them to variables to be sent as parameters.
	doEdit = function (obj, id, formID) {

		var method = 'POST';
		var url = 'update/' + id + '/';
		var params;
		var target = 'operationAlert';
		
		var form = document.getElementById(formID);

		switch (obj) {
			case 'administrators':
				params = getFormValue(form, 'admin_ID') + getFormValue(form, 'admin_username') + getFormValue(form, 'admin_password') + getFormValue(form, 'admin_firstname') + getFormValue(form, 'admin_lastname') + getFormValue(form, 'admin_email');
				break;
			case 'categories':
				params = getFormValue(form, 'category_ID') + getFormValue(form, 'category_name') + getFormValue(form, 'category_parent_ID');
				break;
			case 'currencies':
				params = getFormValue(form, 'currency_ID') + getFormValue(form, 'currency_name') + getFormValue(form, 'currency_code') + getFormValue(form, 'currency_symbol');
				break;
			case 'customers':
				params = getFormValue(form, 'customer_ID') + getFormValue(form, 'customer_username') + getFormValue(form, 'customer_password') + getFormValue(form, 'customer_firstname') + getFormValue(form, 'customer_lastname') + getFormValue(form, 'customer_address1') + getFormValue(form, 'customer_address2') + getFormValue(form, 'customer_postcode') + getFormValue(form, 'customer_phone') + getFormValue(form, 'customer_email');
				break;
			case 'products':
				params = getFormValue(form, 'product_ID') + getFormValue(form, 'category_ID') + getFormValue(form, 'product_name') + getFormValue(form, 'product_description') + getFormValue(form, 'product_condition') + getFormValue(form, 'product_price') + getFormValue(form, 'product_stock') + getFormValue(form, 'product_image');
				break;
			case 'reviews':
				params = getFormValue(form, 'review_ID') + getFormValue(form, 'product_ID') + getFormValue(form, 'review_subject') + getFormValue(form, 'review_description') + getFormValue(form, 'review_rating') + getFormValue(form, 'customer_ID')
				break;
			case 'settings':
				params = getFormValue(form, 'setting_ID') + getFormValue(form, 'setting_column') + getFormValue(form, 'setting_value');
			default:
				params = '';
		}

		params = 'operation=edit' + params;

		window.console.log('doEdit: ' + method + '\r\n' + url + '\r\n' + params + '\r\n' + target);

		loader(method, url, params, target);

	};

	// Creates and assigns the variables required to be sent to the loader function in order to
	// use it to call the form to add an object.
	getAddForm = function () {

		var method = 'GET'
		var url = 'insert/';
		var params = '';
		var target = 'message';

		if (document.getElementById(target).classList.contains('displayBlock')) {
			toggleHide(target);
		} else {
			loader(method, url, params, target);
		}

	};

	// Creates and assigns the variables required to be sent to the loader function in order to
	// use it to call the form to edit an object.
	getEditForm = function (id) {

		var method = 'GET';
		var url = 'update/' + id;
		var params = '';
		var target = 'edit_' + id;

		if (document.getElementById(target).classList.contains('displayBlock')) {
			toggleHide(target);
		} else {
			loader(method, url, params, target);
		}

	};

	// Creates and assigns the variables required to be sent to the loader function in order to
	// use it to delete an object.
	doDelete = function (id) {

		var method = 'DELETE';
		var url = 'delete/' + id;
		var params = '';
		var target = 'operationAlert';

		loader(method, url, params, target);

	};

	// Function to be called when the document is loaded on every page.
	loaded = function () {};

	// Returns the respective function to the objects listed below.
	return {
		'toggleShow': toggleShow,
		'toggleHide': toggleHide,
		'toggleShowHide': toggleShowHide,
		'doAdd': doAdd,
		'doEdit': doEdit,
		'getAddForm': getAddForm,
		'getEditForm': getEditForm,
		'doDelete': doDelete,
		'loaded': loaded
	};

// The () right before the end of the function makes sure that this function is called
// as soon as the page is loaded and returns the value that will then be stored in the
// main function.
}());

// Creates an event listener for when the page has finished loading and calls the function
// stated below as soon as this has happened.
window.addEventListener('load', layercms.webscrp.loaded);