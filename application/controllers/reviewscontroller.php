<?php

	/**
	 * Serves as the controller for all review related functions and
	 * operations. Sends commands to the model to update the model's state.
	 */
	class ReviewsController extends Controller
	{

		/**
		 * Lets the system know which file to be used for the view of the
		 * default page in this controller if an action is not specified.
		 * 
		 * @access public
		 */
		public function defaultPage()
		{
			header('Location: ' . BASE_PATH . '/reviews/getList');
		}

		/**
		 * Returns all reviews in the database.
		 * 
		 * @access public
		 */
		public function getList()
		{
			// Checks if the user has sufficient privileges.
			if (self::isAdmin()) {
				$this->Reviews->clear();
				$this->Reviews->select();
				// Fetches all the reviews.
				$reviews = $this->Reviews->fetch(true);
				// Fetches all the customers and products.
				$customers = self::_getCustomers();
				$products = self::_getProducts();
				self::set('customers', $customers);
				self::set('products', $products);
				self::set('reviews', $reviews);
			} else {
				// Returns an unauthorized access page.
				$this->_action = 'unauthorizedAccess';
			}
		}

		/**
		 * Adds a review into the database.
		 *
		 * @param  int    $product_ID Product identifier.
		 * @access public
		 */
		public function insert($product_ID = null)
		{
			// Checks if the user has sufficient privileges.
			if (self::isCustomer() || self::isAdmin()) {
				// Checks if a product was not supplied.
				if ($product_ID != null) {
					// Checks if the user is a customer.
					if (self::isCustomer()) {
						// Loads the content and the template for this operation.
						$this->ajax = false;
						// Assigns the customer identifier to a variable.
						$customer_ID = $_SESSION['SESS_CUSTOMERID'];
						self::set('product_ID', $product_ID);
					} else {
						// Returns an unauthorized access page.
						$this->_action = 'unauthorizedAccess';
					}
				} else {
					// Only loads the content for this operation.
					$this->ajax = true;
				}
				// Checks if this was a POST request.
				if (isset($_POST['operation'])) {
					// Checks if the user is a customer.
					if (self::isCustomer()) {
						// Assigns the customer and product identifiers.
						$customer_ID = $_SESSION['SESS_CUSTOMERID'];
						$product_ID = $_POST['product_ID'];
					} else {
						// Assigns the customer and product identifiers.
						$customer_ID = $_POST['customer_ID'];
						$product_ID = $_POST['product_ID'];
					}
					// Only loads the content for this operation.
					$this->ajax = true;
					// Checks if the specified product exists.
					if (self::_exists('product_ID', $_POST['product_ID'], true, 'products')) {
						$this->Reviews->clear();
						$review = array(
							'product_ID'         => $_POST['product_ID'],
							'review_subject'     => $_POST['review_subject'],
							'review_description' => $_POST['review_description'],
							'review_rating'      => $_POST['review_rating'],
							'customer_ID'        => $customer_ID
						);
						// Inserts the review into the database.
						$this->Reviews->insert($review);
						// Returns the alert message to be sent to the user.
						self::set('message', 'Review successfully created.');
						self::set('alert', 'alert-success');
					} else {
						// Returns the alert message to be sent to the user.
						self::set('message', 'Product does not exist.');
						self::set('alert', '');
					}
					// Show an alert.
					$this->_action = 'alert';
				} else {
					// Fetches all the customers and products.
					$customers = self::_getCustomers();
					$products = self::_getProducts();
					self::set('customers', $customers);
					self::set('products', $products);
				}
			} else {
				// Returns an unauthorized access page.
				$this->_action = 'unauthorizedAccess';
			}
		}

		/**
		 * Removes a review from the database.
		 * 
		 * @param  int    $review_ID Review identifier.
		 * @access public
		 */
		public function delete($review_ID = null)
		{
			// Checks if the user has sufficient privileges.
			if (self::isAdmin()) {
				// Only loads the content for this method.
				$this->ajax = true;
				// Checks if the specified review exists.
				if (self::_exists('review_ID', $review_ID, true)) {
					$this->Reviews->clear();
					// Looks for the review with that identifier.
					$this->Reviews->where('review_ID', $review_ID);
					// Deletes the review from the database.
					$this->Reviews->delete();
					// Returns the alert message to be sent to the user.
					self::set('message', 'Review successfully deleted.');
					self::set('alert', 'alert-success nomargin');
				} else {
					// Returns the alert message to be sent to the user.
					self::set('message', 'Review does not exist.');
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
		 * Modifies a review in the database with the specified new attributes.
		 *
		 * @param  int    $review_ID Review identifier.
		 * @access public
		 */
		public function update($review_ID = null)
		{
			// Checks if the user has sufficient privileges.
			if (self::isAdmin()) {
				// Only loads the content for this method.
				$this->ajax = true;
				// Checks if this was a POST request.
				if (isset($_POST['operation'])) {
					// Checks if the specified review exists.
					if (self::_exists('review_ID', $review_ID, true)) {
						// Checks if the specified product exists.
						if (self::_exists('product_ID', $_POST['product_ID'], true, 'products')) {
							$this->Reviews->clear();
							// Looks for the review with that identifier.
							$this->Reviews->where('review_ID', $review_ID, true);
							$review = array(
								'product_ID'         => $_POST['product_ID'],
								'review_subject'     => $_POST['review_subject'],
								'review_description' => $_POST['review_description'],
								'review_rating'      => $_POST['review_rating'],
								'customer_ID'        => $customer_ID
							);
							// Updates the review.
							$this->Reviews->update($review);
							// Returns the alert message to be sent to the user.
							self::set('message', 'Review successfully updated.');
							self::set('alert', 'alert-success nomargin');
						} else {
							// Returns the alert message to be sent to the user.
							self::set('message', 'Product does not exist.');
							self::set('alert', 'nomargin');
						}
					} else {
						// Returns the alert message to be sent to the user.
						self::set('message', 'Review does not exist.');
						self::set('alert', 'nomargin');
					}
					// Show an alert.
					$this->_action = 'alert';
				} else {
					// Returns the review's values from the database.
					$review = self::_getReviewById($review_ID);
					// Returns the customers and products from the database.
					$customers = self::_getCustomers();
					$products = self::_getProducts();
					self::set('review_ID', $review_ID);
					self::set('review', $review);
					self::set('customers', $customers);
					self::set('products', $products);
				}
			} else {
				// Returns an unauthorized access page.
				$this->_action = 'unauthorizedAccess';
			}
		}

		/**
		 * Returns review values in a variable.
		 * 
		 * @param  int       $review_ID Review identifier.
		 * @return array                Returns the review values.
		 * @access protected
		 */
		protected function _getReviewById($review_ID = null)
		{
			// Checks if the specified review exists.
			if (self::_exists('review_ID', $review_ID, true)) {
				$this->Reviews->clear();
				// Looks for the review with that identifier.
				$this->Reviews->where('review_ID', $review_ID);
				$this->Reviews->select();
				// Returns the result of the review.
				return $this->Reviews->fetch();
			} else {
				return false;
			}
		}

		/**
		 * Returns customer values in a variable.
		 * 
		 * @return array     Returns the customer values.
		 * @access protected
		 */
		protected function _getCustomers()
		{
			$this->Reviews->clear();
			// Uses the customers table.
			$this->Reviews->table('customers');
			$this->Reviews->select();
			// Returns the results of the customers.
			return $this->Reviews->fetch(true);
		}

		/**
		 * Returns product values in a variable.
		 * 
		 * @return array     Returns the product values.
		 * @access protected
		 */
		protected function _getProducts()
		{
			$this->Reviews->clear();
			// Uses the products table.
			$this->Reviews->table('products');
			$this->Reviews->select();
			// Returns the results of the products.
			return $this->Reviews->fetch(true);
		}

		/**
		 * Checks if a review exists in the database with the given attributes.
		 * 
		 * @param  string    $column      Name of the column to search on.
		 * @param  string    $value       Value to search for.
		 * @param  boolean   $requireInt  Requires the value sent to be an integer.
		 * @param  string    $customTable Uses a table from another controller.
		 * @return boolean                Does the review exist?
		 * @access protected
		 */
		protected function _exists($column = null, $value = null, $requireInt = false, $customTable = 'reviews')
		{
			// Checks if not all characters are digits.
			if ($requireInt == true && !ctype_digit($value)) return false;
			$this->Reviews->clear();
			// Uses a different table for other controllers.
			if ($customTable != 'reviews') $this->Reviews->table($customTable);
			if ($requireInt == false) {
				// Looks for a string value in a specified column.
				$this->Reviews->where($column, '"' . $value . '"');
			} else {
				// Loooks for an integer value in a specified column.
				$this->Reviews->where($column, $value);
			}
			$this->Reviews->select();
			// Returns the appropriate value if the element exists or not.
			if ($this->Reviews->rowCount() != 0) {
				return true;
			} else {
				return false;
			}
		}

	}

?>