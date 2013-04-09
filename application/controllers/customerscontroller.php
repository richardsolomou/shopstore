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
		 * @return array  Customers in the database.
		 * @access public
		 */
		public function getList()
		{
			if (self::isAdmin()) {
				$this->Customer->clear();
				$this->Customer->select();
				$customers = $this->Customer->fetch(true);
				self::set('customers', $customers);
			} else {
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
			if (self::isAdmin()) {
				// Only loads the content for this method.
				$this->ajax = true;
				// Checks if this was a POST request.
				if (isset($_POST['operation'])) {
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
						$this->Customer->insert($customer);
						self::set('insert', $customer);
						// Sets the value and class of the alert to be shown.
						self::set('message', 'Customer successfully inserted.');
						self::set('alert', 'alert-success nomargin');
						return true;
					} else {
						self::set('message', 'Customer username already exists.');
						self::set('alert', '');
						return false;
					}
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
				$this->ajax = true;
				if (self::_exists('customer_ID', $customer_ID, true)) {
					$this->Customer->clear();
					$this->Customer->where('customer_ID', $customer_ID);
					$this->Customer->delete();
					self::set('delete', true);
					self::set('message', 'Customer successfully deleted.');
					self::set('alert', 'alert-success nomargin');
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
		 * @param  int    $customer_ID Customer identifier.
		 * @access public
		 */
		public function update($customer_ID = null)
		{
			if (self::isAdmin()) {
				$this->ajax = true;
				if (isset($_POST['operation'])) {
					if (self::_exists('customer_ID', $customer_ID, true)) {
						$this->Customer->clear();
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
						$this->Customer->update($customer);
						self::set('update', $customer);
						self::set('message', 'Customer successfully updated.');
						self::set('alert', 'alert-success nomargin');
						return true;
					} else {
						self::set('message', 'Customer does not exist.');
						self::set('alert', '');
						return false;
					}
				} else {
					$customer = self::_getCustomerById($customer_ID);
					self::set('customer_ID', $customer_ID);
					self::set('customer', $customer);
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
			if ($requireInt == false) {
				$this->Customer->where($column, '"' . $value . '"');
			} else {
				$this->Customer->where($column, $value);
			}
			$this->Customer->select();
			if ($this->Customer->rowCount() != 0) {
				return true;
			} else {
				return false;
			}
		}

	}

?>