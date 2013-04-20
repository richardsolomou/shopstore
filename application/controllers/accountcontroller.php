<?php

	/**
	 * Serves as the controller for all account related functions and
	 * operations. Sends commands to the model to update the model's state.
	 */
	class AccountController extends Controller
	{

		/**
		 * Lets the system know which file to be used for the view of the
		 * default page in this controller if an action is not specified.
		 * 
		 * @access public
		 */
		public function defaultPage()
		{
			header('Location: ' . BASE_PATH . '/account/login');
		}

		/**
		 * Logs in the user with the appropriate credentials and permissions.
		 * 
		 * @access public
		 */
		public function login()
		{
			// Only loads the content for this method.
			$this->ajax = true;
			// Checks if this was a POST request.
			if (isset($_POST['operation'])) {
				try {
					$this->Account->clear();
					// Looks for the account with the username and password.
					if ($_POST['admin'] == 0) {
						$this->Account->table('customers');
						$this->Account->where('customer_username', '"' . $_POST['username'] . '"');
						$this->Account->where('customer_password', '"' . $_POST['password'] . '"');
					} else {
						$this->Account->table('administrators');
						$this->Account->where('admin_username', '"' . $_POST['username'] . '"');
						$this->Account->where('admin_password', '"' . $_POST['password'] . '"');
					}
					// Select and fetches the account from the database.
					$this->Account->select();
					$account = $this->Account->fetch();
					// Sets the sessions required if the account was found.
					if ($this->Account->rowCount() != 0) {
						if ($_POST['admin'] == 0) {
							$_SESSION['SESS_LOGGEDIN'] = 1;
							$_SESSION['SESS_CUSTOMERID'] = $account['customer_ID'];
						} else {
							$_SESSION['SESS_ADMINLOGGEDIN'] = 1;
							$_SESSION['SESS_ADMINID'] = $account['admin_ID'];
						}
						// Returns the alert message to be sent to the user.
						self::set('message', 'Successfully logged in.');
						self::set('alert', 'alert-success');
					} else {
						// Returns the alert message to be sent to the user.
						self::set('message', 'Wrong username or password.');
						self::set('alert', 'nomargin');
					}
				} catch (PDOException $e) {
					// Returns the alert message to be sent to the user.
					self::set('message', 'Wrong username or password.');
					self::set('alert', 'nomargin');
				}
			}
			$this->_action = 'alert';
		}

		/**
		 * Logs out the current user.
		 * 
		 * @access public
		 */
		public function logout()
		{
			// Only loads the content for this method.
			$this->ajax = true;
			// Checks if the user has sufficient privileges.
			if (self::isCustomer() || self::isAdmin()) {
				// Unsets the sessions.
				if ($_POST['admin'] == 0) {
					unset($_SESSION['SESS_LOGGEDIN']);
					unset($_SESSION['SESS_CUSTOMERID']);
				} else {
					unset($_SESSION['SESS_ADMINLOGGEDIN']);
					unset($_SESSION['SESS_ADMINID']);
				}
				// Returns the alert message to be sent to the user.
				self::set('message', 'You have logged out.');
				self::set('alert', 'alert-success');
				$this->_action = 'alert';
			} else {
				// Returns an unauthorized access page.
				$this->_alert = 'unauthorizedAccess';
			}
		}

	}

?>