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

		public function liveSearch($str = null)
		{
			if (strlen($str) > 2) {
				$this->ajax = true;
				$this->Search->clear();
				$this->Search->table('products');
				$this->Search->like('product_name', $str);
				$columns = array('product_ID', 'product_name');
				$this->Search->select($columns);
				$search = $this->Search->fetch(true);
				$count = count($search);
				self::set('count', $count);
				self::set('search', $search);
			} else {
				$this->_action = 'unauthorizedAccess';
			}
		}

	}

?>