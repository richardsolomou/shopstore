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
			header('Location: ' . BASE_PATH . '/customers/getList');
		}

		/**
		 * Returns all customers in the database.
		 * 
		 * @access public
		 */
		public function getList()
		{
			// Checks if the user has sufficient privileges.
			if (self::isAdmin()) {
				$this->Customer->clear();
				$this->Customer->select();
				// Fetches all the customers.
				$customers = $this->Customer->fetch(true);
				self::set('customers', $customers);
			} else {
				// Returns an unauthorized access page.
				$this->_action = 'unauthorizedAccess';
			}
		}

		/**
		 * Adds a customer into the database.
		 * 
		 * @access public
		 */
		public function insert()
		{
			// Checks if the user has sufficient privileges.
			if (self::isAdmin()) {
				// Only loads the content for this method.
				$this->ajax = true;
				// Checks if this was a POST request.
				if (isset($_POST['operation'])) {
					// Checks if the username is not taken.
					if (!self::_exists('customer_username', $_POST['customer_username'])) {
						$this->Customer->clear();
						$customer = array(
							'customer_username'  => $_POST['customer_username'],
							'customer_password'  => $_POST['customer_password'],
							'customer_firstname' => $_POST['customer_firstname'],
							'customer_lastname'  => $_POST['customer_lastname'],
							'customer_address1'  => $_POST['customer_address1'],
							'customer_address2'  => $_POST['customer_address2'],
							'customer_postcode'  => $_POST['customer_postcode'],
							'customer_phone'     => $_POST['customer_phone'],
							'customer_email'     => $_POST['customer_email']
						);
						// Inserts the customer into the database.
						$this->Customer->insert($customer);
						// Returns the alert message to be sent to the user.
						self::set('message', 'Customer successfully inserted.');
						self::set('alert', 'alert-success');
					} else {
						// Returns the alert message to be sent to the user.
						self::set('message', 'Customer username already exists.');
						self::set('alert', '');
					}
					// Show an alert.
					$this->_action = 'alert';
				}
			} else {
				// Returns an unauthorized access page.
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
			// Checks if the user has sufficient privileges.
			if (self::isAdmin()) {
				// Only loads the content for this method.
				$this->ajax = true;
				// Checks if the customer exists.
				if (self::_exists('customer_ID', $customer_ID, true)) {
					$this->Customer->clear();
					// Looks for the customer with that identifier.
					$this->Customer->where('customer_ID', $customer_ID);
					// Deletes the customer from the database.
					$this->Customer->delete();
					// Returns the alert message to be sent to the user.
					self::set('message', 'Customer successfully deleted.');
					self::set('alert', 'alert-success nomargin');
				} else {
					// Returns the alert message to be sent to the user.
					self::set('message', 'Customer does not exist.');
					self::set('alert', 'nomargin');
				}
				// Show an alert.
				$this->_action = 'alert';
			} else {
				// Returns an unauthorized access page.
				$this->_action = 'unauthorizedAccess';
			}
		}

		/**
		 * Modifies a customer in the database with the specified new attributes.
		 * 
		 * @param  int    $customer_ID Customer identifier.
		 * @access public
		 */
		public function update($customer_ID = null)
		{
			// Checks if the user has sufficient privileges.
			if (self::isAdmin()) {
				// Only loads the content for this method.
				$this->ajax = true;
				if (isset($_POST['operation'])) {
					// Checks if the specified customer exists.
					if (self::_exists('customer_ID', $customer_ID, true)) {
						$this->Customer->clear();
						// Looks for the customer with that identifier.
						$this->Customer->where('customer_ID', $customer_ID, true);
						$customer = array(
							'customer_username'  => $_POST['customer_username'],
							'customer_password'  => $_POST['customer_password'],
							'customer_firstname' => $_POST['customer_firstname'],
							'customer_lastname'  => $_POST['customer_lastname'],
							'customer_address1'  => $_POST['customer_address1'],
							'customer_address2'  => $_POST['customer_address2'],
							'customer_postcode'  => $_POST['customer_postcode'],
							'customer_phone'     => $_POST['customer_phone'],
							'customer_email'     => $_POST['customer_email']
						);
						// Updates the customer.
						$this->Customer->update($customer);
						// Returns the alert message to be sent to the user.
						self::set('message', 'Customer successfully updated.');
						self::set('alert', 'alert-success nomargin');
					} else {
						// Returns the alert message to be sent to the user.
						self::set('message', 'Customer does not exist.');
						self::set('alert', 'nomargin');
					}
					// Show an alert.
					$this->_action = 'alert';
				// Default action for GET requests.
				} else {
					// Returns the customer's values from the database.
					$customer = self::_getCustomerById($customer_ID);
					self::set('customer_ID', $customer_ID);
					self::set('customer', $customer);
				}
			} else {
				// Returns an unauthorized access page.
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
			// Checks if the customer exists.
			if (self::_exists('customer_ID', $customer_ID, true)) {
				$this->Customer->clear();
				// Looks for the customer with that identifier.
				$this->Customer->where('customer_ID', $customer_ID);
				$this->Customer->select();
				// Returns the results of the customer.
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
			// Checks if not all characters are digits.
			if ($requireInt == true && !ctype_digit($value)) return false;
			$this->Customer->clear();
			// Uses a different table for other controllers.
			if ($customTable != 'customers') $this->Customer->table($customTable);
			if ($requireInt == false) {
				// Looks for a string value in a specified column.
				$this->Customer->where($column, '"' . $value . '"');
			} else {
				// Loooks for an integer value in a specified column.
				$this->Customer->where($column, $value);
			}
			$this->Customer->select();
			// Returns the appropriate value if the element exists or not.
			if ($this->Customer->rowCount() != 0) {
				return true;
			} else {
				return false;
			}
		}

	}

?>