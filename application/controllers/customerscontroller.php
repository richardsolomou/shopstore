<?php

	/**
	 * Serves as the controller for all customer related functions and
	 * operations. Sends commands to the model to update the model's state.
	 */
	class CustomersController extends Controller
	{

		/**
		 * Lets the system know which file to be used for the view of the
		 * default page in this controller if an action is not specified.
		 * 
		 * @access public
		 */
		public function defaultPage()
		{
			$this->_action = 'getList';
		}

		/**
		 * Searches the database for a customer with the given ID and returns
		 * the results of that customer to the view presentation.
		 * 
		 * @param  int    $customer_ID Customer identifier.
		 * @access public
		 */
		public function getById($customer_ID = null)
		{
			if (self::exists('customer_ID', $customer_ID, true)) {
				$customer = self::getCustomerById($customer_ID);
				self::set('customer', $customer);
			} else {
				$this->_action = 'error';
				return false;
			}
		}

		/**
		 * Returns all customers in the database.
		 * 
		 * @return array  Customers in the database.
		 * @access public
		 */
		public function getList()
		{
			$this->Customer->clear();
			$this->Customer->select();
			return $this->Customer->fetch(true);
		}

		/**
		 * Returns customer values in a variable.
		 * 
		 * @param  int       $customer_ID Customer identifier.
		 * @return array                  Returns the customer values.
		 * @access protected
		 */
		protected function getCustomerById($customer_ID = null)
		{
			if (self::exists('customer_ID', $customer_ID, true)) {
				$this->Customer->clear();
				$this->Customer->where('customer_ID', $customer_ID);
				$this->Customer->select();
				return $this->Customer->fetch();
			} else {
				return false;
			}
		}

		/**
		 * Checks if a customer exists in the database with the given attributes.
		 * 
		 * @param  string    $column      Name of the column to search on.
		 * @param  string    $value       Value to search for.
		 * @param  boolean   $requireInt  Requires the value sent to be an integer.
		 * @param  string    $customTable Uses a table from another controller.
		 * @return boolean                Does the customer exist?
		 * @access protected
		 */
		protected function exists($column = null, $value = null, $requireInt = false, $customTable = 'customers')
		{
			// Checks if all characters are digits.
			if ($requireInt == true && !ctype_digit($value)) return false;
			$this->Customer->clear();
			// Uses a different table for other controllers.
			if ($customTable != 'customers') $this->Customer->table($customTable);
			$this->Customer->where($column, $value);
			$this->Customer->select();
			if ($this->Customer->rowCount() != 0) {
				return true;
			} else {
				return false;
			}
		}

	}

?>