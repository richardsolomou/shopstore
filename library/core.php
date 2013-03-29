<?php

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

	function setRouting()
	{
		global $url;

		$controller=$action=$controllerName="";

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
		}
		if (file_exists(SERVER_ROOT . DS . 'application' . DS . 'controllers' . DS . $controller . '.php')) {
			$controllerName = ucfirst($controller);
			$dispatch = new $controllerName();
			if (method_exists($controllerName, $action)) call_user_func_array(array($dispatch, $action), $queryString);
		}
	}

	setReporting();
	setRouting();

?>
