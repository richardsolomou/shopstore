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
			header('Location: ' . BASE_PATH . '/products/getList');
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
			// Checks if the specified product exists.
			if (self::_exists('product_ID', $product_ID, true)) {
				// Gets the values of the product and its category.
				$product = self::_getProductById($product_ID);
				$productCategory = self::_getCatById($product['category_ID']);
				// Gets the currency ID from the website settings table and the
				// respective symbol from the currencies table.
				$settingsCurrency = self::_getSettingByColumn('currency_ID');
				$currencySymbol = self::_getCurrencyById($settingsCurrency['setting_value']);
				// Gets the reviews and the number of reviews for the product.
				$reviews = self::_getProductReviews($product_ID);
				$reviewNumber = self::_getProductReviewNumber($product_ID);
				// Calculates the average rating based on all reviews.
				$reviewRatingAverage = 0;
				$individualReview = 0;
		        if ($reviewNumber > 0) foreach ($reviews as $individualReview) $reviewRatingAverage += $individualReview['review_rating'] / $reviewNumber;
		        // Creates an instance of the customers controller to fetch
		        // details for the customers in the reviews.
		        $customerDispatch = new CustomersController('customers', '_getCustomerById');
		        // Checks if the product is currently in the basket.
		        $inBasket = null;
		        if (self::isCustomer()) {
		        	$inBasket = self::_checkIfInBasket($product_ID, $_SESSION['SESS_CUSTOMERID']);
			        if ($inBasket != 0) {
			        	$basket_ID = self::_getBasketID($product_ID, $_SESSION['SESS_CUSTOMERID']);
			        	self::set('basket_ID', $basket_ID);
			        }
			    }
				self::set('product', $product);
				self::set('reviews', $reviews);
				self::set('reviewNumber', $reviewNumber);
				self::set('individualReview', $individualReview);
				self::set('reviewRatingAverage', $reviewRatingAverage);
				self::set('customerDispatch', $customerDispatch);
				self::set('productCategory', $productCategory['category_name']);
				self::set('currencySymbol', $currencySymbol['currency_symbol']);
				self::set('inBasket', $inBasket);
			} else {
				// Returns a 404 error page.
				notFound();
			}
		}

		/**
		 * Returns all products in the database.
		 * 
		 * @access public
		 */
		public function getList()
		{
			// Checks if the user has sufficient privileges.
			if (self::isAdmin()) {
				$this->Products->clear();
				$this->Products->select();
				// Fetches all the products.
				$products = $this->Products->fetch(true);
				self::set('products', $products);
			} else {
				// Returns an unauthorized access page.
				$this->_action = 'unauthorizedAccess';
			}
		}

		/**
		 * Adds a product into the database.
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
					// Checks if the specified category exists.
					if (self::_exists('category_ID', $_POST['category_ID'], true, 'categories')) {
						$this->Products->clear();
						$imageExtension = $_POST['product_image'];
						if ($_POST['product_image'] != null) {
							// Moves the image from the temporary folder.
							$product_image = SERVER_ROOT . '\\templates\\img\\products\\tmp\\' . $_POST['product_image'];
							// Gets the extension of the image.
							$imageExtension = strrchr($product_image, '.');
						}
						// Creates the array of product values to be parsed.
						$product = array(
							'category_ID'         => $_POST['category_ID'],
							'product_name'        => $_POST['product_name'],
							'product_description' => $_POST['product_description'],
							'product_condition'   => $_POST['product_condition'],
							'product_price'       => $_POST['product_price'],
							'product_stock'       => $_POST['product_stock'],
							'product_image'       => $imageExtension
						);
						// Inserts the product into the database.
						$this->Products->insert($product);
						if ($_POST['product_image'] != null) {
							// Gets the product identifier of this request.
							$lastId = $this->Products->lastId();
							// Moves and renames the file based on its product_ID.
							if (file_exists($product_image)) rename($product_image, SERVER_ROOT . '\\templates\\img\\products\\' . $lastId . $imageExtension);
						}
						// Returns the alert message to be sent to the user.
						self::set('message', 'Product successfully created.');
						self::set('alert', 'alert-success');
					} else {
						// Returns the alert message to be sent to the user.
						self::set('message', 'Category does not exist.');
						self::set('alert', '');
					}
					// Show an alert.
					$this->_action = 'alert';
				// Default action for GET requests.
				} else {
					// Gets the currency ID from the website settings table and
					// the respective symbol from the currencies table.
					$settingsCurrency = self::_getSettingByColumn('currency_ID');
					$currencySymbol = self::_getCurrencyById($settingsCurrency['setting_value']);
					self::set('currencySymbol', $currencySymbol['currency_symbol']);
				}
			} else {
				// Returns an unauthorized access page.
				$this->_action = 'unauthorizedAccess';
			}
		}

		/**
		 * Removes a product from the database.
		 * 
		 * @param  int    $product_ID Product identifier.
		 * @access public
		 */
		public function delete($product_ID = null)
		{
			// Checks if the user has sufficient privileges.
			if (self::isAdmin()) {
				// Only loads the content for this method.
				$this->ajax = true;
				// Checks if the specified product exists.
				if (self::_exists('product_ID', $product_ID, true)) {
					// Deletes the image.
					self::deleteImage($product_ID);
					// Deletes all reviews for that product.
					self::_deleteReviews($product_ID);
					// Deletes all basket item records for that product.
					self::_deleteFromBasket($product_ID);
					$this->Products->clear();
					// Looks for the product with that identifier.
					$this->Products->where('product_ID', $product_ID);
					// Deletes the product from the database.
					$this->Products->delete();
					// Returns the alert message to be sent to the user.
					self::set('message', 'Product successfully deleted.');
					self::set('alert', 'alert-success nomargin');
				} else {
					// Returns the alert message to be sent to the user.
					self::set('message', 'Product does not exist.');
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
		 * Modifies a product in the database with the specified new attributes.
		 * 
		 * @param  int    $product_ID Product identifier.
		 * @access public
		 */
		public function update($product_ID = null)
		{
			// Checks if the user has sufficient privileges.
			if (self::isAdmin()) {
				// Only loads the content for this method.
				$this->ajax = true;
				// Checks if this was a POST request.
				if (isset($_POST['operation'])) {
					// Checks if the specified product exists.
					if (self::_exists('product_ID', $product_ID, true)) {
						// Checks if the specified category exists.
						if (self::_exists('category_ID', $_POST['category_ID'], true, 'categories')) {
							$this->Products->clear();
							$imageExtension = $_POST['product_image'];
							if ($_POST['product_image'] != null) {
								// Moves the image from the temporary folder.
								$product_image = SERVER_ROOT . '\\templates\\img\\products\\tmp\\' . $_POST['product_image'];
								// Gets the extension of the image.
								$imageExtension = strrchr($product_image, '.');
							}
							// Looks for the product with that identifier.
							$this->Products->where('product_ID', $product_ID, true);
							$product = array(
								'category_ID'         => $_POST['category_ID'],
								'product_name'        => $_POST['product_name'],
								'product_description' => $_POST['product_description'],
								'product_condition'   => $_POST['product_condition'],
								'product_price'       => $_POST['product_price'],
								'product_stock'       => $_POST['product_stock']
							);
							$imageArray = array(
								'product_image' => $imageExtension
							);
							if ($imageExtension != null) $product = array_merge($product, $imageArray);
							// Updates the product.
							$this->Products->update($product);
							if ($_POST['product_image'] != null) {
								// Deletes any previous images.
								self::deleteImage($product_ID);
								// Moves and renames the file based on its product_ID.
								if (file_exists($product_image)) rename($product_image, SERVER_ROOT . '\\templates\\img\\products\\' . $product_ID . $imageExtension);
							}
							// Returns the alert message to be sent to the user.
							self::set('message', 'Product successfully updated.');
							self::set('alert', 'alert-success nomargin');
						} else {
							// Returns the alert message to be sent to the user.
							self::set('message', 'Category does not exist.');
							self::set('alert', 'nomargin');
						}
					} else {
						// Returns the alert message to be sent to the user.
						self::set('message', 'Product does not exist.');
						self::set('alert', 'nomargin');
					}
					// Show an alert.
					$this->_action = 'alert';
				// Default action for GET requests.
				} else {
					// Returns the product's values from the database.
					$product = self::_getProductById($product_ID);
					// Gets the currency ID from the website settings table and
					// the respective symbol from the currencies table.
					$settingsCurrency = self::_getSettingByColumn('currency_ID');
					$currencySymbol = self::_getCurrencyById($settingsCurrency['setting_value']);
					self::set('currencySymbol', $currencySymbol['currency_symbol']);
					self::set('product', $product);
					self::set('product_ID', $product_ID);
				}
			} else {
				// Returns an unauthorized access page.
				$this->_action = 'unauthorizedAccess';
			}
		}

		/**
		 * Uploads a file into a temporary folder.
		 * 
		 * @access public
		 */
		public function uploadFile()
		{
			// Only loads the content for this method.
			$this->ajax = true;
			// Checks if a file was uploaded.
			if (array_key_exists('HTTP_X_FILE_NAME', $_SERVER) && array_key_exists('CONTENT_LENGTH', $_SERVER)) {
				// Assigns the file and its filename to a variable.
				$fileName = $_SERVER['HTTP_X_FILE_NAME'];
				$file = file_get_contents('php://input');
				// Creates the file in the temporary folder.
				$tmpFilePath = SERVER_ROOT . '\\templates\\img\\products\\tmp\\' . $fileName;
				$tmpFile = file_put_contents($tmpFilePath, $file);
				// Add permissions to the file.
				chmod($tmpFilePath, 0666);
				// Creates the main attributes of the file.
				$attributes = getimagesize($tmpFilePath);
				$imageWidth = $attributes[0];
				$imageHeight = $attributes[1];
				$imageType = $attributes[2];
				// Resizes the image to create a smaller version.
				$thumbnailHeight = intval($imageHeight / $imageWidth * 175);
				$thumbnailResource = imagecreatetruecolor(175, $thumbnailHeight);
				// Checks what type of file it is.
				switch ($imageType) {
					case IMAGETYPE_GIF:
						$imageResource = imagecreatefromgif($tmpFilePath);
						imagecopyresampled($thumbnailResource, $imageResource, 0, 0, 0, 0, 175, $thumbnailHeight, $imageWidth, $imageHeight);
						imagegif($thumbnailResource, $tmpFilePath);
						break;
					case IMAGETYPE_JPEG:
						$imageResource = imagecreatefromjpeg($tmpFilePath);
						imagecopyresampled($thumbnailResource, $imageResource, 0, 0, 0, 0, 175, $thumbnailHeight, $imageWidth, $imageHeight);
						imagejpeg($thumbnailResource, $tmpFilePath, 85);
						break;
					case IMAGETYPE_PNG:
						imagealphablending($thumbnailResource, false);
						imagesavealpha($thumbnailResource, true);
						$imageResource = imagecreatefrompng($tmpFilePath);
						imagealphablending($imageResource, true);
						imagecopyresampled($thumbnailResource, $imageResource, 0, 0, 0, 0, 175, $thumbnailHeight, $imageWidth, $imageHeight);
						imagepng($thumbnailResource, $tmpFilePath);
						break;
					default:
						// Deletes the temporary file since it's not an image.
						unlink($tmpFilePath);
						// Returns the alert message to be sent to the user.
						self::set('message', 'File is not an image.');
						self::set('alert', '');
						$this->_action = 'alert';
						return false;
				}
				// Returns the alert message to be sent to the user.
				self::set('message', 'File uploaded.');
				self::set('alert', 'alert-success');
				$this->_action = 'alert';
	        }
		}

		/**
		 * Deletes an image from the file system.
		 * 
		 * @param  int    $product_ID Product identifier.
		 * @access public
		 */
		public function deleteImage($product_ID = null)
		{
			$this->ajax = true;
			$imageGlobal = glob(strtolower(SERVER_ROOT . '/templates/img/products/' . $product_ID . '.' . '*'));
			$imageArray = $imageGlobal ? $imageGlobal : array();
			if ($imageArray != array()) {
				foreach ($imageArray as $image) {
					unlink($image);
				}
				// Returns the alert message to be sent to the user.
				self::set('message', 'Image deleted.');
				self::set('alert', 'alert-success');
				$this->_action = 'delete';
			} else {
				// Returns the alert message to be sent to the user.
				self::set('message', 'Image doesn\'t exist.');
				self::set('alert', '');
				$this->_action = 'delete';
			}
		}

		/**
		 * Adds more stock to a product.
		 * 
		 * @param  int    $product_ID Product identifier.
		 * @access public
		 */
		public function addStock($product_ID = null)
		{
			// Checks if the user has sufficient privileges.
			if (self::isAdmin()) {
				// Only loads the content for this method.
				$this->ajax = true;
				// Checks if this was a POST request.
				if (isset($_POST['operation'])) {
					// Checks if the specified product exists.
					if (self::_exists('product_ID', $product_ID, true)) {
						// Gets the current values of the product.
						$productItem = self::_getProductById($product_ID);
						$newStock = $productItem['product_stock'] + $_POST['product_stock'];
						$this->Products->clear();
						// Looks for the product with that identifier.
						$this->Products->where('product_ID', $product_ID, true);
						$product = array(
							'product_stock' => $newStock
						);
						// Updates the product.
						$this->Products->update($product);
						// Returns the alert message to be sent to the user.
						self::set('message', 'Product successfully updated.');
						self::set('alert', 'alert-success nomargin');
					} else {
						// Returns the alert message to be sent to the user.
						self::set('message', 'Product does not exist.');
						self::set('alert', 'nomargin');
					}
					// Show an alert.
					$this->_action = 'alert';
				// Default action for GET requests.
				} else {
					// Returns the product's values from the database.
					$product = self::_getProductById($product_ID);
					self::set('product', $product);
					self::set('product_ID', $product_ID);
				}
			} else {
				// Returns an unauthorized access page.
				$this->_action = 'unauthorizedAccess';
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
			// Checks if the product exists.
			if (self::_exists('product_ID', $product_ID, true)) {
				$this->Products->clear();
				// Looks for the product with that identifier.
				$this->Products->where('product_ID', $product_ID);
				$this->Products->select();
				// Returns the result of the product.
				return $this->Products->fetch();
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
			// Checks if the specified category exists.
			if (self::_exists('category_ID', $category_ID, true, 'categories')) {
				$this->Products->clear();
				// Uses the categories table.
				$this->Products->table('categories');
				// Looks for the category with that identifier.
				$this->Products->where('category_ID', $category_ID);
				$this->Products->select();
				// Returns the result of the category.
				return $this->Products->fetch();
			} else {
				return false;
			}
		}

		/**
		 * Returns a specified setting from the database.
		 *
		 * @param  string    $setting_column Name of the setting's column.
		 * @return array                     Settings of the database.
		 * @access protected
		 */
		protected function _getSettingByColumn($setting_column = null)
		{
			// Checks if the setting column value exists.
			if (self::_exists('setting_column', $setting_column, false, 'settings')) {
				$this->Products->clear();
				// Uses the settings table.
				$this->Products->table('settings');
				// Looks for the setting column with that value.
				$this->Products->where('setting_column', '"' . $setting_column . '"');
				$this->Products->select();
				// Returns the result of the setting.
				return $this->Products->fetch();
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
			// Checks if the currency exists.
			if (self::_exists('currency_ID', $currency_ID, true, 'currencies')) {
				$this->Products->clear();
				// Uses the currencies table.
				$this->Products->table('currencies');
				// Looks for a currency with that identifier.
				$this->Products->where('currency_ID', $currency_ID);
				$this->Products->select();
				// Returns the result of that currency.
				return $this->Products->fetch();
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
			// Checks if the product exists.
			if (self::_exists('product_ID', $product_ID, true)) {
				$this->Products->clear();
				// Uses the reviews table
				$this->Products->table('reviews');
				// Looks for a review with that product identifier
				$this->Products->where('product_ID', $product_ID);
				$this->Products->select();
				// Returns the results of the reviews.
				return $this->Products->fetch(true);
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
			// Checks if the product exists.
			if (self::_exists('product_ID', $product_ID, true)) {
				$this->Products->clear();
				// Uses the reviews table.
				$this->Products->table('reviews');
				// Looks for a review with that product identifier.
				$this->Products->where('product_ID', $product_ID);
				$this->Products->select();
				// Returns the number of results of reviews.
				return $this->Products->rowCount();
			} else {
				return false;
			}
		}

		/**
		 * Deletes the reviews of the specified product.
		 * 
		 * @param  int       $product_ID Product identifier.
		 * @access protected
		 */
		protected function _deleteReviews($product_ID = null)
		{
			// Checks if the product exists.
			if (self::_exists('product_ID', $product_ID, true)) {
				$this->Products->clear();
				// Uses the reviews table.
				$this->Products->table('reviews');
				// Looks for a review with that product identifier.
				$this->Products->where('product_ID', $product_ID);
				// Deletes the reviews.
				$this->Products->delete();
			}
		}

		/**
		 * Deletes the specified product from the basket.
		 * 
		 * @param  int       $product_ID Product identifier.
		 * @access protected
		 */
		protected function _deleteFromBasket($product_ID = null)
		{
			// Checks if the product exists.
			if (self::_exists('product_ID', $product_ID, true)) {
				$this->Products->clear();
				// Uses the basket table.
				$this->Products->table('basket');
				// Looks for a basket item with that product identifier.
				$this->Products->where('product_ID', $product_ID);
				// Deletes the basket item.
				$this->Products->delete();
			}
		}

		/**
		 * Checks if the current item is in the basket.
		 * 
		 * @param  int       $product_ID  Product identifier.
		 * @param  int       $customer_ID Customer identifier.
		 * @return string                 Returns the quantity in the basket.
		 * @access protected
		 */
		protected function _checkIfInBasket($product_ID = null, $customer_ID = null)
		{
			// Checks if the product exists.
			if (self::_exists('product_ID', $product_ID, true)) {
				// Checks if the customer exists.
				if (self::_exists('customer_ID', $customer_ID, true, 'customers')) {
					$this->Products->clear();
					// Uses the basket table.
					$this->Products->table('basket');
					// Looks for the product with that product identifier.
					$this->Products->where('product_ID', $product_ID);
					// Looks for the customer with that customer identifier.
					$this->Products->where('customer_ID', $customer_ID);
					$this->Products->select();
					// Returns the number of results of basket items.
					return $this->Products->rowCount();
				} else {
					return false;
				}
			} else {
				return false;
			}
		}

		/**
		 * Gets the basket identifier for that specific product.
		 *
		 * @param  int       $product_ID  Product identifier.
		 * @param  int       $customer_ID Customer identifier.
		 * @return int                    Returns the basket identifier.
		 * @access protected
		 */
		protected function _getBasketID($product_ID = null, $customer_ID = null)
		{
			// Checks if the product exists.
			if (self::_exists('product_ID', $product_ID, true)) {
				// Checks if the customer exists.
				if (self::_exists('customer_ID', $customer_ID, true, 'customers')) {
					$this->Products->clear();
					// Uses the basket table.
		        	$this->Products->table('basket');
		        	// Looks for the customer and product with those identifiers.
		        	$this->Products->where('customer_ID', $customer_ID);
		        	$this->Products->where('product_ID', $product_ID);
		        	$this->Products->select();
		        	// Returns the basket identifier.
		        	return $this->Products->fetch();
		        } else {
		        	return false;
		        }
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
			// Checks if not all characters are digits.
			if ($requireInt == true && !ctype_digit($value)) return false;
			$this->Products->clear();
			// Uses a different table for other controllers.
			if ($customTable != 'products') $this->Products->table($customTable);
			if ($requireInt == false) {
				// Looks for a string value in a specified column.
				$this->Products->where($column, '"' . $value . '"');
			} else {
				// Loooks for an integer value in a specified column.
				$this->Products->where($column, $value);
			}
			$this->Products->select();
			// Returns the appropriate value if the element exists or not.
			if ($this->Products->rowCount() != 0) {
				return true;
			} else {
				return false;
			}
		}
	}

?>