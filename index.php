<?php

	/**
	 * Start the session.
	 */
	session_start();

	/**
	 * Define the basic global variables.
	 */
	define('DS', DIRECTORY_SEPARATOR);
	define('SERVER_ROOT', dirname(__FILE__));
	define('BASE_PATH', dirname($_SERVER['PHP_SELF']) == '/' ? '' : dirname($_SERVER['PHP_SELF']));
	define('ADMIN_PATH', BASE_PATH . DS . 'admin' . DS);

	/**
	 * Write the raw URL to a variable to be used in a later function.
	 */
	$url = isset($_GET['url']) ? $_GET['url'] : null;

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
	require_once SERVER_ROOT . DS . 'application' . DS . 'controllers' . DS . 'categories.php';
	require_once SERVER_ROOT . DS . 'application' . DS . 'controllers' . DS . 'currencies.php';
	require_once SERVER_ROOT . DS . 'application' . DS . 'controllers' . DS . 'customers.php';
	require_once SERVER_ROOT . DS . 'application' . DS . 'controllers' . DS . 'managers.php';
	require_once SERVER_ROOT . DS . 'application' . DS . 'controllers' . DS . 'products.php';
	require_once SERVER_ROOT . DS . 'application' . DS . 'controllers' . DS . 'reviews.php';
	require_once SERVER_ROOT . DS . 'application' . DS . 'controllers' . DS . 'settings.php';

	/**
	 * Include the element class models.
	 */
	require_once SERVER_ROOT . DS . 'application' . DS . 'models' . DS . 'category.php';

	/**
	 * Include the server configuration and core functions.
	 */
	require_once SERVER_ROOT . DS . 'library' . DS . 'config.php';
	require_once SERVER_ROOT . DS . 'library' . DS . 'core.php';

?>