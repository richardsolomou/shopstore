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
				$model = ucfirst(Inflect::singularize($this->_controller));
				$this->$model = $model != 'Controller' ? new $model : new Model;
				// Stores the settings of the website in a variable.
				$settings = $this->$model->query('SELECT `setting_value` FROM `settings` WHERE `setting_column` = "website_name"');
				self::set('settings', $settings);
				// Stores the categories of the website in a variable.
				$categories = $this->$model->query('SELECT * FROM categories', true);
				self::set('categories', $categories);
				// Sets the title of the current page according to the controller.
				$pageTitle = $this->_controller != 'Controller' ? $this->_controller : 'Home';
				self::set('pageTitle', $pageTitle);
				self::set('title', $settings['setting_value'] . ' &raquo; ' . $pageTitle);
				// Checks if the administrator is logged in.
				if (self::isAdmin()) {
					$this->$model->table('administrators');
					$this->$model->where('admin_ID', $_SESSION['SESS_ADMINID']);
					$this->$model->select();
					self::set('admin', $this->$model->fetch());
				}
			} else {
				// Database was not setup, forward to installer.
				self::set('title', 'LayerCMS Installer');
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

			if ($this->_controller == 'Controller' || $this->_action == 'unauthorizedAccess') $this->_controller = null;
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