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
			$dispatch = new $controllerName;
			$action = empty($action) ? 'defaultPage' : $action;
			if (method_exists($controllerName, $action)) {
				call_user_func_array(array($dispatch, $action), $queryString);
			} else {
				header("HTTP/1.0 404 Not Found");
				require_once SERVER_ROOT . DS . 'application' . DS . 'views' . DS . '404.php';
			}
		} else {
			header("HTTP/1.0 404 Not Found");
			require_once SERVER_ROOT . DS . 'application' . DS . 'views' . DS . '404.php';
		}
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
			$dispatch = new Controller;
			call_user_func_array(array($dispatch, 'defaultPage'), array());
		}
	}

	// Execute the two functions
	setReporting();
	setRouting();

?>