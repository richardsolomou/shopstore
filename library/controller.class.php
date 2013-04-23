<?php

	/**
	 * Serves as the main controller class that initializes its descendants and
	 * creates global attributes for every page in the system.
	 */
	class Controller
	{

		/**
		 * Name of the controller to connect to.
		 * @var    string
		 * @access protected
		 */
		protected $_controller;

		/**
		 * Name of the method action to take.
		 * @var    string
		 * @access protected
		 */
		protected $_action;

		/**
		 * Holds the variables to be passed on the views page.
		 * @var    array
		 * @access protected
		 */
		protected $_variables = array();

		/**
		 * Decides if this is an AJAX call. Defaults to false.
		 * @var    boolean
		 * @access public
		 */
		public $ajax = false;

		/**
		 * Decides if this is a full page width call. Defaults to false.
		 * @var    boolean
		 * @access public
		 */
		public $full = false;

		/**
		 * Creates the model of the controller that was created and stores
		 * the controller and action names in the class.
		 * 
		 * @param  string $controller Element class controller to use.
		 * @param  string $action     Method to execute.
		 * @access public
		 */
		public function __construct($controller = null, $action = null)
		{
			// Checks if the database has been setup, otherwise blocks all
			// connectivity and sends the user to the installation page.
			if (DB_SETUP == false && $controller != 'installer') header('Location: ' . BASE_PATH . '/installer');
			
			$this->_controller = $controller == null ? 'Controller' : ucfirst($controller);
			$this->_action = $this->_controller == 'Controller' && $action == null ? 'index' : $action;

			self::shared();
		}

		/**
		 * Renders the view page with or without the headers based on the value
		 * of the AJAX public variable.
		 *
		 * @access public
		 */
		public function __destruct()
		{
			self::render($this->ajax, $this->full);
		}

		/**
		 * Executes some operations that run on every load of the controllers.
		 * Collects data from the website settings and categories table since
		 * that data is used more than once in the website. This is done to
		 * avoid code repetition throughout the application.
		 * 
		 * @param  string $model Name of the model class of the controller.
		 * @access public
		 */
		public function shared()
		{
			// Checks if the database has been setup.
			if (DB_SETUP == true) {
				$model = $this->_controller;
				$this->$model = $model != 'Controller' ? new $model : new Model;
				// Stores the settings of the website in a variable.
				$settings = $this->$model->query('SELECT `setting_value` FROM `settings` WHERE `setting_column` = "website_name"');
				self::set('settings', $settings);
				// Stores the categories of the website in a variable.
				$categories = $this->$model->query('SELECT * FROM categories', true);
				self::set('categories', $categories);
				// Stores the products of the website in a variable.
				$products = $this->$model->query('SELECT * FROM products', true);
				self::set('products', $products);
				// Sets the title of the current page and based on the controller.
				if ($this->_controller == 'Controller') {
					$pageTitle = 'Home';
				} else {
					$pageTitle = $this->_controller;
				}
				self::set('pageTitle', $pageTitle);
				self::set('title', $settings['setting_value'] . ' &raquo; ' . $pageTitle);
				self::set('website_name', $settings['setting_value']);
				// Checks if the administrator is logged in.
				if (self::isAdmin()) {
					$this->$model->table('administrators');
					$this->$model->where('admin_ID', $_SESSION['SESS_ADMINID']);
					$this->$model->select();
					self::set('admin', $this->$model->fetch());
				}
				self::set('basketItems', self::_getBasket($model));
				// Gets the currency ID from the website settings table and the
				// respective symbol from the currencies table.
				$settingsCurrency = self::_getSettingByColumn($model, 'currency_ID');
				$currencySymbol = self::_getCurrencyById($model, $settingsCurrency['setting_value']);
				self::set('currencySymbol', $currencySymbol['currency_symbol']);
			} else {
				// Database was not setup, forward to installer.
				self::set('title', 'LayerCMS Installer');
				self::set('website_name', 'LayerCMS');
			}
		}

		/**
		 * Sets a variable name and its value in an array to be later extracted
		 * into the view for the static pages to use.
		 * 
		 * @param  string $name  Identifier of the variable.
		 * @param  mixed  $value The value of the variable to pass.
		 * @access public
		 */
		public function set($name, $value)
		{
			$this->_variables[$name] = $value;
		}

		/**
		 * Checks if the current user is a logged in administrator.
		 * 
		 * @return boolean Returns whether or not an administrator is logged in.
		 * @access public
		 * */
		public function isAdmin()
		{
			if (isset($_SESSION['SESS_ADMINLOGGEDIN']) && isset($_SESSION['SESS_ADMINID'])) {
				return true;
			} else {
				return false;
			}
		}

		/**
		 * Returns all basket items in the database.
		 *
		 * @param  string    $model Model to be used for this operation.
		 * @return array            Items in the basket.
		 * @access protected
		 */
		protected function _getBasket($model)
		{
			// Checks if the user has sufficient privileges.
			if (self::isCustomer()) {
				$this->$model->clear();
				$this->$model->table('basket');
				// Looks up items for this customer.
				$this->$model->where('customer_ID', $_SESSION['SESS_CUSTOMERID']);
				$this->$model->select();
				// Fetches all the basket items.
				return $this->$model->fetch(true);
			} else {
				return array();
			}
		}

		/**
		 * Returns a specified setting from the database.
		 *
		 * @param  string    $model          Model to be used for this operation.
		 * @param  string    $setting_column Name of the setting's column.
		 * @return array                     Settings of the database.
		 * @access protected
		 */
		protected function _getSettingByColumn($model, $setting_column = null)
		{
			// Checks if the setting column value exists.
			if (self::_exists($model, 'setting_column', $setting_column, false, 'settings')) {
				$this->$model->clear();
				// Uses the settings table.
				$this->$model->table('settings');
				// Looks for the setting column with that value.
				$this->$model->where('setting_column', '"' . $setting_column . '"');
				$this->$model->select();
				// Returns the result of the setting.
				return $this->$model->fetch();
			} else {
				return false;
			}
		}

		/**
		 * Returns the values of the currency that was selected.
		 *
		 * @param  string    $model       Model to be used for this operation.
		 * @param  int       $currency_ID Currency identifier.
		 * @return string                 Returns the values of the currency.
		 * @access protected
		 */
		protected function _getCurrencyById($model, $currency_ID = null)
		{
			// Checks if the currency exists.
			if (self::_exists($model, 'currency_ID', $currency_ID, true, 'currencies')) {
				$this->$model->clear();
				// Uses the currencies table.
				$this->$model->table('currencies');
				// Looks for a currency with that identifier.
				$this->$model->where('currency_ID', $currency_ID);
				$this->$model->select();
				// Returns the result of that currency.
				return $this->$model->fetch();
			} else {
				return false;
			}
		}

		/**
		 * Checks if a review exists in the database with the given attributes.
		 *
		 * @param  string    $model       Model to be used for this operation.
		 * @param  string    $column      Name of the column to search on.
		 * @param  string    $value       Value to search for.
		 * @param  boolean   $requireInt  Requires the value sent to be an integer.
		 * @param  string    $customTable Uses a table from another controller.
		 * @return boolean                Does the review exist?
		 * @access protected
		 */
		protected function _exists($model, $column = null, $value = null, $requireInt = false, $customTable = null)
		{
			// Checks if not all characters are digits.
			if ($requireInt == true && !ctype_digit($value)) return false;
			// Allows for the category parent to have a root parent.
			if ($model == 'Categories' && $column == 'category_ID' && $value == '0') return true;
			$this->$model->clear();
			// Uses a different table for other controllers.
			if ($customTable == null) {
				$this->$model->table(strtolower($model));
			} else {
				$this->$model->table($customTable);
			}
			if ($requireInt == false) {
				// Looks for a string value in a specified column.
				$this->$model->where($column, '"' . $value . '"');
			} else {
				// Loooks for an integer value in a specified column.
				$this->$model->where($column, $value);
			}
			$this->$model->select();
			// Returns the appropriate value if the element exists or not.
			if ($this->$model->rowCount() != 0) {
				return true;
			} else {
				return false;
			}
		}

		/**
		 * Checks if the current user is a logged in customer.
		 * 
		 * @return boolean Returns whether or not a customer is logged in.
		 * @access public
		 * */
		public function isCustomer()
		{
			if (isset($_SESSION['SESS_LOGGEDIN']) && isset($_SESSION['SESS_CUSTOMERID'])) {
				return true;
			} else {
				return false;
			}
		}

		/**
		 * Extracts the variables array in the view to be used from static pages.
		 * Responsible for the creation of the main template of every page as
		 * well as for handling AJAX calls and requests in order to remove the
		 * header and footer from them.
		 * 
		 * @param  boolean $ajax Decides if this is an AJAX call.
		 * @access public
		 */
		public function render($ajax = false, $full = false)
		{
			extract($this->_variables);

			if ($ajax == false) {
				require_once SERVER_ROOT . '/application/views/' . 'header.php';
				if ($full == false) require_once SERVER_ROOT . '/application/views/' . 'navigation.php';
			}

			if ($this->_controller == 'Controller' || $this->_action == 'unauthorizedAccess' || $this->_action == 'alert') $this->_controller = null;
			$viewLowerCase = strtolower(SERVER_ROOT . '/application/views/' . $this->_controller . '/' . $this->_action . '.php');
			$viewGlobal = glob(strtolower(SERVER_ROOT . '/application/views/' . $this->_controller . '/' . '*'));
			$viewArray = $viewGlobal ? $viewGlobal : array();
			if ($viewArray != array()) {
				foreach ($viewArray as $view) {
					if (strtolower($view) == $viewLowerCase) {
						include $view;
					}
				}
			}

			if ($ajax == false) {
				if ($full == false) require_once SERVER_ROOT . '/application/views/' . 'sidebar.php';
				require_once SERVER_ROOT . '/application/views/' . 'footer.php';
			}
		}

	}

?>