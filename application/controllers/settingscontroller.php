<?php

	/**
	 * Serves as the controller for all setting related functions and
	 * operations. Sends commands to the model to update the model's state.
	 */
	class SettingsController extends Controller
	{

		/**
		 * Lets the system know which file to be used for the view of the
		 * default page in this controller if an action is not specified.
		 * 
		 * @access public
		 */
		public function defaultPage()
		{
			header('Location: ' . BASE_PATH . '/settings/getList');
		}

		/**
		 * Returns all settings in the database.
		 * 
		 * @access public
		 */
		public function getList()
		{
			// Checks if the user has sufficient privileges.
			if (self::isAdmin()) {
				$this->Settings->clear();
				$this->Settings->select();
				// Fetches all the settings.
				$settings = $this->Settings->fetch(true);
				// Fetches all the currencies.
				$currencies = self::_getCurrencies();
				self::set('settings', $settings);
				self::set('currencies', $currencies);
			} else {
				// Returns an unauthorized access page.
				$this->_action = 'unauthorizedAccess';
			}
		}

		/**
		 * Modifies a setting in the database with the specified new attributes.
		 * 
		 * @param  int    $setting_ID Setting identifier.
		 * @access public
		 */
		public function update($setting_ID = null)
		{
			// Checks if the user has sufficient privileges.
			if (self::isAdmin()) {
				// Only loads the content for this method.
				$this->ajax = true;
				// Checks if this was a POST request.
				if (isset($_POST['operation'])) {
					// Checks if the setting exists.
					if (self::_exists('setting_ID', $setting_ID, true)) {
						$this->Settings->clear();
						// Looks for the setting with that identifier.
						$this->Settings->where('setting_ID', $setting_ID, true);
						$setting = array(
							'setting_column' => $_POST['setting_column'],
							'setting_value'  => $_POST['setting_value']
						);
						// Updates the setting.
						$this->Settings->update($setting);
						// Returns the alert message to be sent to the user.
						self::set('message', 'Setting successfully updated.');
						self::set('alert', 'alert-success nomargin');
					} else {
						// Returns the alert message to be sent to the user.
						self::set('message', 'Setting does not exist.');
						self::set('alert', 'nomargin');
					}
					// Show an alert.
					$this->_action = 'alert';
				// Default action for GET requests.
				} else {
					// Fetches the currencies.
					$currencies = self::_getCurrencies();
					// Returns the setting's values from the database.
					$setting = self::_getSettingById($setting_ID);
					// Returns the currency used in the settings.
					$currency_ID = self::_getSettingByColumn('currency_ID');
					self::set('currency_ID', $currency_ID['setting_value']);
					self::set('currencies', $currencies);
					self::set('setting', $setting);
					self::set('setting_ID', $setting_ID);
				}
			} else {
				// Returns an unauthorized access page.
				$this->_action = 'unauthorizedAccess';
			}
		}

		/**
		 * Resets the database to the default settings.
		 * 
		 * @access public
		 */
		public function resetDatabase()
		{
			if (isset($_POST['operation'])) {
				$products = self::_getProducts();
	            foreach ($products as $product) {
	            	$imageGlobal = glob(strtolower(SERVER_ROOT . '/templates/img/products/' . $product['product_ID'] . '.' . '*'));
					foreach ($imageGlobal as $image) {
						unlink($image);
					}
	            }
	            // Unsets any sessions.
	            unset($_SESSION['SESS_ADMINID']);
	            unset($_SESSION['SESS_ADMINLOGGEDIN']);
	            unset($_SESSION['SESS_LOGGEDIN']);
				unset($_SESSION['SESS_CUSTOMERID']);
	            // Creates a configuration array with the content to include
	        	// in the configuration file to be created.
	            $config[] = '<?php' . PHP_EOL;
	            $config[] = '    /**';
	            $config[] = '     * Set the under-development environment variable.';
	            $config[] = '     */';
	            $config[] = '    define (\'DEVELOPMENT_ENVIRONMENT\', true);' . PHP_EOL;
	            $config[] = '    /**';
	            $config[] = '     * Set configuration variables.';
	            $config[] = '     */';
	            $config[] = '    define(\'DB_HOST\', \'\');';
	            $config[] = '    define(\'DB_USER\', \'\');';
	            $config[] = '    define(\'DB_PASS\', \'\');';
	            $config[] = '    define(\'DB_NAME\', \'\');' . PHP_EOL;
	            $config[] = '    /**';
	            $config[] = '     * Automatically set by the installer.';
	            $config[] = '     */';
	            $config[] = '    define(\'DB_SETUP\', false);' . PHP_EOL;
	            $config[] = '?>';
	            // Puts the contents in the configuration file.
	            file_put_contents(SERVER_ROOT . '/library/config.php', implode(PHP_EOL, $config));
	            // Sends the user to the installer
            	header('Location: ' . BASE_PATH . '/installer');
            }
		}

		/**
		 * Returns product values in a variable.
		 * 
		 * @return array     Returns the product values.
		 * @access protected
		 */
		protected function _getProducts()
		{
			$this->Settings->clear();
			// Uses the products table.
			$this->Settings->table('products');
			$this->Settings->select();
			// Returns the result of the products.
			return $this->Settings->fetch(true);
		}

		/**
		 * Returns currency values in a variable.
		 * 
		 * @return array     Returns the currency values.
		 * @access protected
		 */
		protected function _getCurrencies()
		{
			$this->Settings->clear();
			// Uses the currencies table.
			$this->Settings->table('currencies');
			$this->Settings->select();
			// Returns the results of the currencies.
			return $this->Settings->fetch(true);
		}

		/**
		 * Returns setting values in a variable based on the identifier provided.
		 * 
		 * @param  int       $setting_ID Setting identifier.
		 * @return array                 Returns the setting values.
		 * @access protected
		 */
		protected function _getSettingById($setting_ID = null)
		{
			// Checks if the setting exists.
			if (self::_exists('setting_ID', $setting_ID, true)) {
				$this->Settings->clear();
				// Looks for the setting with that identifier.
				$this->Settings->where('setting_ID', $setting_ID);
				$this->Settings->select();
				// Returns the results of the setting.
				return $this->Settings->fetch();
			} else {
				return false;
			}
		}

		/**
		 * Returns setting values in a variable based on the column provided.
		 *
		 * @param  string    $setting_column Name of the setting's column.
		 * @return array                     Returns the setting values.
		 * @access protected
		 */
		protected function _getSettingByColumn($setting_column = null)
		{
			// Checks if the setting column value exists.
			if (self::_exists('setting_column', $setting_column, false)) {
				$this->Settings->clear();
				// Looks for the setting with that value.
				$this->Settings->where('setting_column', '"' . $setting_column . '"');
				$this->Settings->select();
				// Returns the results of the setting.
				return $this->Settings->fetch();
			} else {
				return false;
			}
		}

		/**
		 * Checks if a setting exists in the database with the given attributes.
		 * 
		 * @param  string    $column      Name of the column to search on.
		 * @param  string    $value       Value to search for.
		 * @param  boolean   $requireInt  Requires the value sent to be an integer.
		 * @param  string    $customTable Uses a table from another controller.
		 * @return boolean                Does the product exist?
		 * @access protected
		 */
		protected function _exists($column = null, $value = null, $requireInt = false, $customTable = 'settings')
		{
			// Checks if not all characters are digits.
			if ($requireInt == true && !ctype_digit($value)) return false;
			$this->Settings->clear();
			// Uses a different table for other controllers.
			if ($customTable != 'settings') $this->Settings->table($customTable);
			if ($requireInt == false) {
				// Looks for a string value in a specified column.
				$this->Settings->where($column, '"' . $value . '"');
			} else {
				// Loooks for an integer value in a specified column.
				$this->Settings->where($column, $value);
			}
			$this->Settings->select();
			// Returns the appropriate value if the element exists or not.
			if ($this->Settings->rowCount() != 0) {
				return true;
			} else {
				return false;
			}
		}
	}

?>