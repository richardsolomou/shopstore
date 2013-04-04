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
			$this->_action = 'getList';
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
			if (self::_exists('product_ID', $product_ID, true)) {
				// Gets the values of the product and its category.
				$product = self::_getProductById($product_ID);
				$productCategory = self::_getCatById($product['category_ID']);
				// Gets the currency ID from the website settings table.
				$settingsCurrency = self::_getSettings('\'currency_ID\'');
				// And gets the symbol from the ID of the currency.
				$currencySymbol = self::_getCurrencyById($settingsCurrency['setting_value']);
				// Gets the amount of reviews for the specific product.
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
				$this->_action = 'error';
				return false;
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
			$this->Product->clear();
			$this->Product->select();
			return $this->Product->fetch(true);
		}

		/**
		 * Adds a product into the database.
		 * 
		 * @param  int    $category_ID         Category the product belongs to.
		 * @param  string $product_name        Name of the product.
		 * @param  string $product_description Summary of the product.
		 * @param  string $product_condition   Product condition (new, used, etc.)
		 * @param  float  $product_price       Price of the product.
		 * @param  int    $product_stock       Available stock of the product.
		 * @param  string $product_image       Path to the image of the product.
		 * @access public
		 */
		public function insert($category_ID = null, $product_name = null, $product_description = null, $product_condition = null, $product_price = null, $product_stock = null, $product_image = null)
		{
			if (self::_exists('category_ID', $category_ID, true, 'categories')) {
				$this->Product->clear();
				$product = array(
					'category_ID' => $category_ID,
					'product_name' => $product_name,
					'product_description' => $product_description,
					'product_condition' => $product_condition,
					'product_price' => $product_price,
					'product_stock' => $product_stock,
					'product_image' => $product_image
				);
				$this->Product->insert($product);
				self::set('insert', $product);
			} else {
				return false;
			}
		}

		/**
		 * Removes a product from the database.
		 * 
		 * @param  int    $product ID Product identifier.
		 * @access public
		 */
		public function delete($product_ID = null)
		{
			if (self::_exists('product_ID', $product_ID, true)) {
				$this->Product->clear();
				$this->Product->where('product_ID', $product_ID);
				$this->Product->delete();
				self::set('delete', true);
			} else {
				return false;
			}
		}

		/**
		 * Modifies a product in the database with the specified new attributes.
		 * 
		 * @param  int    $product_ID          Product identifier.
		 * @param  int    $category_ID         Category the product belongs to.
		 * @param  string $product_name        Name of the product.
		 * @param  string $product_description Summary of the product.
		 * @param  string $product_condition   Product condition (new, used, etc.)
		 * @param  float  $product_price       Price of the product.
		 * @param  int    $product_stock       Available stock of the product.
		 * @param  string $product_image       Path to the image of the product.
		 * @access public
		 */
		public function update($product_ID = null, $category_ID = null, $product_name = null, $product_description = null, $product_condition = null, $product_price = null, $product_stock = null, $product_image = null)
		{
			if (self::_exists('product_ID', $product_ID, true) && self::_exists('category_ID', $category_ID, true, 'categories')) {
				$this->Product->clear();
				$this->Product->where('product_ID', $product_ID);
				$product = array(
					'category_ID' => $category_ID,
					'product_name' => $product_name,
					'product_description' => $product_description,
					'product_condition' => $product_condition,
					'product_price' => $product_price,
					'product_stock' => $product_stock,
					'product_image' => $product_image
				);
				$this->Product->update($product);
				self::set('update', $product);
			} else {
				return false;
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
			if (self::_exists('product_ID', $product_ID, true, 'products')) {
				$this->Product->clear();
				$this->Product->where('product_ID', $product_ID);
				$this->Product->select();
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
			if (self::_exists('category_ID', $category_ID, true, 'categories')) {
				$this->Product->clear();
				$this->Product->table('categories');
				$this->Product->where('category_ID', $category_ID);
				$this->Product->select();
				return $this->Product->fetch();
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
		protected function _getSettings($setting_column = null)
		{
			if (self::_exists('setting_column', $setting_column, false, 'settings')) {
				$this->Product->clear();
				$this->Product->table('settings');
				$this->Product->where('setting_column', $setting_column);
				$this->Product->select();
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
			if (self::_exists('currency_ID', $currency_ID, true, 'currencies')) {
				$this->Product->clear();
				$this->Product->table('currencies');
				$this->Product->where('currency_ID', $currency_ID);
				$this->Product->select();
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
			if (self::_exists('product_ID', $product_ID, true)) {
				$this->Product->clear();
				$this->Product->table('reviews');
				$this->Product->where('product_ID', $product_ID);
				$this->Product->select();
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
			if (self::_exists('product_ID', $product_ID, true)) {
				$this->Product->clear();
				$this->Product->table('reviews');
				$this->Product->where('product_ID', $product_ID);
				$this->Product->select();
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
			// Checks if all characters are digits.
			if ($requireInt == true && !ctype_digit($value)) return false;
			$this->Product->clear();
			// Uses a different table for other controllers.
			if ($customTable != 'products') $this->Product->table($customTable);
			$this->Product->where($column, $value);
			$this->Product->select();
			if ($this->Product->rowCount() != 0) {
				return true;
			} else {
				return false;
			}
		}
	}

?>