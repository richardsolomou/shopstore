<?php

	/**
	* Autoload any classes that are required.
	*/
	function __autoload($className)
	{
		if (file_exists($libraryClass = SERVER_ROOT . '/library/' . strtolower($className) . '.class.php')) {
			// Includes the inflect, main controller and database classes.
			require_once $libraryClass;
		} else if (file_exists($controllerClass = SERVER_ROOT . '/application/controllers/' . strtolower($className) . '.php')) {
			// Includes the element controllers for categories, customers, etc.
			require_once $controllerClass;
		} else if (file_exists($modelClass = SERVER_ROOT . '/application/models/' . strtolower($className) . '.php')) {
			// Include the element class models for categories, customers, etc.
			require_once $modelClass;
		}
	}

	/**
	 * If currently in development display errors, otherwise hide and log
	 * the errors in a file.
	 */
	function setReporting()
	{
		if (DEVELOPMENT_ENVIRONMENT == true) {
			error_reporting(E_ALL);
			ini_set('display_errors', '1');
		} else {
			error_reporting(E_ALL);
			ini_set('display_errors', '0');
			ini_set('log_errors', '1');
			ini_set('error_log', SERVER_ROOT . 'error.log');
		}
	}

	/**
	 * Checks if the element controller file exists and moves on to check for
	 * the method given. If either one doesn't exist, it returns a 404 error.
	 * 
	 * @param string $controller   Controller to use for operation.
	 * @param string $action       Method to use in the controller.
	 * @param string  $queryString Parameters following the action.
	 */
	function setControllerView($controller, $action, $queryString)
	{
		$controllerName = ucfirst($controller) . 'Controller';
		if (file_exists(SERVER_ROOT . '/application/controllers/' . strtolower($controllerName) . '.php')) {
			$dispatch = new $controllerName($controller, $action);
			$action = empty($action) ? 'defaultPage' : $action;
			// Checks if the method exists.
			if (method_exists($controllerName, $action)) {
				$reflection = new ReflectionMethod($controllerName, $action);
				// Checks if the method is public, and if so displays it.
				if ($reflection->isPublic()) {
					call_user_func_array(array($dispatch, $action), $queryString);
				} else {
					notFound();
				}
			} else {
				notFound();
			}
		} else {
			notFound();
		}
	}

	/**
	 * Sends the user to an error 404 page.
	 */
	function notFound()
	{
		header("HTTP/1.1 404 Not Found");
		$controller = new Controller(null, '404');
		$controller->set('title', 'Error 404');
	}

	/**
	 * Break down the URL in parts to figure out the controller and action,
	 * and then create a new instance of that controller.
	 */
	function setRouting()
	{
		// Use the $url variable from /index.php
		global $url;

		$controller = $action = $controllerName = null;

		if (isset($url)) {
			$urlArray = array();
			$urlArray = explode('/', $url);
			$controller = $urlArray[0];
			array_shift($urlArray);
			if (isset($urlArray[0])) {
				$action = $urlArray[0];
				array_shift($urlArray);
			}
			$queryString = $urlArray;
			setControllerView($controller, $action, $queryString);
		} else {
			// If the URL is empty, render the default index page.
			$dispatch = new Controller();
		}
	}

	// Execute the two functions
	setReporting();
	setRouting();

?>
