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
		 * @return array  Currencies in the database.
		 * @access public
		 */
		public function getList()
		{
			if (self::isAdmin()) {
				$this->Currency->clear();
				$this->Currency->select();
				$currencies = $this->Currency->fetch(true);
				self::set('currencies', $currencies);
			} else {
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
			if (self::isAdmin()) {
				// Only loads the content for this method.
				$this->ajax = true;
				// Checks if this was a POST request.
				if (isset($_POST['operation'])) {
					try {
						$this->Currency->clear();
						$currency = array(
							'currency_name' => $_POST['currency_name'],
							'currency_code' => $_POST['currency_code'],
							'currency_symbol' => $_POST['currency_symbol']
						);
						$this->Currency->insert($currency);
						self::set('insert', $currency);
						// Sets the value and class of the alert to be shown.
						self::set('message', 'Currency successfully inserted.');
						self::set('alert', 'alert-success nomargin');
						return true;
					} catch (PDOException $e) {
						self::set('message', 'Currency could not be inserted.');
						self::set('alert', '');
						return false;
					}
				}
			} else {
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
			if (self::isAdmin()) {
				$this->ajax = true;
				if (self::_exists('currency_ID', $currency_ID, true) && self::_getDefaultCurrency() != $currency_ID) {
					$this->Currency->clear();
					$this->Currency->where('currency_ID', $currency_ID);
					$this->Currency->delete();
					self::set('delete', true);
					self::set('message', 'Currency successfully deleted.');
					self::set('alert', 'alert-success nomargin');
					return true;
				} else {
					self::set('message', 'Currency does not exist, or is the default currency.');
					self::set('alert', '');
					return false;
				}
			} else {
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
			if (self::isAdmin()) {
				$this->ajax = true;
				if (isset($_POST['operation'])) {
					if (self::_exists('currency_ID', $currency_ID, true)) {
						$this->Currency->clear();
						$this->Currency->where('currency_ID', $currency_ID, true);
						$currency = array(
							'currency_name' => $_POST['currency_name'],
							'currency_code' => $_POST['currency_code'],
							'currency_symbol' => $_POST['currency_symbol']
						);
						$this->Currency->update($currency);
						self::set('update', $currency);
						self::set('message', 'Currency successfully updated.');
						self::set('alert', 'alert-success nomargin');
						return true;
					} else {
						self::set('message', 'Currency does not exist.');
						self::set('alert', '');
						return false;
					}
				} else {
					$currency = self::_getCurrencyById($currency_ID);
					self::set('currency', $currency);
					self::set('currency_ID', $currency_ID);
				}
			} else {
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
			if (self::_exists('currency_ID', $currency_ID, true)) {
				$this->Currency->clear();
				$this->Currency->where('currency_ID', $currency_ID);
				$this->Currency->select();
				return $this->Currency->fetch();
			} else {
				return false;
			}
		}

		/**
		 * Returns all the settings of the database.
		 *
		 * @param  string    $setting_column Name of the setting's column.
		 * @return array                     Settings of the database.
		 * @access protected
		 */
		protected function _getSettingByColumn($setting_column = null)
		{
			if (self::_exists('setting_column', $setting_column, false, 'settings')) {
				$this->Currency->clear();
				$this->Currency->table('settings');
				$this->Currency->where('setting_column', '"' . $setting_column . '"');
				$this->Currency->select();
				return $this->Currency->fetch();
			} else {
				return false;
			}
		}

		/**
		 * Gets the default currency used in settings.
		 *
		 * @return string    Default currency identifier.
		 * @access protected
		 */
		protected function _getDefaultCurrency()
		{
			$settingsCurrency = self::_getSettingByColumn('currency_ID');
			return $settingsCurrency['setting_value'];
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
			// Checks if all characters are digits.
			if ($requireInt == true && !ctype_digit($value)) return false;
			$this->Currency->clear();
			// Uses a different table for other controllers.
			if ($customTable != 'currencies') $this->Currency->table($customTable);
			if ($requireInt == false) {
				$this->Currency->where($column, '"' . $value . '"');
			} else {
				$this->Currency->where($column, $value);
			}
			$this->Currency->select();
			if ($this->Currency->rowCount() != 0) {
				return true;
			} else {
				return false;
			}
		}
	}

?>