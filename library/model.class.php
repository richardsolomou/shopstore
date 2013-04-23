<?php

	/**
	 * Extends the database and is extended by individual element class models.
	 */
	class Model extends Database
	{

		/**
		 * Contains the name of the model currently being used.
		 * @var string
		 * @access protected
		 */
		protected $_model;

		/**
		 * Activates a connection to the database using the global variables in
		 * the configuration file and assigns the default table according to
		 * the model class being used at the moment.
		 *
		 * @access public
		 */
		public function __construct()
		{
			$this->connect(DB_HOST, DB_NAME, DB_USER, DB_PASS);
			$this->_model = get_class($this);
			$this->table(strtolower($this->_model));
		}

		/**
		 * Closes the connection to the database.
		 *
		 * @access public
		 */
		public function __destruct()
		{
			$this->disconnect();
		}

	}

?>