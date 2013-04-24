<?php

	/**
	 * Serves as the controller for all order related functions and
	 * operations. Sends commands to the model to update the model's state.
	 */
	class OrdersController extends Controller
	{

		/**
		 * Lets the system know which file to be used for the view of the
		 * default page in this controller if an action is not specified.
		 * 
		 * @access public
		 */
		public function defaultPage()
		{
			header('Location: ' . BASE_PATH . '/orders/getList');
		}

		/**
		 * Returns all orders in the database.
		 * 
		 * @access public
		 */
		public function getList()
		{
			// Checks if the user has sufficient privileges.
			if (self::isAdmin()) {
				$this->Orders->clear();
				$this->Orders->select();
				// Fetches all the orders.
				$orders = $this->Orders->fetch(true);
				self::set('pendingOrders', self::_getPendingOrders());
				self::set('customers', self::_getCustomers());
				self::set('orders', $orders);
			} else {
				// Returns an unauthorized access page.
				$this->_action = 'unauthorizedAccess';
			}
		}

		/**
		 * Adds an order into the database.
		 * 
		 * @access public
		 */
		public function insert()
		{
			// Checks if the user has sufficient privileges.
			if (self::isCustomer() || self::isAdmin()) {
				// Only loads the content for this method.
				$this->ajax = true;
				// Checks if this was a POST request.
				if (isset($_POST['operation'])) {
					// Checks if the specified customer exists.
					if (self::_exists('Orders', 'customer_ID', $_POST['customer_ID'], true, 'customers')) {
						$this->Orders->clear();
						$order = array(
							'order_total'  => $_POST['order_total'],
							'customer_ID'  => $_POST['customer_ID']
						);
						// Inserts the order into the database.
						$this->Orders->insert($order);
						// Gets the order identifier of this request.
						$order_ID = $this->Orders->lastId();
						// Assigns the basket items to the order.
						$basketItems = self::_getBasketItemsByCustomer($_POST['customer_ID']);
						self::_assignToOrder($basketItems, $order_ID);
						// Returns the alert message to be sent to the user.
						self::set('message', 'Order successfully placed.');
						self::set('alert', 'alert-success');
					} else {
						// Returns the alert message to be sent to the user.
						self::set('message', 'Customer does not exist.');
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
		 * Removes an order from the database.
		 * 
		 * @param  int    $order_ID Order identifier.
		 * @access public
		 */
		public function delete($order_ID = null)
		{
			// Checks if the user has sufficient privileges.
			if (self::isAdmin()) {
				// Only loads the content for this method.
				$this->ajax = true;
				// Checks if the order exists.
				if (self::_exists('Orders', 'order_ID', $order_ID, true)) {
					// Deletes all ordered items for this order.
					self::_deleteFromItems($order_ID);
					$this->Orders->clear();
					// Looks for the order with that identifier.
					$this->Orders->where('order_ID', $order_ID);
					// Deletes the order from the database.
					$this->Orders->delete();
					// Returns the alert message to be sent to the user.
					self::set('message', 'Order successfully deleted.');
					self::set('alert', 'alert-success nomargin');
				} else {
					// Returns the alert message to be sent to the user.
					self::set('message', 'Order does not exist.');
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
		 * Modifies an order in the database with the specified new attributes.
		 * 
		 * @param  int    $order_ID Order identifier.
		 * @access public
		 */
		public function update($order_ID = null)
		{
			// Checks if the user has sufficient privileges.
			if (self::isAdmin()) {
				// Only loads the content for this method.
				$this->ajax = true;
				// Checks if this was a POST request.
				if (isset($_POST['operation'])) {
					// Checks if the specified order exists.
					if (self::_exists('Orders', 'order_ID', $order_ID, true)) {
						// Checks if the specified customer exists.
						if (self::_exists('Orders', 'customer_ID', $_POST['customer_ID'], true, 'customers')) {
							$this->Orders->clear();
							// Looks for the order with that identifier.
							$this->Orders->where('order_ID', $order_ID, true);
							$order = array(
								'order_total'  => $_POST['order_total'],
								'customer_ID'  => $_POST['customer_ID']
							);
							// Updates the order.
							$this->Orders->update($order);
							// Returns the alert message to be sent to the user.
							self::set('message', 'Order successfully updated.');
							self::set('alert', 'alert-success nomargin');
						} else {
							// Returns the alert message to be sent to the user.
							self::set('message', 'Customer does not exist.');
							self::set('alert', 'nomargin');
						}
					} else {
						// Returns the alert message to be sent to the user.
						self::set('message', 'Order does not exist.');
						self::set('alert', 'nomargin');
					}
					// Show an alert.
					$this->_action = 'alert';
				// Default action for GET requests.
				} else {
					// Returns the order's values from the database.
					self::set('order_ID', $order_ID);
					self::set('order', self::_getOrderById($order_ID));
					self::set('customers', self::_getCustomers());
					self::set('orderedProducts', self::_getItemsByOrder($order_ID));
				}
			} else {
				// Returns an unauthorized access page.
				$this->_action = 'unauthorizedAccess';
			}
		}

		/**
		 * Returns order values in a variable.
		 * 
		 * @param  int       $order_ID Order identifier.
		 * @return array               Returns the order values.
		 * @access protected
		 */
		protected function _getOrderById($order_ID = null)
		{
			// Checks if the order exists.
			if (self::_exists('Orders', 'order_ID', $order_ID, true)) {
				$this->Orders->clear();
				// Looks for the order with that identifier.
				$this->Orders->where('order_ID', $order_ID);
				$this->Orders->select();
				// Returns the results of the order.
				return $this->Orders->fetch();
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
			$this->Orders->clear();
			// Uses the customers table.
			$this->Orders->table('customers');
			$this->Orders->select();
			// Returns the results of the customers.
			return $this->Orders->fetch(true);
		}

		/**
		 * Returns all pending orders in the basket.
		 * 
		 * @return array     Pending orders.
		 * @access protected
		 */
		protected function _getPendingOrders()
		{
			$this->Orders->clear();
			// Uses the basket table.
			$this->Orders->table('basket');
			$this->Orders->select();
			// Returns all pending orders.
			return $this->Orders->fetch(true);
		}

		/**
		 * Returns ordered items related to this order.
		 * 
		 * @param  int       $order_ID Order identifier.
		 * @return array               Returns the ordered items.
		 * @access protected
		 */
		protected function _getItemsByOrder($order_ID = null)
		{
			// Checks if the order exists.
			if (self::_exists('Orders', 'order_ID', $order_ID, true)) {
				$this->Orders->clear();
				// Uses the basket table.
				$this->Orders->table('items');
				// Looks for the order with that identifier.
				$this->Orders->where('order_ID', $order_ID);
				$this->Orders->select();
				// Returns the results of the order.
				return $this->Orders->fetch(true);
			} else {
				return false;
			}
		}

		/**
		 * Assigns basket items to the order.
		 * 
		 * @param  array     $items    Basket items array.
		 * @param  int       $order_ID Order identifier.
		 * @access protected
		 */
		protected function _assignToOrder($basketItems = array(), $order_ID = null)
		{
			// Checks if the order exists.
			if (self::_exists('Orders', 'order_ID', $order_ID, true)) {
				$this->Orders->clear();
				// Uses the basket table.
				$this->Orders->table('basket');
				// Looks for all basket items with that customer identifier.
				$items = array();
				foreach ($basketItems as $basketItem) {
					$this->Orders->where('customer_ID', $basketItem['customer_ID'], true);
					$newItem = array(
						'item_quantity' => $basketItem['basket_quantity'],
						'product_ID'    => $basketItem['product_ID'],
						'customer_ID'   => $basketItem['customer_ID'],
						'order_ID'      => $order_ID
					);
					array_push($items, $newItem);
				}
				// Deletes the basket items.
				$this->Orders->delete();
				$this->Orders->clear();
				// Uses the items table.
				$this->Orders->table('items');
				// Inserts the new items.
				foreach ($items as $item) $this->Orders->insert($item);
			}
		}

		/**
		 * Returns basket items related to this customer.
		 * 
		 * @param  int       $customer_ID Customer identifier.
		 * @return array                  Returns the basket items.
		 * @access protected
		 */
		protected function _getBasketItemsByCustomer($customer_ID = null)
		{
			// Checks if the order exists.
			if (self::_exists('Orders', 'customer_ID', $customer_ID, true, 'customers')) {
				$this->Orders->clear();
				// Uses the basket table.
				$this->Orders->table('basket');
				// Looks for the order with that identifier.
				$this->Orders->where('customer_ID', $customer_ID);
				$this->Orders->select();
				// Returns the results of the order.
				return $this->Orders->fetch(true);
			} else {
				return false;
			}
		}

		/**
		 * Deletes the ordered items for the specified order.
		 * 
		 * @param  int       $order_ID Order identifier.
		 * @access protected
		 */
		protected function _deleteFromItems($order_ID = null)
		{
			// Checks if the order exists.
			if (self::_exists('Orders', 'order_ID', $order_ID, true)) {
				$this->Orders->clear();
				// Uses the basket table.
				$this->Orders->table('items');
				// Looks for a basket item with that order identifier.
				$this->Orders->where('order_ID', $order_ID);
				// Deletes the basket item.
				$this->Orders->delete();
			}
		}

	}

?>