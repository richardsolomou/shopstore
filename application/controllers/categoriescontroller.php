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
			if (self::_exists('category_ID', $category_ID, true)) {
				$category = self::_getCatById($category_ID);
				$products = self::_getProductsByCat($category_ID);
				self::set('category', $category);
				self::set('products', $products);
			} else {
				notFound();
				return false;
			}
		}

		/**
		 * Returns all categories in the database.
		 * 
		 * @return array  Categories in the database.
		 * @access public
		 */
		public function getList()
		{
			if (self::isAdmin()) {
				$this->Category->clear();
				$this->Category->select();
				$categories = $this->Category->fetch(true);
				$categoryDispatch = new CategoriesController('categories', '_getProductCountByCat');
				self::set('objectParse', $this->_controller);
				self::set('categoryDispatch', $categoryDispatch);
				self::set('categories1', $categories);
				self::set('categories2', $categories);
			} else {
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
			if (self::isAdmin()) {
				$this->ajax = true;
				if (isset($_POST['operation'])) {
					if (self::_exists('category_ID', $_POST['category_parent_ID'], true)) {
						$this->Category->clear();
						$category = array(
							'category_name' => $_POST['category_name'],
							'category_parent_ID' => $_POST['category_parent_ID']
						);
						$this->Category->insert($category);
						self::set('insert', $category);
						self::set('message', 'Category successfully inserted.');
						self::set('alert', 'alert-success nomargin');
						return true;
					} else {
						self::set('message', 'Category parent does not exist.');
						self::set('alert', '');
						return false;
					}
				}
			} else {
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
			if (self::isAdmin()) {
				$this->ajax = true;
				if (self::_exists('category_ID', $category_ID, true) && self::_getProductCountByCat($category_ID) == 0) {
					$this->Category->clear();
					$this->Category->where('category_ID', $category_ID);
					$this->Category->delete();
					self::set('delete', true);
					self::set('message', 'Category successfully deleted.');
					self::set('alert', 'alert-success nomargin');
					return true;
				} else {
					self::set('message', 'Category does not exist, or has products under it.');
					self::set('alert', '');
					return false;
				}
			} else {
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
			if (self::isAdmin()) {
				$this->ajax = true;
				if (isset($_POST['operation'])) {
					if (self::_exists('category_ID', $_POST['category_ID'], true) && self::_exists('category_ID', $_POST['category_parent_ID'], true) && $_POST['category_parent_ID'] != $_POST['category_ID']) {
						$this->Category->clear();
						$this->Category->where('category_ID', $_POST['category_ID'], true);
						$category = array(
							'category_name' => $_POST['category_name'],
							'category_parent_ID' => $_POST['category_parent_ID']
						);
						$this->Category->update($category);
						self::set('update', $category);
						self::set('message', 'Category successfully updated.');
						self::set('alert', 'alert-success nomargin');
						return true;
					} else {
						self::set('message', 'Category does not exist, or category parent does not exist, or category is the same as the parent.');
						self::set('alert', '');
						return false;
					}
				} else {
					$category = self::_getCatById($category_ID);
					self::set('category_ID', $category_ID);
					self::set('category', $category);
				}
			} else {
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
			if (self::_exists('category_ID', $category_ID, true)) {
				$this->Category->clear();
				$this->Category->where('category_ID', $category_ID);
				$this->Category->select();
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
			if (self::_exists('category_ID', $category_ID, true)) {
				$this->Category->clear();
				$this->Category->table('products');
				$this->Category->where('category_ID', $category_ID);
				$this->Category->select();
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
			if (self::_exists('category_ID', $category_ID, true)) {
				$this->Category->clear();
				$this->Category->table('products');
				$this->Category->where('category_ID', $category_ID);
				$this->Category->select();
				return $this->Category->rowCount();
			} else {
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
			// Checks if all characters are digits.
			if ($requireInt == true && !ctype_digit($value)) return false;
			// Allows for the category parent to have a root parent.
			if ($column == 'category_ID' && $value == '0') return true;
			$this->Category->clear();
			$this->Category->where($column, $value);
			$this->Category->select();
			if ($this->Category->rowCount() != 0) {
				return true;
			} else {
				return false;
			}
		}

	}

?>