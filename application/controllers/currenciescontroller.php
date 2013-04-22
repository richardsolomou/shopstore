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
				if (self::_exists('Currencies', 'currency_ID', $currency_ID, true)) {
					// Checks if the specified currency is not the default.
					$settingsCurrency = self::_getSettingByColumn('Currencies', 'currency_ID');
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
					if (self::_exists('Currencies', 'currency_ID', $currency_ID, true)) {
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
					$currency = self::_getCurrencyById('Currencies', $currency_ID);
					self::set('currency', $currency);
					self::set('currency_ID', $currency_ID);
				}
			} else {
				// Returns an unauthorized access page.
				$this->_action = 'unauthorizedAccess';
			}
		}

	}

?>