<?php

	/**
	 * Serves as the controller for all currency related functions and
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
		 * @return array  Currencies in the database.
		 * @access public
		 */
		public function getList()
		{
			if (self::isAdmin()) {
				$this->Setting->clear();
				$this->Setting->select();
				$settings = $this->Setting->fetch(true);
				$currencies = self::_getCurrencies();
				self::set('settings', $settings);
				self::set('currencies', $currencies);
			} else {
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
			if (self::isAdmin()) {
				$this->ajax = true;
				if (isset($_POST['operation'])) {
					if (self::_exists('setting_ID', $setting_ID, true)) {
						$this->Setting->clear();
						$this->Setting->where('setting_ID', $setting_ID, true);
						$setting = array(
							'setting_column' => $_POST['setting_column'],
							'setting_value' => $_POST['setting_value']
						);
						$this->Setting->update($setting);
						self::set('update', $setting);
						self::set('message', 'Setting successfully updated.');
						self::set('alert', 'alert-success nomargin');
						return true;
					} else {
						self::set('message', 'Setting does not exist.');
						self::set('alert', '');
						return false;
					}
				} else {
					$currencies = self::_getCurrencies();
					$setting = self::_getSettingById($setting_ID);
					$currency_ID = self::_getDefaultCurrency();
					self::set('currency_ID', $currency_ID);
					self::set('currencies', $currencies);
					self::set('setting', $setting);
					self::set('setting_ID', $setting_ID);
				}
			} else {
				$this->_action = 'unauthorizedAccess';
			}
		}

		/**
		 * Returns currency values in a variable.
		 * 
		 * @return array                  Returns the currency values.
		 * @access protected
		 */
		protected function _getCurrencies()
		{
			$this->Setting->clear();
			$this->Setting->table('currencies');
			$this->Setting->select();
			return $this->Setting->fetch(true);
		}

		/**
		 * Returns setting values in a variable.
		 * 
		 * @param  int       $setting_ID Setting identifier.
		 * @return array                 Returns the setting values.
		 * @access protected
		 */
		protected function _getSettingById($setting_ID = null)
		{
			if (self::_exists('setting_ID', $setting_ID, true)) {
				$this->Setting->clear();
				$this->Setting->where('setting_ID', $setting_ID);
				$this->Setting->select();
				return $this->Setting->fetch();
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
			if (self::_exists('setting_column', $setting_column, false)) {
				$this->Setting->clear();
				$this->Setting->where('setting_column', '"' . $setting_column . '"');
				$this->Setting->select();
				return $this->Setting->fetch();
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
			// Checks if all characters are digits.
			if ($requireInt == true && !ctype_digit($value)) return false;
			$this->Setting->clear();
			// Uses a different table for other controllers.
			if ($customTable != 'settings') $this->Setting->table($customTable);
			if ($requireInt == false) {
				$this->Setting->where($column, '"' . $value . '"');
			} else {
				$this->Setting->where($column, $value);
			}
			$this->Setting->select();
			if ($this->Setting->rowCount() != 0) {
				return true;
			} else {
				return false;
			}
		}
	}

?>