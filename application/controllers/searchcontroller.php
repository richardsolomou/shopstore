<?php

	/**
	 * Serves as the controller for all search related functions and
	 * operations. Sends commands to the model to update the model's state.
	 */
	class SearchController extends Controller
	{

		/**
		 * Lets the system know which file to be used for the view of the
		 * default page in this controller if an action is not specified.
		 * 
		 * @access public
		 */
		public function defaultPage()
		{
			header('Location: ' . BASE_PATH . '/search/liveSearch');
		}

		/**
		 * Gets products that match the string given and returns them.
		 * 
		 * @param  string $str String to search for.
		 * @access public
		 */
		public function liveSearch($str = null)
		{
			// Only loads if the string has a length of more than 2 characters.
			if (strlen($str) > 2) {
				// Only loads the content for this method.
				$this->ajax = true;
				$this->Search->clear();
				$this->Search->table('products');
				// Looks for products with a similar title to the string.
				$this->Search->like('product_name', $str);
				$columns = array('product_ID', 'product_name');
				$this->Search->select($columns);
				$search = $this->Search->fetch(true);
				// Gets the number of results.
				$count = count($search);
				self::set('count', $count);
				self::set('search', $search);
			} else {
				$this->_action = 'unauthorizedAccess';
			}
		}

	}

?>