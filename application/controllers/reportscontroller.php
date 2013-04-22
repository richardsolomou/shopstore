<?php

	/**
	 * Serves as the controller for all reports related functions and
	 * operations. Sends commands to the model to update the model's state.
	 */
	class ReportsController extends Controller
	{

		/**
		 * Lets the system know which file to be used for the view of the
		 * default page in this controller if an action is not specified.
		 * 
		 * @access public
		 */
		public function defaultPage()
		{
			header('Location: ' . BASE_PATH . '/reports/getList');
		}

		/**
		 * Returns all reports in the database.
		 * 
		 * @access public
		 */
		public function getList()
		{
			// Checks if the user has sufficient privileges.
			if (self::isAdmin()) {
				// Fetches the number of pending orders.
				self::set('numberOfPendingOrders', self::_getElementRowCount('basket'));
				// Fetches the number of products.
				self::set('numberOfProducts', self::_getElementRowCount('products'));
				// Fetches the number of reviews.
				self::set('numberOfReviews', self::_getElementRowCount('reviews'));
				// Fetches the number of customers.
				self::set('numberOfCustomers', self::_getElementRowCount('customers'));
				// Fetches the number of customers.
				self::set('numberOfOrders', self::_getElementRowCount('orders'));
				// Fetches all the completed orders.
				self::set('orders', self::_getElementRows('orders'));
				// Fetches all the customers.
				self::set('customers', self::_getElementRows('customers'));
				// Fetches all the products and the ones that are low on stock.
				$products = self::_getElementRows('products');
				self::set('products', $products);
				self::set('productsLowOnStock', self::_getProductsLowOnStock($products));
			} else {
				// Returns an unauthorized access page.
				$this->_action = 'unauthorizedAccess';
			}
		}

		/**
		 * Returns the rows of an element in a variable.
		 *
		 * @param  string    $element Table name and element controller.
		 * @return array              Returns the values.
		 * @access protected
		 */
		protected function _getElementRows($element = null)
		{
			$this->Reports->clear();
			// Uses the specified element table.
			$this->Reports->table($element);
			$this->Reports->select();
			// Returns the results of the specified element.
			return $this->Reports->fetch(true);
		}

		/**
		 * Returns the number of rows of an element in a variable.
		 *
		 * @param  string    $element Table name and element controller.
		 * @return array              Returns the values.
		 * @access protected
		 */
		protected function _getElementRowCount($element = null)
		{
			$this->Reports->clear();
			// Uses the specified element table.
			$this->Reports->table($element);
			$this->Reports->select();
			// Returns the results of the specified element.
			return $this->Reports->rowCount();
		}

		/**
		 * Returns products that have lower than 10 stock left.
		 * 
		 * @param  array     $products All products in the database.
		 * @return array               Products low on stock.
		 * @access protected
		 */
		protected function _getProductsLowOnStock($products = array())
		{
			if ($products != array()) {
				$this->Reports->clear();
				$this->Reports->table('products');
				foreach ($products as $product) $this->Reports->where('product_stock', 15, false, '<');
				$this->Reports->select();
				return $this->Reports->fetch(true);
			} else {
				return false;
			}
		}

	}

?>