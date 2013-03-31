<?php

	/**
	 * Include the inflection class.
	 */
	require_once SERVER_ROOT . DS . 'library' . DS . 'inflect.class.php';
	$inflect = new Inflect;

	/**
	 * Include the main controller and the database.
	 */
	require_once SERVER_ROOT . DS . 'library' . DS . 'database.class.php';
	require_once SERVER_ROOT . DS . 'library' . DS . 'model.class.php';
	require_once SERVER_ROOT . DS . 'library' . DS . 'controller.class.php';
	
	/**
	 * Include all element class controllers.
	 */
	require_once SERVER_ROOT . DS . 'application' . DS . 'controllers' . DS . 'administrators.php';
	require_once SERVER_ROOT . DS . 'application' . DS . 'controllers' . DS . 'categories.php';
	require_once SERVER_ROOT . DS . 'application' . DS . 'controllers' . DS . 'currencies.php';
	require_once SERVER_ROOT . DS . 'application' . DS . 'controllers' . DS . 'customers.php';
	require_once SERVER_ROOT . DS . 'application' . DS . 'controllers' . DS . 'products.php';
	require_once SERVER_ROOT . DS . 'application' . DS . 'controllers' . DS . 'reviews.php';
	require_once SERVER_ROOT . DS . 'application' . DS . 'controllers' . DS . 'settings.php';

	/**
	 * Include the element class models.
	 */
	require_once SERVER_ROOT . DS . 'application' . DS . 'models' . DS . 'administrator.php';
	require_once SERVER_ROOT . DS . 'application' . DS . 'models' . DS . 'category.php';
	require_once SERVER_ROOT . DS . 'application' . DS . 'models' . DS . 'currency.php';
	require_once SERVER_ROOT . DS . 'application' . DS . 'models' . DS . 'customer.php';
	require_once SERVER_ROOT . DS . 'application' . DS . 'models' . DS . 'product.php';
	require_once SERVER_ROOT . DS . 'application' . DS . 'models' . DS . 'review.php';
	require_once SERVER_ROOT . DS . 'application' . DS . 'models' . DS . 'setting.php';

?>