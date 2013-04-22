<?php

	/**
	 * Serves as the controller for all basket related functions and
	 * operations. Sends commands to the model to update the model's state.
	 */
	class BasketController extends Controller
	{

		/**
		 * Lets the system know which file to be used for the view of the
		 * default page in this controller if an action is not specified.
		 * 
		 * @access public
		 */
		public function defaultPage()
		{
			header('Location: ' . BASE_PATH . '/basket/getList');
		}

		/**
		 * Returns all basket items in the database.
		 * 
		 * @access public
		 */
		public function getList()
		{
			// Checks if the user has sufficient privileges.
			if (self::isCustomer()) {
				$this->Basket->clear();
				// Looks up items for this customer.
				$this->Basket->where('customer_ID', $_SESSION['SESS_CUSTOMERID']);
				$this->Basket->select();
				// Fetches all the basket items.
				$basketItems = $this->Basket->fetch(true);
				// Gets the currency ID from the website settings table and the
				// respective symbol from the currencies table.
				$settingsCurrency = self::_getSettingByColumn('currency_ID');
				$currencySymbol = self::_getCurrencyById($settingsCurrency['setting_value']);
				// Gets a list of all the products in the database.
				self::set('products', self::_getProducts());
				self::set('basketItems', $basketItems);
				self::set('currencySymbol', $currencySymbol['currency_symbol']);
			} else {
				// Returns an unauthorized access page.
				$this->_action = 'unauthorizedAccess';
			}
		}

		/**
		 * Adds a basket item into the database.
		 * 
		 * @access public
		 */
		public function insert()
		{
			// Only loads the content for this method.
			$this->ajax = true;
			// Checks if the user has sufficient privileges.
			if (self::isCustomer()) {
				// Checks if this was a POST request.
				if (isset($_POST['operation'])) {
					// Checks if the product exists.
					if (self::_exists('product_ID', $_POST['product_ID'], true, 'products')) {
						// Check if there is enough stock.
						if (self::_checkStock($_POST['product_ID']) >= $_POST['basket_quantity']) {
							// Checks if the customer exists.
							if (self::_exists('customer_ID', $_POST['customer_ID'], true, 'customers')) {
								if (!self::_itemExists($_POST['product_ID'], $_POST['customer_ID'])) {
									$this->Basket->clear();
									$basket = array(
										'basket_quantity' => $_POST['basket_quantity'],
										'product_ID'      => $_POST['product_ID'],
										'customer_ID'     => $_POST['customer_ID']
									);
									// Inserts the basket into the database.
									$this->Basket->insert($basket);
									// Reduce the available stock of the product.
									$lastId = $this->Basket->lastId();
									self::_reduceStock($_POST['product_ID'], $_POST['basket_quantity'], self::_checkStock($_POST['product_ID']));
									// Returns the alert message to be sent to the user.
									self::set('message', 'Basket item successfully added.');
									self::set('alert', 'alert-success');
								} else {
									// Returns the alert message to be sent to the user.
									self::set('message', 'Item already exists in the basket.');
									self::set('alert', '');
								}
							} else {
								// Returns the alert message to be sent to the user.
								self::set('message', 'Customer does not exist.');
								self::set('alert', '');
							}
						} else {
							// Returns the alert message to be sent to the user.
							self::set('message', 'There is not enough stock available for this product.');
							self::set('alert', '');
						}
					} else {
						// Returns the alert message to be sent to the user.
						self::set('message', 'Product does not exist.');
						self::set('alert', '');
					}
				}
			} else {
				// Returns the alert message to be sent to the user.
				self::set('message', 'You do not have sufficient privileges to view the content of this page.');
				self::set('alert', '');
			}
			// Show an alert.
			$this->_action = 'alert';
		}

		/**
		 * Removes a basket item from the database.
		 * 
		 * @param  int    $basket_ID Basket item identifier.
		 * @access public
		 */
		public function delete($basket_ID = null, $admin = null)
		{
			// Only loads the content for this method.
			$this->ajax = true;
			// Checks if the user has sufficient privileges.
			if (self::isCustomer() || $admin != null) {
				// Checks if the basket item exists.
				if (self::_exists('basket_ID', $basket_ID, true)) {
					// Gets the basket item's values for future reference.
					$basketItem = self::_getBasketItemById($basket_ID);
					// Increases the stock since the ordered quantity was deleted.
					self::_increaseStock($basketItem['product_ID'], $basketItem['basket_quantity'], self::_checkStock($basketItem['product_ID']));
					$this->Basket->clear();
					// Looks for the basket item with that identifier.
					$this->Basket->where('basket_ID', $basket_ID);
					// Deletes the basket item from the database.
					$this->Basket->delete();
					// Returns the alert message to be sent to the user.
					self::set('message', 'Basket item successfully deleted.');
					if ($admin == null) {
						self::set('alert', 'alert-success');
					} else {
						self::set('alert', 'alert-success nomargin');
					}
				} else {
					// Returns the alert message to be sent to the user.
					self::set('message', 'Basket item does not exist.');
					if ($admin == null) {
						self::set('alert', '');
					} else {
						self::set('alert', 'nomargin');
					}
				}
			} else {
				// Returns the alert message to be sent to the user.
				self::set('message', 'You do not have sufficient privileges to view the content of this page.');
				if ($admin == null) {
					self::set('alert', '');
				} else {
					self::set('alert', 'nomargin');
				}
			}
			// Show an alert.
			$this->_action = 'alert';
		}

		/**
		 * Modifies a basket item in the database with the specified new attributes.
		 * 
		 * @param  int    $basket_ID Basket item identifier.
		 * @access public
		 */
		public function update($basket_ID = null, $admin = null)
		{
			// Only loads the content for this method.
			$this->ajax = true;
			// Checks if the user has sufficient privileges.
			if (self::isCustomer() || $admin != null) {
				// Checks if this was a POST request.
				if (isset($_POST['operation'])) {
					// Checks if the specified basket item exists.
					if (self::_exists('basket_ID', $basket_ID, true)) {
						// Gets the basket item's values.
						$basketItem = self::_getBasketItemById($basket_ID);
						// Checks if the quantity is zero.
						if ($_POST['basket_quantity'] == 0) {
							// Increases the stock back.
							self::_increaseStock($_POST['product_ID'], $_POST['basket_quantity'], self::_checkStock($_POST['product_ID']));
							// Deletes the basket item.
							self::delete($basket_ID);
							return;
						}
						if ($_POST['basket_quantity'] == $basketItem['basket_quantity'] && $_POST['product_ID'] == $basketItem['product_ID'] && $_POST['customer_ID'] == $basketItem['customer_ID']) {
							// Returns the alert message to be sent to the user.
							self::set('message', 'No changes made.');
							if ($admin == null) {
								self::set('alert', 'alert-info');
							} else {
								self::set('alert', 'alert-info nomargin');
							}
							$this->_action = 'alert';
							return;
						}
						// Checks if the product exists.
						if (self::_exists('product_ID', $_POST['product_ID'], true, 'products')) {
							// Gets the product's current stock.
							$productStock = self::_checkStock($_POST['product_ID']);
							// Check if the stock is zero.
							if ($productStock == 0 && ($_POST['basket_quantity'] > $basketItem['basket_quantity'])) {
								// Returns the alert message to be sent to the user.
								self::set('message', 'There is no more stock available.');
								if ($admin == null) {
									self::set('alert', '');
								} else {
									self::set('alert', 'nomargin');
								}
								$this->_action = 'alert';
								return;
							}
							// Checks if the customer exists.
							if (self::_exists('customer_ID', $_POST['customer_ID'], true, 'customers')) {
								// Check if there is enough stock.
								if (self::_checkStock($_POST['product_ID']) >= ($_POST['basket_quantity'] - $basketItem['basket_quantity'])) {
									$this->Basket->clear();
									// Looks for the basket item with that identifier.
									$this->Basket->where('basket_ID', $basket_ID, true);
									$basket = array(
										'basket_quantity' => $_POST['basket_quantity'],
										'product_ID'      => $_POST['product_ID'],
										'customer_ID'     => $_POST['customer_ID']
									);
									// Updates the basket item.
									$this->Basket->update($basket);
									// Reduce the available stock of the product.
									if ($_POST['basket_quantity'] > $basketItem['basket_quantity']) {
										self::_reduceStock($_POST['product_ID'], ($_POST['basket_quantity'] - $basketItem['basket_quantity']), $productStock);
									} else {
										self::_increaseStock($_POST['product_ID'], ($basketItem['basket_quantity'] - $_POST['basket_quantity']), $productStock);
									}
									// Returns the alert message to be sent to the user.
									self::set('message', 'Basket item successfully updated.');
									if ($admin == null) {
										self::set('alert', 'alert-success');
									} else {
										self::set('alert', 'alert-success nomargin');
									}
								} else {
									// Returns the alert message to be sent to the user.
									self::set('message', 'There is not enough stock available for this product.');
									if ($admin == null) {
										self::set('alert', '');
									} else {
										self::set('alert', 'nomargin');
									}
								}
							} else {
								// Returns the alert message to be sent to the user.
								self::set('message', 'Customer does not exist.');
								if ($admin == null) {
									self::set('alert', '');
								} else {
									self::set('alert', 'nomargin');
								}
							}
						} else {
							// Returns the alert message to be sent to the user.
							self::set('message', 'Product does not exist.');
							if ($admin == null) {
								self::set('alert', '');
							} else {
								self::set('alert', 'nomargin');
							}
						}
					} else {
						// Returns the alert message to be sent to the user.
						self::set('message', 'Basket item does not exist.');
						if ($admin == null) {
							self::set('alert', '');
						} else {
							self::set('alert', 'nomargin');
						}
					}
				// Default action for GET requests.
				} else {
					// Returns the basket item's values from the database.
					$basket = self::_getBasketItemById($basket_ID);
					self::set('basket_ID', $basket_ID);
					self::set('basket', $basket);
					self::set('products', self::_getProducts());
					self::set('customers', self::_getCustomers());
					$this->_action = 'update';
					return;
				}
			} else {
				// Returns the alert message to be sent to the user.
				self::set('message', 'You do not have sufficient privileges to view the content of this page.');
				self::set('alert', '');
			}
			// Show an alert.
			$this->_action = 'alert';
		}

		/**
		 * Adds more quantity to a basket item in the database.
		 * 
		 * @param  int    $basket_ID Basket item identifier.
		 * @access public
		 */
		public function addMore($basket_ID = null)
		{
			// Only loads the content for this method.
			$this->ajax = true;
			// Checks if the user has sufficient privileges.
			if (self::isCustomer()) {
				// Checks if this was a POST request.
				if (isset($_POST['operation'])) {
					// Checks if the specified basket item exists.
					if (self::_exists('basket_ID', $basket_ID, true)) {
						// Gets the basket item's values.
						$basketItem = self::_getBasketItemById($basket_ID);
						// Check if there is enough stock.
						if (self::_checkStock($basketItem['product_ID']) >= $_POST['basket_quantity']) {
							$this->Basket->clear();
							// Looks for the basket item with that identifier.
							$this->Basket->where('basket_ID', $basket_ID, true);
							$newQuantity = $_POST['basket_quantity'] + $basketItem['basket_quantity'];
							$basket = array(
								'basket_quantity' => $newQuantity
							);
							// Updates the basket item.
							$this->Basket->update($basket);
							// Reduce the available stock of the product.
							self::_reduceStock($basketItem['product_ID'], $_POST['basket_quantity'], self::_checkStock($basketItem['product_ID']));
							// Returns the alert message to be sent to the user.
							self::set('message', 'Basket item successfully updated.');
							self::set('alert', 'alert-success');
						} else {
							// Returns the alert message to be sent to the user.
							self::set('message', 'There is not enough stock available for this product.');
							self::set('alert', '');
						}
					} else {
						// Returns the alert message to be sent to the user.
						self::set('message', 'Basket item does not exist.');
						self::set('alert', '');
					}
				}
			} else {
				// Returns the alert message to be sent to the user.
				self::set('message', 'You do not have sufficient privileges to view the content of this page.');
				self::set('alert', '');
			}
			// Show an alert.
			$this->_action = 'alert';
		}

		/**
		 * Returns all basket items in the database.
		 * Does the same thing as getList but is used for AJAX calls instead.
		 * 
		 * @access public
		 */
		public function getTable()
		{
			$this->ajax = true;
			// Checks if the user has sufficient privileges.
			if (self::isCustomer()) {
				$this->Basket->clear();
				// Looks up items for this customer.
				$this->Basket->where('customer_ID', $_SESSION['SESS_CUSTOMERID']);
				$this->Basket->select();
				// Fetches all the basket items.
				$basketItems = $this->Basket->fetch(true);
				// Gets a list of all the products in the database.
				$products = self::_getProducts();
				// Gets the currency ID from the website settings table and the
				// respective symbol from the currencies table.
				$settingsCurrency = self::_getSettingByColumn('currency_ID');
				$currencySymbol = self::_getCurrencyById($settingsCurrency['setting_value']);
				self::set('products', $products);
				self::set('basketItems', $basketItems);
				self::set('currencySymbol', $currencySymbol['currency_symbol']);
			} else {
				// Returns an empty basket page.
				$this->_action = 'empty';
			}
		}

		/**
		 * Returns basket item values in a variable.
		 * 
		 * @param  int       $basket_ID Basket item identifier.
		 * @return array                Returns the basket item values.
		 * @access protected
		 */
		protected function _getBasketItemById($basket_ID = null)
		{
			// Checks if the basket item exists.
			if (self::_exists('basket_ID', $basket_ID, true)) {
				$this->Basket->clear();
				// Looks for the basket item with that identifier.
				$this->Basket->where('basket_ID', $basket_ID);
				$this->Basket->select();
				// Returns the results of the basket item.
				return $this->Basket->fetch();
			} else {
				return false;
			}
		}

		/**
		 * Checks if an item already exists in the basket items table.
		 * 
		 * @param  int     $product_ID  Product identifier.
		 * @param  int     $customer_ID Customer identifier.
		 * @return boolean              Does it exist or not?
		 */
		protected function _itemExists($product_ID = null, $customer_ID = null)
		{
			// Gets all the basket items.
			$basketItems = self::_getBasketItems();
			// Checks if the basket item exists.
			foreach ($basketItems as $item) {
				if ($customer_ID == $item['customer_ID'] && $product_ID == $item['product_ID']) return true;
			}
			return false;
		}

		/**
		 * Gets all basket items from the database.
		 * 
		 * @return array     Returns all basket items.
		 * @access protected
		 */
		protected function _getBasketItems()
		{
			$this->Basket->clear();
			$this->Basket->select();
			// Returns the results of the basket items.
			return $this->Basket->fetch(true);
		}

		/**
		 * Checks how much stock the specified product currently has.
		 * 
		 * @param  int       $product_ID Product identifier.
		 * @return int                   Current stock.
		 * @access protected
		 */
		protected function _checkStock($product_ID = null)
		{
			$this->Basket->clear();
			// Uses the products table.
			$this->Basket->table('products');
			// Looks for the product with that product identifier.
			$this->Basket->where('product_ID', $product_ID);
			$this->Basket->select();
			// Assigns the product values to a variable.
			$product = $this->Basket->fetch();
			return $product['product_stock'];
		}

		/**
		 * Reduces the stock of the selected product.
		 * 
		 * @param  int       $product_ID      Product identifier.
		 * @param  int       $orderedQuantity Quantity ordered by the customer.
		 * @param  int       $currentStock    Currently available stock.
		 * @access protected
		 */
		protected function _reduceStock($product_ID = null, $orderedQuantity = null, $currentStock = null)
		{
			$this->Basket->clear();
			// Uses the products table.
			$this->Basket->table('products');
			// Looks for the product with that product identifier.
			$this->Basket->where('product_ID', $product_ID, true);
			// Reduces the quantity based on how much was ordered.
			$newQuantity = $currentStock - $orderedQuantity;
			$product = array(
				'product_stock' => $newQuantity
			);
			// Updates the product with the new values.
			$this->Basket->update($product);
		}

		protected function _increaseStock($product_ID = null, $orderedQuantity = null, $currentStock = null)
		{
			$this->Basket->clear();
			// Uses the products table.
			$this->Basket->table('products');
			// Looks for the product with that product identifier.
			$this->Basket->where('product_ID', $product_ID, true);
			// Increases the quantity based on how much was previously ordered.
			$newQuantity = $currentStock + $orderedQuantity;
			$product = array(
				'product_stock' => $newQuantity
			);
			// Updates the product with the new values.
			$this->Basket->update($product);
		}

		/**
		 * Returns product values in a variable.
		 * 
		 * @return array     Returns the product values.
		 * @access protected
		 */
		protected function _getProducts()
		{
			$this->Basket->clear();
			// Uses the products table.
			$this->Basket->table('products');
			$this->Basket->select();
			// Returns the results of the products.
			return $this->Basket->fetch(true);
		}

		/**
		 * Returns customer values in a variable.
		 * 
		 * @return array     Returns the customer values.
		 * @access protected
		 */
		protected function _getCustomers()
		{
			$this->Basket->clear();
			// Uses the customers table.
			$this->Basket->table('customers');
			$this->Basket->select();
			// Returns the results of the customers.
			return $this->Basket->fetch(true);
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
				$this->Basket->clear();
				// Uses the settings table.
				$this->Basket->table('settings');
				// Looks for the setting column with that value.
				$this->Basket->where('setting_column', '"' . $setting_column . '"');
				$this->Basket->select();
				// Returns the result of the setting.
				return $this->Basket->fetch();
			} else {
				return false;
			}
		}

		/**
		 * Returns the values of the currency that was selected.
		 * 
		 * @param  int       $currency_ID Currency identifier.
		 * @return string                 Returns the values of the currency.
		 * @access protected
		 */
		protected function _getCurrencyById($currency_ID = null)
		{
			// Checks if the currency exists.
			if (self::_exists('currency_ID', $currency_ID, true, 'currencies')) {
				$this->Basket->clear();
				// Uses the currencies table.
				$this->Basket->table('currencies');
				// Looks for a currency with that identifier.
				$this->Basket->where('currency_ID', $currency_ID);
				$this->Basket->select();
				// Returns the result of that currency.
				return $this->Basket->fetch();
			} else {
				return false;
			}
		}

		/**
		 * Checks if a basket item exists in the database with the given attributes.
		 * 
		 * @param  string    $column      Name of the column to search on.
		 * @param  string    $value       Value to search for.
		 * @param  boolean   $requireInt  Requires the value sent to be an integer.
		 * @param  string    $customTable Uses a table from another controller.
		 * @return boolean                Does the basket item exist?
		 * @access protected
		 */
		protected function _exists($column = null, $value = null, $requireInt = false, $customTable = 'basket')
		{
			// Checks if not all characters are digits.
			if ($requireInt == true && !ctype_digit($value)) return false;
			$this->Basket->clear();
			// Uses a different table for other controllers.
			if ($customTable != 'basket') $this->Basket->table($customTable);
			if ($requireInt == false) {
				// Looks for a string value in a specified column.
				$this->Basket->where($column, '"' . $value . '"');
			} else {
				// Loooks for an integer value in a specified column.
				$this->Basket->where($column, $value);
			}
			$this->Basket->select();
			// Returns the appropriate value if the element exists or not.
			if ($this->Basket->rowCount() != 0) {
				return true;
			} else {
				return false;
			}
		}

	}

?>