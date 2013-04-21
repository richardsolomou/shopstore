<?php

	/**
	 * Serves as the controller for all administrator related functions and
	 * operations. Sends commands to the model to update the model's state.
	 */
	class AdministratorsController extends Controller
	{

		/**
		 * Lets the system know which file to be used for the view of the
		 * default page in this controller if an action is not specified.
		 * 
		 * @access public
		 */
		public function defaultPage()
		{
			header('Location: ' . BASE_PATH . '/administrators/getList');
		}

		/**
		 * Returns all administrators in the database.
		 * 
		 * @access public
		 */
		public function getList()
		{
			// Checks if the user has sufficient privileges.
			if (self::isAdmin()) {
				$this->Administrators->clear();
				$this->Administrators->select();
				// Fetches all the administrators.
				$administrators = $this->Administrators->fetch(true);
				self::set('administrators', $administrators);
			} else {
				// Returns an unauthorized access page.
				$this->_action = 'unauthorizedAccess';
			}
		}

		/**
		 * Adds an administrator into the database.
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
					if (!self::_exists('admin_username', $_POST['admin_username'])) {
						$this->Administrators->clear();
						$administrator = array(
							'admin_username'  => $_POST['admin_username'],
							'admin_password'  => $_POST['admin_password'],
							'admin_firstname' => $_POST['admin_firstname'],
							'admin_lastname'  => $_POST['admin_lastname'],
							'admin_email'     => $_POST['admin_email']
						);
						// Inserts the administrator into the database.
						$this->Administrators->insert($administrator);
						// Returns the alert message to be sent to the user.
						self::set('message', 'Administrator successfully inserted.');
						self::set('alert', 'alert-success');
					} else {
						// Returns the alert message to be sent to the user.
						self::set('message', 'Administrator username already exists.');
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
		 * Removes an administrator from the database.
		 * 
		 * @param  int    $admin_ID Administrator identifier.
		 * @access public
		 */
		public function delete($admin_ID = null)
		{
			// Checks if the user has sufficient privileges.
			if (self::isAdmin()) {
				// Only loads the content for this method.
				$this->ajax = true;
				// Checks if the administrator exists.
				if (self::_exists('admin_ID', $admin_ID, true)) {
					// Checks if the administrator is currently logged in.
					if ($_SESSION['SESS_ADMINID'] != $admin_ID) {
						$this->Administrators->clear();
						// Looks for the administrator with that identifier.
						$this->Administrators->where('admin_ID', $admin_ID);
						// Deletes the administrator from the database.
						$this->Administrators->delete();
						// Returns the alert message to be sent to the user.
						self::set('message', 'Administrator successfully deleted.');
						self::set('alert', 'alert-success nomargin');
					} else {
						// Returns the alert message to be sent to the user.
						self::set('message', 'Administrator is currently logged in.');
						self::set('alert', 'nomargin');
					}
				} else {
					// Returns the alert message to be sent to the user.
					self::set('message', 'Administrator does not exist.');
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
		 * Modifies an administrator in the database with the specified new attributes.
		 * 
		 * @param  int    $admin_ID Administrator identifier.
		 * @access public
		 */
		public function update($admin_ID = null)
		{
			// Checks if the user has sufficient privileges.
			if (self::isAdmin()) {
				// Only loads the content for this method.
				$this->ajax = true;
				// Checks if this was a POST request.
				if (isset($_POST['operation'])) {
					// Checks if the specified administrator exists.
					if (self::_exists('admin_ID', $admin_ID, true)) {
						$this->Administrators->clear();
						// Looks for the administrator with that identifier.
						$this->Administrators->where('admin_ID', $admin_ID, true);
						$administrator = array(
							'admin_username'  => $_POST['admin_username'],
							'admin_firstname' => $_POST['admin_firstname'],
							'admin_lastname'  => $_POST['admin_lastname'],
							'admin_email'     => $_POST['admin_email']
						);
						if ($_POST['admin_password'] != '') {
							$passwordArray = array('admin_password' => $_POST['admin_password']);
							$administrator = array_merge($administrator, $passwordArray);
						}
						// Updates the administrator.
						$this->Administrators->update($administrator);
						// Returns the alert message to be sent to the user.
						self::set('message', 'Administrator successfully updated.');
						self::set('alert', 'alert-success nomargin');
					} else {
						// Returns the alert message to be sent to the user.
						self::set('message', 'Administrator does not exist.');
						self::set('alert', 'nomargin');
					}
					// Show an alert.
					$this->_action = 'alert';
				// Default action for GET requests.
				} else {
					// Returns the administrator's values from the database.
					$administrator = self::_getAdminById($admin_ID);
					self::set('admin_ID', $admin_ID);
					self::set('administrator', $administrator);
				}
			} else {
				// Returns an unauthorized access page.
				$this->_action = 'unauthorizedAccess';
			}
		}

		/**
		 * Returns administrator values in a variable.
		 * 
		 * @param  int       $admin_ID Administrator identifier.
		 * @return array               Returns the administrator values.
		 * @access protected
		 */
		protected function _getAdminById($admin_ID = null)
		{
			// Checks if the administrator exists.
			if (self::_exists('admin_ID', $admin_ID, true)) {
				$this->Administrators->clear();
				// Looks for the administrator with that identifier.
				$this->Administrators->where('admin_ID', $admin_ID);
				$this->Administrators->select();
				// Returns the results of the administrator.
				return $this->Administrators->fetch();
			} else {
				return false;
			}
		}

		/**
		 * Checks if an administrator exists in the database with the given attributes.
		 * 
		 * @param  string    $column      Name of the column to search on.
		 * @param  string    $value       Value to search for.
		 * @param  boolean   $requireInt  Requires the value sent to be an integer.
		 * @param  string    $customTable Uses a table from another controller.
		 * @return boolean                Does the administrator exist?
		 * @access protected
		 */
		protected function _exists($column = null, $value = null, $requireInt = false, $customTable = 'administrators')
		{
			// Checks if not all characters are digits.
			if ($requireInt == true && !ctype_digit($value)) return false;
			$this->Administrators->clear();
			// Uses a different table for other controllers.
			if ($customTable != 'administrators') $this->Administrators->table($customTable);
			if ($requireInt == false) {
				// Looks for a string value in a specified column.
				$this->Administrators->where($column, '"' . $value . '"');
			} else {
				// Loooks for an integer value in a specified column.
				$this->Administrators->where($column, $value);
			}
			$this->Administrators->select();
			// Returns the appropriate value if the element exists or not.
			if ($this->Administrators->rowCount() != 0) {
				return true;
			} else {
				return false;
			}
		}

	}

?>