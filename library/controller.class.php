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
		protected $variables = array();

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
			//global $inflect;

			// Checks if the database has been setup, otherwise blocks all
			// connectivity and sends the user to the installation page.
			if (DB_SETUP == false && $controller != 'installer') header('Location: ' . BASE_PATH . DS . 'installer');
			
			$this->_controller = $controller == null ? 'Controller' : ucfirst($controller);
			$this->_action = $this->_controller == 'Controller' ? 'index' : $action;

			$model = ucfirst(Inflect::singularize($this->_controller));
			$this->$model = $model != 'Controller' ? new $model : new Model;
			
			self::shared($model);
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
		public function shared($model)
		{
			// Checks if the database has been setup.
			if (DB_SETUP == true) {
				$store = $this->$model->query('SELECT * FROM settings');
				$categories = $this->$model->query('SELECT * FROM categories', true);
				self::set('store', $store);
				self::set('categories', $categories);
				$pageTitle = $this->_controller != 'Controller' ? $this->_controller : 'Home';
				self::set('pageTitle', $pageTitle);
				// Checks if the administrator is logged in.
				if (isset($_SESSION['SESS_ADMINLOGGEDIN']) && isset($_SESSION['SESS_ADMINID'])) {
					$this->$model->table('administrators');
					$this->$model->where('admin_ID', $_SESSION['SESS_ADMINID']);
					$this->$model->select();
					$this->$model->execute();
					$admin = $this->$model->fetch();
					self::set('admin', $admin);
				}
			} else {
				self::set('pageTitle', 'WEBSCRP');
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
			$this->variables[$name] = $value;
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
			extract($this->variables);

			if ($ajax == false) {
				require_once SERVER_ROOT . DS . 'application' . DS . 'views' . DS . 'header.php';
				if ($full == false) require_once SERVER_ROOT . DS . 'application' . DS . 'views' . DS . 'navigation.php';
			}

			$viewLowerCase = strtolower(SERVER_ROOT . DS . 'application' . DS . 'views' . DS . $this->_controller . DS . $this->_action . '.php');
			$viewGlobal = glob(strtolower(SERVER_ROOT . DS . 'application' . DS . 'views' . DS . $this->_controller . DS . '*'));
			$viewArray = $viewGlobal ? $viewGlobal : array();
			if ($viewArray != array()) {
				foreach ($viewArray as $view) {
					if (strtolower($view) == $viewLowerCase) {
						include $view;
					}
				}
			}

			if ($ajax == false) {
				if ($full == false) require_once SERVER_ROOT . DS . 'application' . DS . 'views' . DS . 'sidebar.php';
				require_once SERVER_ROOT . DS . 'application' . DS . 'views' . DS . 'footer.php';
			}
		}

	}

?>