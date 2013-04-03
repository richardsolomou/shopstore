// Creates a namespace object with the variable name if one doesn't already exist.
var layercms = layercms || {};

// A function that establishes some variables which point to internal functions
// that eventually return data to the user's screen.
layercms.webscrp = (function() {

	// Strict Mode is a new feature in ECMAScript 5 that allows placing functions
	// in a "strict" operating context. This strict context prevents certain actions
	// from being taken and throws more exceptions.  It prevents, or throws errors,
	// when relatively "unsafe" actions are taken (such as gaining access to the global object)
	// and disables features that are confusing or poorly thought out.
	'use strict';

	// Declares the variables that are to be used in this main function.
	var	toggleShow,
		toggleHide,
		toggleShowHide,
		loader,
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
		refreshList,
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
			var versions = ["MSXML2.XmlHttp.5.0",   
							"MSXML2.XmlHttp.4.0",  
							"MSXML2.XmlHttp.3.0",   
							"MSXML2.XmlHttp.2.0",  
							"Microsoft.XmlHttp"];
			for(var i = 0, len = versions.length; i < len; i++) {
				try {
					xhr = new ActiveXObject(versions[i]);
					break;
				} catch(e){}
			}
		}

		xhr.onreadystatechange = function () {
			if (xhr.readyState == 0 || xhr.readyState == 1 || xhr.readyState == 2 || xhr.readyState == 3) {
				document.getElementById(target).innerHTML = "<img src='images/ajax-loader.gif'>";
			} else if (xhr.readyState == 4) {
				if (xhr.status == 200) {
					document.getElementById(target).innerHTML = xhr.responseText;
				}
			}
		}
		
		xhr.open(method, url, true);

		if (method == "POST") {
			xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		}

		xhr.send(params);
		
		window.console.log("loader: " + method + "\r\n" + url + "\r\n" + params + "\r\n" + target + "\r\n" + xhr.responseText);
		
		toggleShow(target);

	};

	// Creates and assigns the variables required to be sent to the loader function and gets all
	// the input fields from the form page and assigns them to variables to be sent as parameters.
	doAdd = function (obj) {

		var method = "POST";
		var url = "index.php?action=admin&page=" + obj;
		var params;
		var target = "operationAlert";
		
		switch (obj) {
			case "administrators":
				params = getAdministratorParams();
				break;
			case "categories":
				params = getCategoryParams();
				break;
			case "currencies":
				params = getCurrencyParams();
				break;
			case "customers":
				params = getCustomerParams();
				break;
			case "products":
				params = getProductParams();
				break;
			case "reviews":
				params = getReviewParams();
				break;
			default:
				params = "";
		}

		params = "operation=add" + params;

		window.console.log("doAdd: " + method + "\r\n" + url + "\r\n" + params + "\r\n" + target);

		loader(method, url, params, target);

		refreshList(obj);

	};

	// Creates and assigns the variables required to be sent to the loader function and gets all
	// the input fields from the form page and assigns them to variables to be sent as parameters.
	doEdit = function (obj) {

		var method = "POST";
		var url = "index.php?action=admin&page=" + obj;
		var params;
		var target = "operationAlert";
		
		switch (obj) {
			case "administrators":
				var admin_ID = document.getElementById('admin_ID').value;
				params = "&admin_ID=" + admin_ID + getAdministratorParams();
				break;
			case "categories":
				var category_ID = document.getElementById('category_ID').value;
				params = "&category_ID=" + category_ID + getCategoryParams();
				break;
			case "currencies":
				var currency_ID = document.getElementById('currency_ID').value;
				params = "&currency_ID=" + currency_ID + getCurrencyParams();
				break;
			case "customers":
				var customer_ID = document.getElementById('customer_ID').value;
				params = "&customer_ID=" + customer_ID + getCustomerParams();
				break;
			case "products":
				var product_ID = document.getElementById('product_ID').value;
				params = "&product_ID=" + product_ID + getProductParams();
				break;
			case "reviews":
				var review_ID = document.getElementById('review_ID').value;
				params = "&review_ID=" + review_ID + getReviewParams();
				break;
			case "settings":
				params = getStoreParams();
			default:
				params = "";
		}

		params = "operation=edit" + params;

		window.console.log("doEdit: " + method + "\r\n" + url + "\r\n" + params + "\r\n" + target);

		loader(method, url, params, target);

		refreshList(obj);

	};

	// Creates and assigns the variables required to be sent to the loader function in order to
	// use it to call the form to add an object.
	getAddForm = function (obj) {

		var method = "GET"
		var url = "index.php?action=admin&page=" + obj + "&operation=add";
		var params = "";
		var target = "message";

		if (document.getElementById(target).classList.contains('displayBlock')) {
			toggleHide(target);
		} else {
			loader(method, url, params, target);
		}

	};

	// Creates and assigns the variables required to be sent to the loader function in order to
	// use it to call the form to edit an object.
	getEditForm = function (obj, prefix, id) {

		var method = "GET";
		var url = "index.php?action=admin&page=" + obj + "&operation=edit&" + prefix + "=" + id;
		var params = "";
		var target = "edit_" + id;

		if (document.getElementById(target).classList.contains('displayBlock')) {
			toggleHide(target);
		} else {
			loader(method, url, params, target);
		}

	};

	// Gets the values of the input fields from the administrator add/edit page
	// and then returns them to the main function.
	getAdministratorParams = function () {
		var admin_username = document.getElementById('admin_username').value;
		var admin_password = document.getElementById('admin_password').value;
		var admin_firstname = document.getElementById('admin_firstname').value;
		var admin_lastname = document.getElementById('admin_lastname').value;
		var admin_email = document.getElementById('admin_email').value;
		return "&admin_username=" + admin_username + "&admin_password=" + admin_password + "&admin_firstname=" + admin_firstname + "&admin_lastname=" + admin_lastname + "&admin_email=" + admin_email;
	};

	// Gets the values of the input fields from the category add/edit page
	// and then returns them to the main function.
	getCategoryParams = function () {
		var category_name = document.getElementById('category_name').value;
		var category_parent_ID = document.getElementById('category_parent_ID').value;
		return "&category_name=" + category_name + "&category_parent_ID=" + category_parent_ID;
	};

	// Gets the values of the input fields from the currency add/edit page
	// and then returns them to the main function.
	getCurrencyParams = function () {
		var currency_name = document.getElementById('currency_name').value;
		var currency_code = document.getElementById('currency_code').value;
		var currency_symbol = document.getElementById('currency_symbol').value;
		return "&currency_name=" + currency_name + "&currency_code=" + currency_code + "&currency_symbol=" + currency_symbol;
	};

	// Gets the values of the input fields from the customer add/edit page
	// and then returns them to the main function.
	getCustomerParams = function () {
		var customer_username = document.getElementById('customer_username').value;
		var customer_password = document.getElementById('customer_password').value;
		var customer_firstname = document.getElementById('customer_firstname').value;
		var customer_lastname = document.getElementById('customer_lastname').value;
		var customer_address1 = document.getElementById('customer_address1').value;
		var customer_address2 = document.getElementById('customer_address2').value;
		var customer_postcode = document.getElementById('customer_postcode').value;
		var customer_phone = document.getElementById('customer_phone').value;
		var customer_email = document.getElementById('customer_email').value;
		return "&customer_username=" + customer_username + "&customer_password=" + customer_password + "&customer_firstname=" + customer_firstname + "&customer_lastname=" + customer_lastname + "&customer_address1=" + customer_address1 + "&customer_address2=" + customer_address2 + "&customer_postcode=" + customer_postcode + "&customer_phone=" + customer_phone + "&customer_email=" + customer_email;
	};

	// Gets the values of the input fields from the product add/edit page
	// and then returns them to the main function.
	getProductParams = function () {
		var category_ID = document.getElementById('category_ID').value;
		var product_name = document.getElementById('product_name').value;
		var product_description = document.getElementById('product_description').value;
		var product_condition = document.getElementById('product_condition').value;
		var product_price = document.getElementById('product_price').value;
		var product_stock = document.getElementById('product_stock').value;
		var product_image = document.getElementById('product_image').value.replace("C:\\fakepath\\", "");
		return "&category_ID=" + category_ID + "&product_name=" + product_name + "&product_description=" + product_description + "&product_condition=" + product_condition + "&product_price=" + product_price + "&product_stock=" + product_stock + "&product_image=" + product_image;
	};

	// Gets the values of the input fields from the review add/edit page
	// and then returns them to the main function.
	getReviewParams = function () {
		var product_ID = document.getElementById('product_ID').value;
		var review_subject = document.getElementById('review_subject').value;
		var review_description = document.getElementById('review_description').value;
		var review_rating = document.getElementById('review_rating').value;
		var customer_ID = document.getElementById('customer_ID').value;
		return "&product_ID=" + product_ID + "&review_subject=" + review_subject + "&review_description=" + review_description + "&review_rating=" + review_rating + "&customer_ID=" + customer_ID;
	};

	// Gets the values of the input fields from the store edit page
	// and then returns them to the main function.
	getStoreParams = function () {
		var store_name = document.getElementById('store_name').value;
		var store_address = document.getElementById('store_address').value;
		var store_phone = document.getElementById('store_phone').value;
		var currency_ID = document.getElementById('currency_ID').value;
		return "&store_name=" + store_name + "&store_address=" + store_address + "&store_phone=" + store_phone + "&currency_ID=" + currency_ID;
	};

	// Creates and assigns the variables required to be sent to the loader function in order to
	// use it to delete an object.
	doDelete = function (obj, prefix, id) {

		var method = "DELETE";
		var url = "index.php?action=admin&page=" + obj + "&operation=delete&" + prefix + "=" +id;
		var params = "";
		var target = "operationAlert";

		loader(method, url, params, target);

		refreshList(obj);

	};

	// Refreshes the category list after every change in order to maintain only the currently
	// existing records on the user's screen.
	refreshList = function (obj) {

		var method = "GET";
		var url = "index.php?action=admin&page=" + obj + "&operation=list";
		var params = "";
		var target = "list";

		loader(method, url, params, target);

	};

	// Function to be called when the document is loaded on every page.
	loaded = function () {

		var parts = window.location.search.substr(1).split("&");
		var GET = {};
		for (var i = 0; i < parts.length; i++) {
			var temp = parts[i].split("=");
			GET[decodeURIComponent(temp[0])] = decodeURIComponent(temp[1]);
		}

		if (GET['page'] == "administrators" || GET['page'] == "categories" || GET['page'] == "currencies" || GET['page'] == "customers" || GET['page'] == "products" || GET['page'] == "reviews") {
			refreshList(GET['page']);
		}

	};

	// Returns the respective function to the objects listed below.
	return {
		"toggleShow": toggleShow,
		"toggleHide": toggleHide,
		"toggleShowHide": toggleShowHide,
		"doAdd": doAdd,
		"doEdit": doEdit,
		"getAddForm": getAddForm,
		"getEditForm": getEditForm,
		"doDelete": doDelete,
		"loaded": loaded
	};

// The () right before the end of the function makes sure that this function is called
// as soon as the page is loaded and returns the value that will then be stored in the
// main function.
}());

// Creates an event listener for when the page has finished loading and calls the function
// stated below as soon as this has happened.
window.addEventListener("load", layercms.webscrp.loaded);