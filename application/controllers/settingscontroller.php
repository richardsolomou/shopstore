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
				self::set('settings', $settings);
			} else {
				$this->_action = 'unauthorizedAccess';
			}
		}

		/**
		 * Adds a setting into the database.
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
						$this->Setting->clear();
						$setting = array(
							'setting_column' => $_POST['setting_column'],
							'setting_value' => $_POST['setting_value']
						);
						$this->Setting->insert($setting);
						self::set('insert', $setting);
						// Sets the value and class of the alert to be shown.
						self::set('message', 'Setting successfully inserted.');
						self::set('alert', 'alert-success nomargin');
						return true;
					} catch (PDOException $e) {
						self::set('message', 'Setting could not be inserted.');
						self::set('alert', '');
						return false;
					}
				}
			} else {
				$this->_action = 'unauthorizedAccess';
			}
		}

		/**
		 * Removes a setting from the database.
		 * 
		 * @param  int    $setting_ID Setting identifier.
		 * @access public
		 */
		public function delete($setting_ID = null)
		{
			if (self::isAdmin()) {
				$this->ajax = true;
				if (self::_exists('setting_ID', $setting_ID, true)) {
					$this->Setting->clear();
					$this->Setting->where('setting_ID', $setting_ID);
					$this->Setting->delete();
					self::set('delete', true);
					self::set('message', 'Setting successfully deleted.');
					self::set('alert', 'alert-success nomargin');
					return true;
				} else {
					self::set('message', 'Setting does not exist.');
					self::set('alert', '');
					return false;
				}
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
					$setting = self::_getSettingById($setting_ID);
					self::set('setting', $setting);
					self::set('setting_ID', $setting_ID);
				}
			} else {
				$this->_action = 'unauthorizedAccess';
			}
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