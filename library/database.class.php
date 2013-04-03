<?php

	/**
	 * Call the database to execute various functions.
	 */
	class Database
	{

		/**
		 * Connection to the database.
		 * @var object
		 * @access protected
		 */
		protected $_connection;

		/**
		 * Query string to be passed.
		 * @var string
		 * @access protected
		 */
		protected $_query;

		/**
		 * Table name to use for the operations.
		 * @var string
		 * @access protected
		 */
		protected $_table;

		/**
		 * Statement to be parsed through PHP Data Objects.
		 * @var object
		 * @access protected
		 */
		protected $_statement;

		/**
		 * Contains string of the where clause to be passed.
		 * @var string
		 * @access protected
		 */
		protected $_clause;

		/**
		 * Sets the variable by which to order the results from.
		 * @var string
		 * @access protected
		 */
		protected $_orderBy;

		/**
		 * Sets sorting to ascending or descending order.
		 * @var string
		 * @access protected
		 */
		protected $_order;

		/**
		 * Limit the query results to this number.
		 * @var [type]
		 */
		protected $_limit;

		/**
		 * Array containing the parameters passed for each function.
		 * @var array
		 */
		protected $_params = array();

		/**
		 * Establishes a connection to the database using the global variables
		 * stored in the configuration file.
		 * 
		 * @param  string $host [description]
		 * @param  string $user Username required to establish connection.
		 * @param  string $pass Password for connecting to the database.
		 * @param  string $db   Contains the database name
		 * @access public
		 */
		public function connect($host, $db, $user, $pass)
		{
			$this->_connection = new PDO('mysql:host=' . $host . ';dbname=' . $db, $user, $pass);
			$this->_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}

		/**
		 * Closes the connection to the database
		 * 
		 * @access public
		 */
		public function disconnect()
		{
			$this->_connection = null;
		}

		/**
		 * Clears the values of all class variables.
		 * 
		 * @access public
		 */
		public function clear()
		{
			$this->_query = null;
			$this->_table = Inflect::pluralize(strtolower(get_class($this)));
			$this->_statement = null;
			$this->_clause = null;
			$this->_orderBy = null;
			$this->_order = null;
			$this->_limit = null;
			$this->_params = array();
		}

		/**
		 * Sets the table name to be used for accessing the database.
		 * 
		 * @param  string $table Table name to use in the database.
		 * @access public
		 */
		public function table($table)
		{
			$this->_table = $table;
		}

		/**
		 * Receives two variables and pushes them to the parameters array
		 * and then adds the statement to the clause class variable.
		 * 
		 * @param  string $column Column of the specific table in the database.
		 * @param  string $value  Value of the column to be searched for.
		 * @access public
		 */
		public function where($column, $value)
		{
			array_push($this->_params, array('column' => $column, 'value' => $value));
			$this->_clause .= '`' . $column . '` = ' . $value . ' AND ';
		}

		/**
		 * Sets the limit of records to retrieve from the database.
		 * 
		 * @param  int $limit Number of records to get.
		 * @access public
		 */
		public function limit($limit)
		{
			$this->_limit = $limit;
		}

		/**
		 * Sorts the database based on the first variable in ascending or
		 * descending order based on the second variable.
		 * 
		 * @param  string $orderBy Column to sort by.
		 * @param  string $order   Ascending or descending order.
		 * @access public
		 */
		public function order($orderBy, $order = 'ASC')
		{
			$this->_orderBy = $orderBy;
			$this->_order = $order;
		}

		/**
		 * Sends a query to the database if there's no where clauses and the
		 * parameters array is empty, otherwise sends a prepared statement
		 * and binds the value of the variables sent to parameters.
		 * 
		 * @param  boolean $override Override and use prepared statements.
		 * @access public
		 */
		public function execute($override = false)
		{
			if ($override == false && ($this->_clause == null || $this->_params == array())) {
				$this->_statement = $this->_connection->query($this->_query);
			} else {
				$this->_statement = $this->_connection->prepare($this->_query);
				foreach ($this->_params as $param) {
					$this->_statement->bindValue($param['column'], $param['value']);
				}
				$this->_statement->execute();
			}

		}

		/**
		 * Sends a custom query to the database and fetches its result(s).
		 * 
		 * @param  string       $query Contains the query to be sent.
		 * @param  boolean      $many  Fetch multiple or one result.
		 * @return array|string        Returns one result or an array of results.
		 * @access public
		 */
		public function query($query, $many = false)
		{
			$this->_query = $query;
			$this->execute();
			return $this->fetch($many);
		}

		/**
		 * Returns the number of rows that the statement previously returned.
		 * 
		 * @return int Number of rows from statement.
		 * @access public
		 */
		public function rowCount()
		{
			$rowCount = $this->_statement->rowCount();
			return $rowCount;
		}

		/**
		 * Fetches the results of the statement that was executed.
		 * 
		 * @param  boolean      $many Fetch multiple or one result.
		 * @return array|string       Returns one result or an array of results.
		 * @access public
		 */
		public function fetch($many = false)
		{
			if ($many == false) {
				return $this->_statement->fetch(PDO::FETCH_ASSOC);
			} else {
				return $this->_statement->fetchAll(PDO::FETCH_ASSOC);
			}
		}

		/**
		 * Selects the records from the given columns, based on the where
		 * clause, sorting and limit given from previous functions.
		 * 
		 * @param array $columns The columns to be retrieved from the database.
		 * @access public
		 */
		public function select($columns = array())
		{
			$where = $this->_clause ? ' WHERE ' . substr($this->_clause, 0, -5) : null;
			$order = $this->_orderBy ? ' ORDER BY `' . $this->_orderBy . '` ' . $this->_order : null;
			$limit = $this->_limit ? ' LIMIT ' . $this->_limit : null;
			$columns = $columns != array() ? implode(', ', $columns) : '*';
			$this->_query = 'SELECT ' . $columns . ' FROM `' . $this->_table . '`' . $where . $order . $limit;
			$this->execute();
		}

		/**
		 * Receives an array of the values and their respective columns to be
		 * added in the database and inserts that record in the database using
		 * a prepared statement.
		 * 
		 * @param array $arr The values to be added in the database.
		 * @access public
		 */
		public function insert($arr = array())
		{
			$this->_query = 'ALTER TABLE ' . $this->_table . ' AUTO_INCREMENT = 1';
			$this->execute();
			$columns = $values = null;
			foreach ($arr as $column => $value) {
				array_push($this->_params, array('column' => $column, 'value' => $value));
				$columns .= '`' . $column . '`, ';
				$values .= ':' . $column . ', ';
			}
			$columns = substr($columns, 0, -2);
			$values = substr($values, 0, -2);
			$this->_query = 'INSERT INTO `' . $this->_table . '` (' . $columns . ') VALUES (' . $values . ')';
			$this->execute(true);
		}

		/**
		 * Deletes a specific record from the database based on the values of
		 * the where clause received.
		 * 
		 * @access public
		 */
		public function delete()
		{
			$where = $this->_clause ? substr(' WHERE ' . $this->_clause, 0, -5) : null;
			$limit = $this->_limit ? ' LIMIT ' . $this->_limit : null;
			$this->_query = 'DELETE FROM `' . $this->_table . '`' . $where . $limit;
			$this->execute();
			$this->clear();
			$this->_query = 'ALTER TABLE ' . $this->_table . ' AUTO_INCREMENT = 1';
			$this->execute();
		}

		/**
		 * Receives an array of the values and their respective columns to be
		 * modified in the database and updates that record in the database using
		 * a prepared statement.
		 * 
		 * @param array $arr The values to be modified in the database.
		 * @access public
		 */
		public function update($arr = array())
		{
			$where = $this->_clause ? substr(' WHERE ' . $this->_clause, 0, -5) : null;
			$set = null;
			foreach ($arr as $column => $value) {
				array_push($this->_params, array($column, $value));
				$set .= '`' . $column . '` = :' . $value . ', ';
			}
			$set = substr($values, 0, -2);
			$this->_query = 'UPDATE `' . $this->_table . '` SET ' . $set . $where;
			$this->execute();
		}

	}

?>