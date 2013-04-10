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
		 * @param  int $category_ID Category identifier.
		 * @access public
		 */
		public function getById($category_ID = null)
		{
			// Checks if the specified category exists.
			if (self::_exists('category_ID', $category_ID, true)) {
				// Gets the values of the category.
				$category = self::_getCatById($category_ID);
				// Gets the products under the category.
				$products = self::_getProductsByCat($category_ID);
				self::set('category', $category);
				self::set('products', $products);
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
				$this->Category->clear();
				$this->Category->select();
				// Fetches all the categories.
				$categories = $this->Category->fetch(true);
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
					if (self::_exists('category_ID', $_POST['category_parent_ID'], true)) {
						$this->Category->clear();
						$category = array(
							'category_name' => $_POST['category_name'],
							'category_parent_ID' => $_POST['category_parent_ID']
						);
						// Inserts the category into the database.
						$this->Category->insert($category);
						// Returns the alert message to be sent to the user.
						self::set('message', 'Category successfully inserted.');
						self::set('alert', 'alert-success nomargin');
					} else {
						// Returns the alert message to be sent to the user.
						self::set('message', 'Category parent does not exist.');
						self::set('alert', '');
					}
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
				if (self::_exists('category_ID', $category_ID, true)) {
					if (self::_getProductCountByCat($category_ID) == 0) {
						$this->Category->clear();
						// Looks for the category with that identifier.
						$this->Category->where('category_ID', $category_ID);
						// Deletes the category from the database.
						$this->Category->delete();
						// Returns the alert message to be sent to the user.
						self::set('message', 'Category successfully deleted.');
						self::set('alert', 'alert-success nomargin');
					} else {
						// Returns the alert message to be sent to the user.
						self::set('message', 'Category has products under it.');
						self::set('alert', '');
					}
				} else {
					// Returns the alert message to be sent to the user.
					self::set('message', 'Category does not exist.');
					self::set('alert', '');
				}
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
					if (self::_exists('category_ID', $category_ID, true)) {
						// Checks if the specified category parent exists.
						if (self::_exists('category_ID', $_POST['category_parent_ID'], true)) {
							// Checks if the specified category parent is not the
							// same as the current category.
							if ($_POST['category_parent_ID'] != $category_ID) {
								$this->Category->clear();
								// Looks for the category with that identifier.
								$this->Category->where('category_ID', $category_ID, true);
								$category = array(
									'category_name' => $_POST['category_name'],
									'category_parent_ID' => $_POST['category_parent_ID']
								);
								// Updates the category.
								$this->Category->update($category);
								// Returns the alert message to be sent to the user.
								self::set('message', 'Category successfully updated.');
								self::set('alert', 'alert-success nomargin');
							} else {
								// Returns the alert message to be sent to the user.
								self::set('message', 'Category is the same as the parent.');
								self::set('alert', '');
							}
						} else {
							// Returns the alert message to be sent to the user.
							self::set('message', 'Category parent does not exist.');
							self::set('alert', '');
						}
					} else {
						// Returns the alert message to be sent to the user.
						self::set('message', 'Category does not exist.');
						self::set('alert', '');
					}
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
			if (self::_exists('category_ID', $category_ID, true)) {
				$this->Category->clear();
				// Looks for the category with that identifier.
				$this->Category->where('category_ID', $category_ID);
				$this->Category->select();
				// Returns the result of the category.
				return $this->Category->fetch();
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
			if (self::_exists('category_ID', $category_ID, true)) {
				$this->Category->clear();
				// Uses the products table.
				$this->Category->table('products');
				// Looks for the product category with that identifier.
				$this->Category->where('category_ID', $category_ID);
				$this->Category->select();
				// Returns the results of the products.
				return $this->Category->fetch(true);
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
			if (self::_exists('category_ID', $category_ID, true)) {
				$this->Category->clear();
				// Uses the products table.
				$this->Category->table('products');
				// Looks for the product category with that identifier.
				$this->Category->where('category_ID', $category_ID);
				$this->Category->select();
				// Returns the number of results of products.
				return $this->Category->rowCount();
			} else {
				// Category wasn't found in any product, so returns a count of 0.
				return 0;
			}
		}

		/**
		 * Checks if a category exists in the database with the given attributes.
		 * 
		 * @param  string    $column     Name of the column to search on.
		 * @param  string    $value      Value to search for.
		 * @param  boolean   $requireInt Requires the value sent to be an integer.
		 * @return boolean               Does the category exist?
		 * @access protected
		 */
		protected function _exists($column = null, $value = null, $requireInt = false)
		{
			// Checks if not all characters are digits.
			if ($requireInt == true && !ctype_digit($value)) return false;
			// Allows for the category parent to have a root parent.
			if ($column == 'category_ID' && $value == '0') return true;
			$this->Category->clear();
			if ($requireInt == false) {
				// Looks for a string value in a specified column.
				$this->Category->where($column, '"' . $value . '"');
			} else {
				// Loooks for an integer value in a specified column.
				$this->Category->where($column, $value);
			}
			$this->Category->select();
			// Returns the appropriate value if the element exists or not.
			if ($this->Category->rowCount() != 0) {
				return true;
			} else {
				return false;
			}
		}

	}

?>