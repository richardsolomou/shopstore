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
		 * @param  int $customer_ID Customer identifier.
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
		 * Returns customer values in a variable.
		 * 
		 * @param  int   $customer_ID Customer identifier.
		 * @return array              Returns the customer values.
		 * @access public
		 */
		public function getCustomerById($customer_ID = null)
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

	}

?>