<?php

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
	 */
	function setControllerView($controller, $action, $queryString)
	{
		if (file_exists(SERVER_ROOT . DS . 'application' . DS . 'controllers' . DS . $controller . '.php')) {
			$controllerName = ucfirst($controller);
			$dispatch = new $controllerName($controller, $action);
			$action = empty($action) ? 'defaultPage' : $action;
			if (method_exists($controllerName, $action)) {
				$dispatch->$action($queryString);
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
		$controller->set('pageTitle', 'Error 404');
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
			$dispatch = new Controller(null, 'index');
		}
	}

	// Execute the two functions
	setReporting();
	setRouting();

?>
