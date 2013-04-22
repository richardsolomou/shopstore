<?php

	/**
	 * Serves as the controller for all currency related functions and
	 * operations. Sends commands to the model to update the model's state.
	 */
	class CurrenciesController extends Controller
	{

		/**
		 * Lets the system know which file to be used for the view of the
		 * default page in this controller if an action is not specified.
		 * 
		 * @access public
		 */
		public function defaultPage()
		{
			header('Location: ' . BASE_PATH . '/currencies/getList');
		}

		/**
		 * Returns all currencies in the database.
		 * 
		 * @access public
		 */
		public function getList()
		{
			// Checks if the user has sufficient privileges.
			if (self::isAdmin()) {
				$this->Currencies->clear();
				$this->Currencies->select();
				// Fetches all the categories.
				$currencies = $this->Currencies->fetch(true);
				self::set('currencies', $currencies);
			} else {
				// Returns an unauthorized access page.
				$this->_action = 'unauthorizedAccess';
			}
		}

		/**
		 * Adds a currency into the database.
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
					try {
						$this->Currencies->clear();
						$currency = array(
							'currency_name'   => $_POST['currency_name'],
							'currency_code'   => $_POST['currency_code'],
							'currency_symbol' => $_POST['currency_symbol']
						);
						// Inserts the currency into the database.
						$this->Currencies->insert($currency);
						// Returns the alert message to be sent to the user.
						self::set('message', 'Currency successfully added.');
						self::set('alert', 'alert-success');
					} catch (PDOException $e) {
						// Returns the alert message to be sent to the user.
						self::set('message', 'Currency could not be added.');
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
		 * Removes a currency from the database.
		 * 
		 * @param  int    $currency_ID Currency identifier.
		 * @access public
		 */
		public function delete($currency_ID = null)
		{
			// Checks if the user has sufficient privileges.
			if (self::isAdmin()) {
				// Only loads the content for this method.
				$this->ajax = true;
				// Checks if the currency exists.
				if (self::_exists('currency_ID', $currency_ID, true)) {
					// Checks if the specified currency is not the default.
					$settingsCurrency = self::_getSettingByColumn('currency_ID');
					if ($settingsCurrency['setting_value'] != $currency_ID) {
						$this->Currencies->clear();
						// Looks for the currency with that identifier.
						$this->Currencies->where('currency_ID', $currency_ID);
						// Deletes the currency from the database.
						$this->Currencies->delete();
						// Returns the alert message to be sent to the user.
						self::set('message', 'Currency successfully deleted.');
						self::set('alert', 'alert-success nomargin');
					} else {
						// Returns the alert message to be sent to the user.
						self::set('message', 'Currency is the default currency.');
						self::set('alert', 'nomargin');
					}
				} else {
					// Returns the alert message to be sent to the user.
					self::set('message', 'Currency does not exist.');
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
		 * Modifies a currency in the database with the specified new attributes.
		 * 
		 * @param  int    $currency_ID Currency identifier.
		 * @access public
		 */
		public function update($currency_ID = null)
		{
			// Checks if the user has sufficient privileges.
			if (self::isAdmin()) {
				// Only loads the content for this method.
				$this->ajax = true;
				// Checks if this was a POST request.
				if (isset($_POST['operation'])) {
					// Checks if the specified currency exists.
					if (self::_exists('currency_ID', $currency_ID, true)) {
						// Checks if the code is less than 4 characters.
						if (strlen($_POST['currency_code']) <= 3) {
							$this->Currencies->clear();
							// Looks for the currency with that identifier.
							$this->Currencies->where('currency_ID', $currency_ID, true);
							$currency = array(
								'currency_name'   => $_POST['currency_name'],
								'currency_code'   => $_POST['currency_code'],
								'currency_symbol' => $_POST['currency_symbol']
							);
							// Updates the currency.
							$this->Currencies->update($currency);
							// Returns the alert message to be sent to the user.
							self::set('message', 'Currency successfully updated.');
							self::set('alert', 'alert-success nomargin');
						} else {
							// Returns the alert message to be sent to the user.
							self::set('message', 'Currency code is more than 3 characters.');
							self::set('alert', 'nomargin');
						}
					} else {
						// Returns the alert message to be sent to the user.
						self::set('message', 'Currency does not exist.');
						self::set('alert', 'nomargin');
					}
					// Show an alert.
					$this->_action = 'alert';
				// Default action for GET requests.
				} else {
					// Returns the currency's values from the database.
					$currency = self::_getCurrencyById($currency_ID);
					self::set('currency', $currency);
					self::set('currency_ID', $currency_ID);
				}
			} else {
				// Returns an unauthorized access page.
				$this->_action = 'unauthorizedAccess';
			}
		}

		/**
		 * Returns currency values in a variable.
		 * 
		 * @param  int       $currency_ID Currency identifier.
		 * @return array                  Returns the currency values.
		 * @access protected
		 */
		protected function _getCurrencyById($currency_ID = null)
		{
			// Checks if the currency exists.
			if (self::_exists('currency_ID', $currency_ID, true)) {
				$this->Currencies->clear();
				// Looks for the currency with that identifier.
				$this->Currencies->where('currency_ID', $currency_ID);
				$this->Currencies->select();
				// Returns the results of the currency.
				return $this->Currencies->fetch();
			} else {
				return false;
			}
		}

		/**
		 * Returns a specified setting from the database.
		 *
		 * @param  string    $setting_column Name of the setting's column.
		 * @return array                     Settings of the database.
		 * @access protected
		 */
		protected function _getSettingByColumn($setting_column = null)
		{
			// Checks if the setting column value exists.
			if (self::_exists('setting_column', $setting_column, false, 'settings')) {
				$this->Currencies->clear();
				// Uses the settings table.
				$this->Currencies->table('settings');
				// Looks for the setting column with that value.
				$this->Currencies->where('setting_column', '"' . $setting_column . '"');
				$this->Currencies->select();
				// Returns the result of the setting.
				return $this->Currencies->fetch();
			} else {
				return false;
			}
		}

		/**
		 * Checks if a currency exists in the database with the given attributes.
		 * 
		 * @param  string    $column      Name of the column to search on.
		 * @param  string    $value       Value to search for.
		 * @param  boolean   $requireInt  Requires the value sent to be an integer.
		 * @param  string    $customTable Uses a table from another controller.
		 * @return boolean                Does the product exist?
		 * @access protected
		 */
		protected function _exists($column = null, $value = null, $requireInt = false, $customTable = 'currencies')
		{
			// Checks if not all characters are digits.
			if ($requireInt == true && !ctype_digit($value)) return false;
			$this->Currencies->clear();
			// Uses a different table for other controllers.
			if ($customTable != 'currencies') $this->Currencies->table($customTable);
			if ($requireInt == false) {
				// Looks for a string value in a specified column.
				$this->Currencies->where($column, '"' . $value . '"');
			} else {
				// Loooks for an integer value in a specified column.
				$this->Currencies->where($column, $value);
			}
			$this->Currencies->select();
			// Returns the appropriate value if the element exists or not.
			if ($this->Currencies->rowCount() != 0) {
				return true;
			} else {
				return false;
			}
		}
	}

?>