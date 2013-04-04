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
			if (self::_exists('customer_ID', $customer_ID, true)) {
				$customer = self::_getCustomerById($customer_ID);
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
		 * Adds a customer into the database.
		 * 
		 * @param  string $customer_username  Customer's login username.
		 * @param  string $customer_password  Customer's login password.
		 * @param  string $customer_firstname Customer's forename.
		 * @param  string $customer_lastname  Customer's surname.
		 * @param  string $customer_address1  Customer address (line 1).
		 * @param  string $customer_address2  Customer address (line 2).
		 * @param  string $customer_postcode  Postcode of the address.
		 * @param  int    $customer_phone     Customer phone number.
		 * @param  string $customer_email     Customer e-mail address.
		 * @access public
		 */
		public function insert($customer_username = null, $customer_password = null, $customer_firstname = null, $customer_lastname = null, $customer_address1 = null, $customer_address2 = null, $customer_postcode = null, $customer_phone = null, $customer_email = null)
		{
			if (self::isAdmin()) {
				if (!self::_exists('customer_username', $customer_username)) {
					$this->Customer->clear();
					$customer = array(
						'customer_username'  => $customer_username,
						'customer_password'  => $customer_password,
						'customer_firstname' => $customer_firstname,
						'customer_lastname'  => $customer_lastname,
						'customer_address1'  => $customer_address1,
						'customer_address2'  => $customer_address2,
						'customer_postcode'  => $customer_postcode,
						'customer_phone'     => $customer_phone,
						'customer_email'     => $customer_email
					);
					$this->Customer->insert($customer);
					self::set('insert', $customer);
					self::set('message', 'Customer successfully inserted.');
					self::set('alert', 'alert-success');
					return true;
				} else {
					self::set('message', 'Customer username already exists.');
					self::set('alert', '');
					return false;
				}
			} else {
				$this->_action = 'unauthorizedAccess';
			}
		}

		/**
		 * Removes a customer from the database.
		 * 
		 * @param  int    $customer_ID Customer identifier.
		 * @access public
		 */
		public function delete($customer_ID = null)
		{
			if (self::isAdmin()) {
				if (self::_exists('customer_ID', $customer_ID, true)) {
					$this->Customer->clear();
					$this->Customer->where('customer_ID', $customer_ID);
					$this->Customer->delete();
					self::set('delete', true);
					self::set('message', 'Customer successfully deleted.');
					self::set('alert', 'alert-success');
					return true;
				} else {
					self::set('message', 'Customer does not exist.');
					self::set('alert', '');
					return false;
				}
			} else {
				$this->_action = 'unauthorizedAccess';
			}
		}

		/**
		 * Modifies a customer in the database with the specified new attributes.
		 * 
		 * @param  int    $customer_ID        Customer identifier.
		 * @param  string $customer_username  Customer's login username.
		 * @param  string $customer_password  Customer's login password.
		 * @param  string $customer_firstname Customer's forename.
		 * @param  string $customer_lastname  Customer's surname.
		 * @param  string $customer_address1  Customer address (line 1).
		 * @param  string $customer_address2  Customer address (line 2).
		 * @param  string $customer_postcode  Postcode of the address.
		 * @param  int    $customer_phone     Customer phone number.
		 * @param  string $customer_email     Customer e-mail address.
		 * @access public
		 */
		public function update($customer_ID = null, $customer_username = null, $customer_password = null, $customer_firstname = null, $customer_lastname = null, $customer_address1 = null, $customer_address2 = null, $customer_postcode = null, $customer_phone = null, $customer_email = null)
		{
			if (self::isAdmin()) {
				if (self::_exists('customer_ID', $customer_ID, true)) {
					$this->Customer->clear();
					$this->Customer->where('customer_ID', $customer_ID, true);
					$customer = array(
						'customer_username'  => $customer_username,
						'customer_password'  => $customer_password,
						'customer_firstname' => $customer_firstname,
						'customer_lastname'  => $customer_lastname,
						'customer_address1'  => $customer_address1,
						'customer_address2'  => $customer_address2,
						'customer_postcode'  => $customer_postcode,
						'customer_phone'     => $customer_phone,
						'customer_email'     => $customer_email
					);
					$this->Customer->update($customer);
					self::set('update', $customer);
					self::set('message', 'Customer successfully updated.');
					self::set('alert', 'alert-success');
					return true;
				} else {
					self::set('message', 'Customer does not exist.');
					self::set('alert', '');
					return false;
				}
			} else {
				$this->_action = 'unauthorizedAccess';
			}
		}

		/**
		 * Returns customer values in a variable.
		 * 
		 * @param  int       $customer_ID Customer identifier.
		 * @return array                  Returns the customer values.
		 * @access protected
		 */
		protected function _getCustomerById($customer_ID = null)
		{
			if (self::_exists('customer_ID', $customer_ID, true)) {
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
		protected function _exists($column = null, $value = null, $requireInt = false, $customTable = 'customers')
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