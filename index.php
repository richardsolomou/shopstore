<?php

	/**
	 * Start the session.
	 */
	session_start();

	/**
	 * Define the basic global variables.
	 */
	define('SERVER_ROOT', dirname(__FILE__));
	define('BASE_PATH', dirname($_SERVER['PHP_SELF']) == '/' ? '' : dirname($_SERVER['PHP_SELF']));

	/**
	 * Write the raw URL to a variable to be used in a later function.
	 */
	$url = isset($_GET['url']) ? $_GET['url'] : null;

	/**
	 * Include the server configuration and core functions.
	 */
	require_once SERVER_ROOT . '/library/config.php';
	require_once SERVER_ROOT . '/library/core.php';

?>