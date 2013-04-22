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
				$this->Customers->clear();
				$this->Customers->select();
				// Fetches all the customers.
				$customers = $this->Customers->fetch(true);
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
					if (!self::_exists('Customers', 'customer_username', $_POST['customer_username'])) {
						$this->Customers->clear();
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
						$this->Customers->insert($customer);
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
		 * Allows a user to register as a customer the database.
		 * 
		 * @access public
		 */
		public function add()
		{
			// Checks if the user has sufficient privileges.
			if (!self::isCustomer()) {
				// Checks if this was a POST request.
				if (isset($_POST['operation'])) {
					// Only loads the content for this method.
					$this->ajax = true;
					// Checks if the username is not taken.
					if (!self::_exists('Customers', 'customer_username', $_POST['customer_username'])) {
						$this->Customers->clear();
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
						$this->Customers->insert($customer);
						// Sets the customer session variables.
						$_SESSION['SESS_LOGGEDIN'] = 1;
						$_SESSION['SESS_CUSTOMERID'] = $this->Customers->lastId();
						// Returns the alert message to be sent to the user.
						self::set('message', 'Customer successfully created.');
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
				if (self::_exists('Customers', 'customer_ID', $customer_ID, true)) {
					// Checks if the customer is currently logged in.
					if (!isset($_SESSION['SESS_CUSTOMERID']) || ($_SESSION['SESS_CUSTOMERID'] != $customer_ID)) {
						// Deletes all reviews from this customer.
						self::_deleteReviews($customer_ID);
						// Deletes all basket item records from this customer.
						self::_deleteFromBasket($customer_ID);
						// Deletes all ordered items from this customer.
						self::_deleteFromItems($customer_ID);
						$this->Customers->clear();
						// Looks for the customer with this identifier.
						$this->Customers->where('customer_ID', $customer_ID);
						// Deletes the customer from the database.
						$this->Customers->delete();
						// Returns the alert message to be sent to the user.
						self::set('message', 'Customer successfully deleted.');
						self::set('alert', 'alert-success nomargin');
					} else {
						// Returns the alert message to be sent to the user.
						self::set('message', 'Customer is currently logged in.');
						self::set('alert', 'nomargin');
					}
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
				// Checks if this was a POST request.
				if (isset($_POST['operation'])) {
					// Checks if the specified customer exists.
					if (self::_exists('Customers', 'customer_ID', $customer_ID, true)) {
						$this->Customers->clear();
						// Looks for the customer with that identifier.
						$this->Customers->where('customer_ID', $customer_ID, true);
						$customer = array(
							'customer_username'  => $_POST['customer_username'],
							'customer_firstname' => $_POST['customer_firstname'],
							'customer_lastname'  => $_POST['customer_lastname'],
							'customer_address1'  => $_POST['customer_address1'],
							'customer_address2'  => $_POST['customer_address2'],
							'customer_postcode'  => $_POST['customer_postcode'],
							'customer_phone'     => $_POST['customer_phone'],
							'customer_email'     => $_POST['customer_email']
						);
						if ($_POST['customer_password'] != '') {
							$passwordArray = array('customer_password' => $_POST['customer_password']);
							$customer = array_merge($customer, $passwordArray);
						}
						// Updates the customer.
						$this->Customers->update($customer);
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
			if (self::_exists('Customers', 'customer_ID', $customer_ID, true)) {
				$this->Customers->clear();
				// Looks for the customer with that identifier.
				$this->Customers->where('customer_ID', $customer_ID);
				$this->Customers->select();
				// Returns the results of the customer.
				return $this->Customers->fetch();
			} else {
				return false;
			}
		}

		/**
		 * Deletes the reviews from the specified customer.
		 * 
		 * @param  int       $customer_ID Customer identifier.
		 * @access protected
		 */
		protected function _deleteReviews($customer_ID = null)
		{
			// Checks if the customer exists.
			if (self::_exists('Customers', 'customer_ID', $customer_ID, true)) {
				$this->Customers->clear();
				// Uses the reviews table.
				$this->Customers->table('reviews');
				// Looks for a review with that customer identifier.
				$this->Customers->where('customer_ID', $customer_ID);
				// Deletes the reviews.
				$this->Customers->delete();
			}
		}

		/**
		 * Deletes the specified customer's basket items.
		 * 
		 * @param  int       $customer_ID Customer identifier.
		 * @access protected
		 */
		protected function _deleteFromBasket($customer_ID = null)
		{
			// Checks if the customer exists.
			if (self::_exists('Customers', 'customer_ID', $customer_ID, true)) {
				$this->Customers->clear();
				// Uses the basket table.
				$this->Customers->table('basket');
				// Looks for a basket item with that customer identifier.
				$this->Customers->where('customer_ID', $customer_ID);
				// Deletes the basket item.
				$this->Customers->delete();
			}
		}

		/**
		 * Deletes the specified customer's ordered items.
		 * 
		 * @param  int       $customer_ID Customer identifier.
		 * @access protected
		 */
		protected function _deleteFromItems($customer_ID = null)
		{
			// Checks if the customer exists.
			if (self::_exists('Customers', 'customer_ID', $customer_ID, true)) {
				$this->Customers->clear();
				// Uses the basket table.
				$this->Customers->table('items');
				// Looks for a basket item with that customer identifier.
				$this->Customers->where('customer_ID', $customer_ID);
				// Deletes the basket item.
				$this->Customers->delete();
			}
		}

	}

?>