<?php

	/**
	 * Serves as the main controller class that initializes its descendants and
	 * creates global attributes for every page in the system.
	 */
	class Controller
	{

		/**
		 * Name of the controller to connect to.
		 * @var string
		 * @access protected
		 */
		protected $_controller;

		/**
		 * Name of the method action to take.
		 * @var string
		 * @access protected
		 */
		protected $_action;

		/**
		 * Holds the variables to be passed on the views page.
		 * @var array
		 * @access protected
		 */
		protected $variables = array();

		/**
		 * Decides if this is an AJAX call. Defaults to false.
		 * @var bool
		 * @access public
		 */
		public $ajax = false;

		/**
		 * Creates the model of the controller that was created and stores
		 * the controller and action names in the class.
		 * 
		 * @param string $controller Element class controller to use.
		 * @param string $action     Method to execute.
		 * @access public
		 */
		public function __construct($controller = null, $action = null)
		{
			global $inflect;
			
			$this->_controller = ucfirst($controller);
			$this->_action = $action;

			$model = $inflect->singularize(get_class($this));
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
			$this->render($this->ajax);
		}

		/**
		 * Lets the system know which file to be used for the view of the
		 * default page in each controller.
		 *
		 * @access public
		 */
		public function defaultPage()
		{
			$this->_action = 'index';
		}

		/**
		 * Executes some operations that run on every load of the controllers.
		 * Collects data from the website settings and categories table since
		 * that data is used more than once in the website. This is done to
		 * avoid code repetition throughout the application.
		 * 
		 * @param string $model Name of the model class of the controller.
		 * @access public
		 */
		public function shared($model)
		{
			// Checks if the database has been setup.
			if (DB_SETUP == true) {
				$store = $this->$model->query('SELECT * FROM settings');
				$categories = $this->$model->query('SELECT * FROM categories', true);
				$this->set('store', $store);
				$this->set('categories', $categories);
				$pageTitle = $this->_controller != null ? $this->_controller : 'Home';
				$this->set('pageTitle', $pageTitle);
				// Checks if the administrator is logged in.
				if (isset($_SESSION['SESS_ADMINLOGGEDIN']) && isset($_SESSION['SESS_ADMINID'])) {
					$this->$model->table('administrators');
					$this->$model->where('admin_ID', $_SESSION['SESS_ADMINID']);
					$this->$model->select();
					$this->$model->execute();
					$admin = $this->$model->fetch();
					$this->set('admin', $admin);
				}
			} else {
				$this->set('pageTitle', 'WEBSCRP');
			}
		}

		/**
		 * Sets a variable name and its value in an array to be later extracted
		 * into the view for the static pages to use.
		 * 
		 * @param string $name  Identifier of the variable.
		 * @param mixed  $value The value of the variable to pass.
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
		public function render($ajax = false)
		{
			extract($this->variables);

			if ($ajax == false) {
				require_once SERVER_ROOT . DS . 'application' . DS . 'views' . DS . 'header.php';
				require_once SERVER_ROOT . DS . 'application' . DS . 'views' . DS . 'navigation.php';
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
				require_once SERVER_ROOT . DS . 'application' . DS . 'views' . DS . 'sidebar.php';
				require_once SERVER_ROOT . DS . 'application' . DS . 'views' . DS . 'footer.php';
			}
		}

	}

?>