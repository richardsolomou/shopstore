<?php

	/**
	 * Serves as the controller for all category related functions and
	 * operations. Sends commands to the model to update the model's state.
	 */
	class CategoriesController extends Controller
	{

		/**
		 * Lets the system know which file to be used for the view of the
		 * default page in this controller if an action is not specified.
		 * 
		 * @access public
		 */
		public function defaultPage()
		{
			header('Location: ' . BASE_PATH . '/categories/getList');
		}

		/**
		 * Searches the database for a category with the given ID and returns
		 * the results of that category as well as products under it to the view
		 * presentation.
		 * 
		 * @param  int    $category_ID Category identifier.
		 * @access public
		 */
		public function getById($category_ID = null)
		{
			// Checks if the specified category exists.
			if (self::_exists('Categories', 'category_ID', $category_ID, true)) {
				// Gets the values of the category.
				$category = self::_getCatById($category_ID);
				// Gets the products under the category.
				$productsByCat = self::_getProductsByCat($category_ID);
				self::set('category', $category);
				self::set('productsByCat', $productsByCat);
			} else {
				// Returns a 404 error page.
				notFound();
			}
		}

		/**
		 * Returns all categories in the database.
		 * 
		 * @access public
		 */
		public function getList()
		{
			// Checks if the user has sufficient privileges.
			if (self::isAdmin()) {
				$this->Categories->clear();
				$this->Categories->select();
				// Fetches all the categories.
				$categories = $this->Categories->fetch(true);
				// Calls another instance of this class to get the number of
				// products under each category.
				$categoryDispatch = new CategoriesController('categories', '_getProductCountByCat');
				self::set('categoryDispatch', $categoryDispatch);
				self::set('categories1', $categories);
				self::set('categories2', $categories);
			} else {
				// Returns an unauthorized access page.
				$this->_action = 'unauthorizedAccess';
			}
		}

		/**
		 * Adds a category into the database.
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
					// Checks if the specified category parent exists.
					if (self::_exists('Categories', 'category_ID', $_POST['category_parent_ID'], true)) {
						$this->Categories->clear();
						$category = array(
							'category_name'      => $_POST['category_name'],
							'category_parent_ID' => $_POST['category_parent_ID']
						);
						// Inserts the category into the database.
						$this->Categories->insert($category);
						// Returns the alert message to be sent to the user.
						self::set('message', 'Category successfully created.');
						self::set('alert', 'alert-success');
					} else {
						// Returns the alert message to be sent to the user.
						self::set('message', 'Category parent does not exist.');
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
		 * Removes a category from the database.
		 * 
		 * @param  int    $category_ID Category identifier.
		 * @access public
		 */
		public function delete($category_ID = null)
		{
			// Checks if the user has sufficient privileges.
			if (self::isAdmin()) {
				// Only loads the content for this method.
				$this->ajax = true;
				// Checks if the specified category exists.
				if (self::_exists('Categories', 'category_ID', $category_ID, true)) {
					if (self::_getProductCountByCat($category_ID) == 0) {
						$this->Categories->clear();
						// Looks for the category with that identifier.
						$this->Categories->where('category_ID', $category_ID);
						// Deletes the category from the database.
						$this->Categories->delete();
						// Returns the alert message to be sent to the user.
						self::set('message', 'Category successfully deleted.');
						self::set('alert', 'alert-success nomargin');
					} else {
						// Returns the alert message to be sent to the user.
						self::set('message', 'Category has products under it.');
						self::set('alert', 'nomargin');
					}
				} else {
					// Returns the alert message to be sent to the user.
					self::set('message', 'Category does not exist.');
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
		 * Modifies a category in the database with the specified new attributes.
		 *
		 * @param  int    $category_ID Category identifier.
		 * @access public
		 */
		public function update($category_ID = null)
		{
			// Checks if the user has sufficient privileges.
			if (self::isAdmin()) {
				// Only loads the content for this method.
				$this->ajax = true;
				// Checks if this was a POST request.
				if (isset($_POST['operation'])) {
					// Checks if the specified category exists.
					if (self::_exists('Categories', 'category_ID', $category_ID, true)) {
						// Checks if the specified category parent exists.
						if (self::_exists('Categories', 'category_ID', $_POST['category_parent_ID'], true)) {
							// Checks if the specified category parent is not the
							// same as the current category.
							if ($_POST['category_parent_ID'] != $category_ID) {
								$this->Categories->clear();
								// Looks for the category with that identifier.
								$this->Categories->where('category_ID', $category_ID, true);
								$category = array(
									'category_name'      => $_POST['category_name'],
									'category_parent_ID' => $_POST['category_parent_ID']
								);
								// Updates the category.
								$this->Categories->update($category);
								// Returns the alert message to be sent to the user.
								self::set('message', 'Category successfully updated.');
								self::set('alert', 'alert-success nomargin');
							} else {
								// Returns the alert message to be sent to the user.
								self::set('message', 'Category is the same as the parent.');
								self::set('alert', 'nomargin');
							}
						} else {
							// Returns the alert message to be sent to the user.
							self::set('message', 'Category parent does not exist.');
							self::set('alert', 'nomargin');
						}
					} else {
						// Returns the alert message to be sent to the user.
						self::set('message', 'Category does not exist.');
						self::set('alert', 'nomargin');
					}
					// Show an alert.
					$this->_action = 'alert';
				} else {
					// Returns the category's values from the database.
					$category = self::_getCatById($category_ID);
					self::set('category_ID', $category_ID);
					self::set('category', $category);
				}
			} else {
				// Returns an unauthorized access page.
				$this->_action = 'unauthorizedAccess';
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
			if (self::_exists('Categories', 'category_ID', $category_ID, true)) {
				$this->Categories->clear();
				// Looks for the category with that identifier.
				$this->Categories->where('category_ID', $category_ID);
				$this->Categories->select();
				// Returns the result of the category.
				return $this->Categories->fetch();
			} else {
				return false;
			}
		}

		/**
		 * Returns all the products in a specified category.
		 * 
		 * @param  int       $category_ID Category identifier.
		 * @return array                  Products in the category.
		 * @access protected
		 */
		protected function _getProductsByCat($category_ID = null)
		{
			// Checks if the specified category exists.
			if (self::_exists('Categories', 'category_ID', $category_ID, true)) {
				$this->Categories->clear();
				// Uses the products table.
				$this->Categories->table('products');
				// Looks for the product category with that identifier.
				$this->Categories->where('category_ID', $category_ID);
				$this->Categories->select();
				// Returns the results of the products.
				return $this->Categories->fetch(true);
			} else {
				return false;
			}
		}

		/**
		 * Returns the amount of products in a specified category.
		 * 
		 * @param  int       $category_ID Category identifier.
		 * @return int                    Number of products in the category.
		 * @access protected
		 */
		protected function _getProductCountByCat($category_ID = null)
		{
			// Checks if the specified category exists.
			if (self::_exists('Categories', 'category_ID', $category_ID, true)) {
				$this->Categories->clear();
				// Uses the products table.
				$this->Categories->table('products');
				// Looks for the product category with that identifier.
				$this->Categories->where('category_ID', $category_ID);
				$this->Categories->select();
				// Returns the number of results of products.
				return $this->Categories->rowCount();
			} else {
				// Category wasn't found in any product, so returns a count of 0.
				return 0;
			}
		}

	}

?>