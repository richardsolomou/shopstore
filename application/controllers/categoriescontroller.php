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
			$this->_action = 'getList';
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
			if (self::exists('category_ID', $category_ID, true)) {
				$category = self::getCatById($category_ID);
				$products = self::getProductsByCat($category_ID);
				self::set('category', $category);
				self::set('products', $products);
			}
		}

		/**
		 * Returns category values in a variable.
		 * 
		 * @param  int   $category_ID Category identifier.
		 * @return array              Returns the category values.
		 * @access public
		 */
		public function getCatById($category_ID = null)
		{
			$this->Category->clear();
			$this->Category->where('category_ID', $category_ID);
			$this->Category->select();
			return $this->Category->fetch();
		}

		/**
		 * Returns all the products in a specified category.
		 * 
		 * @param  int   $category_ID Category identifier.
		 * @return array              Products in the category.
		 * @access public
		 */
		public function getProductsByCat($category_ID = null)
		{
			$this->Category->clear();
			$this->Category->table('products');
			$this->Category->where('category_ID', $category_ID);
			$this->Category->select();
			return $this->Category->fetch(true);
		}

		/**
		 * Returns the amount of products in a specified category.
		 * 
		 * @param  int $category_ID Category identifier.
		 * @return int              Number of products in the category.
		 * @access public
		 */
		public function getProductCountByCat($category_ID = null)
		{
			if (self::exists('category_ID', $category_ID, true)) {
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
		 * Returns all categories in the database.
		 * 
		 * @return array Categories in the database.
		 * @access public
		 */
		public function getList()
		{
			$this->Category->clear();
			$this->Category->select();
			$categories = $this->Category->fetch(true);
			return $categories;
		}

		/**
		 * Adds a category into the database.
		 * 
		 * @param  string $category_name      Name of the inserted category.
		 * @param  int    $category_parent_ID Parent of the inserted category.
		 * @access public
		 */
		public function insert($category_name = null, $category_parent_ID = null)
		{
			if ($this->exists('category_ID', $category_parent_ID, true)) {
				$this->Category->clear();
				$category = array(
					'category_name' => $category_name,
					'category_parent_ID' => $category_parent_ID
				);
				$this->Category->insert($category);
				$this->set('category', $category);
			}
		}

		/**
		 * Removes a category from the database.
		 * 
		 * @param  int $category_ID Category identifier.
		 * @access public
		 */
		public function delete($category_ID = null)
		{
			if ($this->exists('category_ID', $category_ID, true)) {
				$this->Category->clear();
				$this->Category->where('category_ID', $category_ID);
				$this->Category->delete();
				$this->set('category', true);
			}
		}

		/**
		 * Modifies a category in the database with the specified new attributes.
		 * 
		 * @param  int    $category_ID        Category identifier.
		 * @param  string $category_name      Name of the modified category.
		 * @param  int    $category_parent_ID Parent of the modified category.
		 * @access public
		 */
		public function update($category_ID = null, $category_name = null, $category_parent_ID = null)
		{
			if ($this->exists('category_ID', $category_ID, true) && $this->exists('category_ID', $category_parent_ID, true)) {
				$this->Category->clear();
				$this->Category->where('category_ID', $category_ID);
				$category = array(
					'category_name' => $category_name,
					'category_parent_ID' => $category_parent_ID
				);
				$this->Category->update($category);
				$this->set('category', $category);
			}
		}

		/**
		 * Checks if a category exists in the database with the given attributes.
		 * 
		 * @param  array   $arr        Name and value of column to search for.
		 * @param  boolean $requireInt Requires the value sent to be an integer.
		 * @return boolean             Does the category exist?
		 * @access public
		 */
		public function exists($column = null, $value = null, $requireInt = false)
		{
			if ($requireInt == true && !ctype_digit($value)) return false;
			// Allows for the category parent to have a root parent.
			if ($column == 'category_parent_ID' && $value == '0') return true;
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