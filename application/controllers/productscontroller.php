<?php

	/**
	 * Serves as the controller for all product related functions and
	 * operations. Sends commands to the model to update the model's state.
	 */
	class ProductsController extends Controller
	{

		/**
		 * Lets the system know which file to be used for the view of the
		 * default page in this controller if an action is not specified.
		 * 
		 * @access public
		 */
		public function defaultPage()
		{
			header('Location: ' . BASE_PATH . '/products/getList');
		}

		/**
		 * Searches the database for a product with the given ID and returns
		 * the results of that product as well as the currency symbol in the view
		 * presentation.
		 * 
		 * @param  int    $product_ID Product identifier.
		 * @access public
		 */
		public function getById($product_ID = null)
		{
			// Checks if the specified product exists.
			if (self::_exists('product_ID', $product_ID, true)) {
				// Gets the values of the product and its category.
				$product = self::_getProductById($product_ID);
				$productCategory = self::_getCatById($product['category_ID']);
				// Gets the currency ID from the website settings table and the
				// respective symbol from the currencies table.
				$settingsCurrency = self::_getSettingByColumn('currency_ID');
				$currencySymbol = self::_getCurrencyById($settingsCurrency['setting_value']);
				// Gets the reviews and the number of reviews for the product.
				$reviews = self::_getProductReviews($product_ID);
				$reviewNumber = self::_getProductReviewNumber($product_ID);
				// Calculates the average rating based on all reviews.
				$reviewRatingAverage = 0;
		        if ($reviewNumber > 0) foreach ($reviews as $individualReview) $reviewRatingAverage += $individualReview['review_rating'] / $reviewNumber;
		        // Creates an instance of the customers controller to fetch
		        // details for the customers in the reviews.
		        $customerDispatch = new CustomersController('customers', '_getCustomerById');
				self::set('product', $product);
				self::set('reviews', $reviews);
				self::set('reviewNumber', $reviewNumber);
				self::set('individualReview', $individualReview);
				self::set('reviewRatingAverage', $reviewRatingAverage);
				self::set('customerDispatch', $customerDispatch);
				self::set('productCategory', $productCategory['category_name']);
				self::set('currencySymbol', $currencySymbol['currency_symbol']);
			} else {
				// Returns a 404 error page.
				notFound();
			}
		}

		/**
		 * Returns all products in the database.
		 * 
		 * @return array  Products in the database.
		 * @access public
		 */
		public function getList()
		{
			// Checks if the user has sufficient privileges.
			if (self::isAdmin()) {
				$this->Product->clear();
				$this->Product->select();
				// Fetches all the products.
				$products = $this->Product->fetch(true);
				self::set('products', $products);
			} else {
				// Returns an unauthorized access page.
				$this->_action = 'unauthorizedAccess';
			}
		}

		/**
		 * Adds a product into the database.
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
					// Checks if the specified category exists.
					if (self::_exists('category_ID', $_POST['category_ID'], true, 'categories')) {
						$this->Product->clear();
						$product = array(
							'category_ID' => $_POST['category_ID'],
							'product_name' => $_POST['product_name'],
							'product_description' => $_POST['product_description'],
							'product_condition' => $_POST['product_condition'],
							'product_price' => $_POST['product_price'],
							'product_stock' => $_POST['product_stock'],
							'product_image' => $_POST['product_image']
						);
						// Inserts the product into the database.
						$this->Product->insert($product);
						// Returns the alert message to be sent to the user.
						self::set('message', 'Product successfully inserted.');
						self::set('alert', 'alert-success nomargin');
					} else {
						// Returns the alert message to be sent to the user.
						self::set('message', 'Category does not exist.');
						self::set('alert', '');
					}
				// Default action for GET requests.
				} else {
					// Gets the currency ID from the website settings table and
					// the respective symbol from the currencies table.
					$settingsCurrency = self::_getSettingByColumn('currency_ID');
					$currencySymbol = self::_getCurrencyById($settingsCurrency['setting_value']);
					self::set('currencySymbol', $currencySymbol['currency_symbol']);
				}
			} else {
				// Returns an unauthorized access page.
				$this->_action = 'unauthorizedAccess';
			}
		}

		/**
		 * Removes a product from the database.
		 * 
		 * @param  int    $product_ID Product identifier.
		 * @access public
		 */
		public function delete($product_ID = null)
		{
			// Checks if the user has sufficient privileges.
			if (self::isAdmin()) {
				// Only loads the content for this method.
				$this->ajax = true;
				// Checks if the specified product exists.
				if (self::_exists('product_ID', $product_ID, true)) {
					$this->Product->clear();
					// Looks for the product with that identifier.
					$this->Product->where('product_ID', $product_ID);
					// Deletes the product from the database.
					$this->Product->delete();
					// Returns the alert message to be sent to the user.
					self::set('message', 'Product successfully deleted.');
					self::set('alert', 'alert-success nomargin');
				} else {
					// Returns the alert message to be sent to the user.
					self::set('message', 'Product does not exist.');
					self::set('alert', '');
				}
			} else {
				// Returns an unauthorized access page.
				$this->_action = 'unauthorizedAccess';
			}
		}

		/**
		 * Modifies a product in the database with the specified new attributes.
		 * 
		 * @param  int    $product_ID          Product identifier.
		 * @access public
		 */
		public function update($product_ID = null)
		{
			// Checks if the user has sufficient privileges.
			if (self::isAdmin()) {
				// Only loads the content for this method.
				$this->ajax = true;
				// Checks if this was a POST request.
				if (isset($_POST['operation'])) {
					// Checks if the specified product exists.
					if (self::_exists('product_ID', $product_ID, true)) {
						// Checks if the specified category exists.
						if (self::_exists('category_ID', $_POST['category_ID'], true, 'categories')) {
							$this->Product->clear();
							// Looks for the product with that identifier.
							$this->Product->where('product_ID', $product_ID, true);
							$product = array(
								'category_ID' => $_POST['category_ID'],
								'product_name' => $_POST['product_name'],
								'product_description' => $_POST['product_description'],
								'product_condition' => $_POST['product_condition'],
								'product_price' => $_POST['product_price'],
								'product_stock' => $_POST['product_stock'],
								'product_image' => $_POST['product_image']
							);
							// Updates the product.
							$this->Product->update($product);
							// Returns the alert message to be sent to the user.
							self::set('message', 'Product successfully updated.');
							self::set('alert', 'alert-success nomargin');
							return true;
						} else {
							// Returns the alert message to be sent to the user.
							self::set('message', 'Category does not exist.');
							self::set('alert', '');
						}
					} else {
						// Returns the alert message to be sent to the user.
						self::set('message', 'Product does not exist.');
						self::set('alert', '');
					}
				// Default action for GET requests.
				} else {
					// Returns the product's values from the database.
					$product = self::_getProductById($product_ID);
					// Gets the currency ID from the website settings table and
					// the respective symbol from the currencies table.
					$settingsCurrency = self::_getSettingByColumn('currency_ID');
					$currencySymbol = self::_getCurrencyById($settingsCurrency['setting_value']);
					self::set('currencySymbol', $currencySymbol['currency_symbol']);
					self::set('product', $product);
					self::set('product_ID', $product_ID);
				}
			} else {
				// Returns an unauthorized access page.
				$this->_action = 'unauthorizedAccess';
			}
		}

		/**
		 * Returns product values in a variable.
		 * 
		 * @param  int       $product_ID Product identifier.
		 * @return array                 Returns the product values.
		 * @access protected
		 */
		protected function _getProductById($product_ID = null)
		{
			// Checks if the product exists.
			if (self::_exists('product_ID', $product_ID, true)) {
				$this->Product->clear();
				// Looks for the product with that identifier.
				$this->Product->where('product_ID', $product_ID);
				$this->Product->select();
				// Returns the result of the product.
				return $this->Product->fetch();
			} else {
				return false;
			}
		}

		/**
		 * Returns category values in a variable.
		 * 
		 * @param  int       $category_ID Category identifier.
		 * @return array                  Returns the category values.
		 * @access protected
		 */
		protected function _getCatById($category_ID = null)
		{
			// Checks if the specified category exists.
			if (self::_exists('category_ID', $category_ID, true, 'categories')) {
				$this->Product->clear();
				// Uses the categories table.
				$this->Product->table('categories');
				// Looks for the category with that identifier.
				$this->Product->where('category_ID', $category_ID);
				$this->Product->select();
				// Returns the result of the category.
				return $this->Product->fetch();
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
				$this->Product->clear();
				// Uses the settings table.
				$this->Product->table('settings');
				// Looks for the setting column with that value.
				$this->Product->where('setting_column', '"' . $setting_column . '"');
				$this->Product->select();
				// Returns the result of the setting.
				return $this->Product->fetch();
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
				$this->Product->clear();
				// Uses the currencies table.
				$this->Product->table('currencies');
				// Looks for a currency with that identifier.
				$this->Product->where('currency_ID', $currency_ID);
				$this->Product->select();
				// Returns the result of that currency.
				return $this->Product->fetch();
			} else {
				return false;
			}
		}

		/**
		 * Returns the reviews of the product selected.
		 * 
		 * @param  int       $product_ID Product identifier.
		 * @return string                Returns the reviews of the product.
		 * @access protected
		 */
		protected function _getProductReviews($product_ID = null)
		{
			// Checks if the product exists.
			if (self::_exists('product_ID', $product_ID, true)) {
				$this->Product->clear();
				// Uses the reviews table
				$this->Product->table('reviews');
				// Looks for a review with that product identifier
				$this->Product->where('product_ID', $product_ID);
				$this->Product->select();
				// Returns the results of the reviews.
				return $this->Product->fetch(true);
			} else {
				return false;
			}
		}

		/**
		 * Returns the number of reviews of the product selected.
		 * 
		 * @param  int       $product_ID Product identifier.
		 * @return string                Returns the reviews of the product.
		 * @access protected
		 */
		protected function _getProductReviewNumber($product_ID = null)
		{
			// Checks if the product exists.
			if (self::_exists('product_ID', $product_ID, true)) {
				$this->Product->clear();
				// Uses the reviews table.
				$this->Product->table('reviews');
				// Looks for a review with that product identifier.
				$this->Product->where('product_ID', $product_ID);
				$this->Product->select();
				// Returns the number of results of reviews.
				return $this->Product->rowCount();
			} else {
				return false;
			}
		}

		/**
		 * Checks if a product exists in the database with the given attributes.
		 * 
		 * @param  string    $column      Name of the column to search on.
		 * @param  string    $value       Value to search for.
		 * @param  boolean   $requireInt  Requires the value sent to be an integer.
		 * @param  string    $customTable Uses a table from another controller.
		 * @return boolean                Does the product exist?
		 * @access protected
		 */
		protected function _exists($column = null, $value = null, $requireInt = false, $customTable = 'products')
		{
			// Checks if not all characters are digits.
			if ($requireInt == true && !ctype_digit($value)) return false;
			$this->Product->clear();
			// Uses a different table for other controllers.
			if ($customTable != 'products') $this->Product->table($customTable);
			if ($requireInt == false) {
				// Looks for a string value in a specified column.
				$this->Product->where($column, '"' . $value . '"');
			} else {
				// Loooks for an integer value in a specified column.
				$this->Product->where($column, $value);
			}
			$this->Product->select();
			// Returns the appropriate value if the element exists or not.
			if ($this->Product->rowCount() != 0) {
				return true;
			} else {
				return false;
			}
		}
	}

?>