<?php

	/**
	 * Serves as the controller for the installation of the content management
	 * system. Sends commands to the model to update the model's state.
	 */
	class InstallerController extends Controller
	{

		/**
		 * Lets the system know which file to be used for the view of the
		 * default page in this controller if an action is not specified.
		 * 
		 * @access public
		 */
		public function defaultPage()
		{
			// Only loads the content for this method with a full layout.
			$this->full = true;
			// Calls the correct action depending on whether the database has
			// already been setup or not.
			$this->_action = DB_SETUP == false ? 'first' : 'alreadyInstalled';
		}

		public function first()
		{
			// Only loads the content for this method with a full layout.
			$this->full = true;
			// Checks if the database has already been setup.
			if (DB_SETUP == false) {
				// Destroys any logged in sessions.
				unset($_SESSION['SESS_ADMINID']);
				unset($_SESSION['SESS_ADMINLOGGEDIN']);
			} else{
				// Sends the user to an already installed error page.
				$this->_action = 'alreadyInstalled';
			}
		}

		public function second()
		{
			// Only loads the content for this method with a full layout.
			$this->full = true;
			// Checks that the database has not been setup and that the user has
			// completed the installation form.
			if (isset($_POST['first']) && DB_SETUP == false) {
				$error = array();
				try {
					// Makes a connection to the MySQL server.
		            $db_conn = new PDO('mysql:host=' . $_POST['db_host'], $_POST['db_user'], $_POST['db_pass']);
		            $db_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		        } catch (PDOException $e) {
		        	// Pushes the error to an array.
		        	array_push($error, 'Failed to connect to server.<br>' . $e->getMessage());
		        }
		        // Checks if the checkbox for dropping the existing database was
		        // ticked.
		        if (isset($_POST['dropExisting']) && $_POST['dropExisting'] == 'Yes') {
		            try {
		            	// Drops the specified database.
		                $db_conn->query('DROP DATABASE ' . $_POST['db_name']);
		            } catch (PDOException $e) {
		            	// Pushes the error to an array.
		                array_push($error, 'Database could not be dropped.<br>' . $e->getMessage());
		            }
		        }
		        try {
		        	// Creates the specified database.
		        	$db_conn->query('CREATE DATABASE ' . $_POST['db_name']);
		        } catch (PDOException $e) {
		        	// Pushes the error to an array.
		        	array_push($error, 'Failed to create database<br>' . $e->getMessage());
		        }
		        try {
		        	// Makes a connection to the MySQL server with the database.
		            $db_conn = new PDO('mysql:host=' . $_POST['db_host'] . ';dbname=' . $_POST['db_name'], $_POST['db_user'], $_POST['db_pass']);
		            $db_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		        } catch (PDOException $e) {
		        	// Pushes the error to an array.
		        	array_push($error, 'Failed to connect to the database.<br>' . $e->getMessage());
		        }
		        try {
		        	// Creates the required tables.
			        $db_conn->query('CREATE TABLE products (product_ID int unsigned NOT NULL auto_increment, category_ID int unsigned NOT NULL, product_name varchar(100) NOT NULL, product_description text NULL, product_condition varchar(100) NOT NULL default "New", product_price varchar(8) NOT NULL default "0.00", product_stock int unsigned NOT NULL, product_image varchar(255) default NULL, PRIMARY KEY (product_ID));');
			        $db_conn->query('CREATE TABLE reviews (review_ID int unsigned NOT NULL auto_increment, product_ID int unsigned NOT NULL, review_subject varchar(50) NOT NULL, review_description text NULL, review_rating int unsigned NOT NULL, customer_ID int unsigned NOT NULL, PRIMARY KEY (review_ID));');
			        $db_conn->query('CREATE TABLE categories (category_ID int unsigned NOT NULL auto_increment, category_name varchar(50) NOT NULL, category_parent_ID int NULL default "0", PRIMARY KEY (category_ID));');
			        $db_conn->query('CREATE TABLE customers (customer_ID int unsigned NOT NULL auto_increment, customer_username varchar(50) NOT NULL, customer_password varchar(50) NOT NULL, customer_firstname varchar(50) NOT NULL, customer_lastname varchar(50) NOT NULL, customer_address1 varchar(150) NOT NULL, customer_address2 varchar(150) NULL, customer_postcode varchar(10) NOT NULL, customer_phone varchar(20) NOT NULL, customer_email varchar(150) NOT NULL, PRIMARY KEY (customer_ID), UNIQUE KEY `customer_username` (`customer_username`));');
			        $db_conn->query('CREATE TABLE basket (basket_ID int unsigned NOT NULL auto_increment, basket_quantity int unsigned NULL default "1", product_ID int unsigned NOT NULL, customer_ID int unsigned NOT NULL, PRIMARY KEY (basket_ID));');
			        $db_conn->query('CREATE TABLE orders (order_ID int unsigned NOT NULL auto_increment, order_total float NOT NULL, customer_ID int unsigned NOT NULL, PRIMARY KEY (order_ID));');
			        $db_conn->query('CREATE TABLE items (item_ID int unsigned NOT NULL auto_increment, item_quantity int unsigned NULL default "1", product_ID int unsigned NOT NULL, customer_ID int unsigned NOT NULL, order_ID int unsigned NOT NULL, PRIMARY KEY (item_ID));');
			        $db_conn->query('CREATE TABLE administrators (admin_ID int unsigned NOT NULL auto_increment, admin_username varchar(50) NOT NULL, admin_password varchar(50) NOT NULL, admin_firstname varchar(50) NOT NULL, admin_lastname varchar(50) NOT NULL, admin_email varchar(150) NOT NULL, PRIMARY KEY (admin_ID), UNIQUE KEY `admin_username` (`admin_username`));');
			        $db_conn->query('CREATE TABLE currencies (currency_ID int unsigned NOT NULL auto_increment, currency_name varchar(30) NOT NULL, currency_code char(3) NOT NULL, currency_symbol varchar(8) NOT NULL, PRIMARY KEY (currency_ID));');
			        $db_conn->query('CREATE TABLE settings (setting_ID int unsigned NOT NULL auto_increment, setting_column varchar(150) NOT NULL, setting_value varchar(150) NULL, PRIMARY KEY (setting_ID), UNIQUE KEY `setting_column` (`setting_column`));');
			    } catch (PDOException $e) {
			    	// Pushes the error to an array.
			    	array_push($error, 'Tables could not be created.<br>' . $e->getMessage());
			    }
		        try {
		        	// Fills the administrators table with the information
		        	// specified in the installation form.
		        	$db_conn->query('INSERT INTO administrators (`admin_ID`, `admin_username`, `admin_password`, `admin_firstname`, `admin_lastname`, `admin_email`) VALUES (1, "' . $_POST['admin_username'] . '", "' . $_POST['admin_password'] . '", "' . $_POST['admin_firstname'] . '", "' . $_POST['admin_lastname'] . '", "' . $_POST['admin_email'] . '");');
		        } catch (PDOException $e) {
		        	// Pushes the error to an array.
		        	array_push($error, 'Failed to create administrator account.<br>' . $e->getMessage());
		        }
		        try {
		        	// Sets the website name in the settings.
		        	$db_conn->query('INSERT INTO settings (`setting_ID`, `setting_column`, `setting_value`) VALUES (1, "website_name", "' . $_POST['website_name'] . '");');
		        } catch (PDOException $e) {
		        	// Pushes the error to an array.
		        	array_push($error, 'Failed to create website name setting.<br>' . $e->getMessage());
		        }
		        try {
		        	// Sets the first currency as the default in the settings.
		        	$db_conn->query('INSERT INTO settings (`setting_ID`, `setting_column`, `setting_value`) VALUES (2, "currency_ID", 1);');
		        } catch (PDOException $e) {
		        	// Pushes the error to an array.
		        	array_push($error, 'Failed to create default currency setting.<br>' . $e->getMessage());
		        }
		        // Checks if the sample data checkbox was ticked.
		        if (isset($_POST['sampleData']) && $_POST['sampleData'] == 'Yes') {
		        	try {
		        		// Creates sample data for the currencies.
		        		$db_conn->query('INSERT INTO currencies (currency_ID, currency_name, currency_code, currency_symbol) VALUES (1, "British Pound", "GBP", "&pound;");');
		        		$db_conn->query('INSERT INTO currencies (currency_ID, currency_name, currency_code, currency_symbol) VALUES (2, "Euro", "EUR", "&euro;");');
		        		$db_conn->query('INSERT INTO currencies (currency_ID, currency_name, currency_code, currency_symbol) VALUES (3, "United States Dollar", "USD", "&#36;");');
		        	} catch (PDOException $e) {
		        		// Pushes the error to an array.
		        		array_push($error, 'Failed to fill in sample data.<br>' . $e->getMessage());
		        	}
		        }
		        // Checks if the error array is empty.
		        if(empty($error)) {
		        	// Creates a configuration array with the content to include
		        	// in the configuration file to be created.
		            $config[] = '<?php' . PHP_EOL;
		            $config[] = '    /**';
		            $config[] = '     * Set the under-development environment variable.';
		            $config[] = '     */';
		            $config[] = '    define (\'DEVELOPMENT_ENVIRONMENT\', true);' . PHP_EOL;
		            $config[] = '    /**';
		            $config[] = '     * Set configuration variables.';
		            $config[] = '     */';
		            $config[] = '    define(\'DB_HOST\', \'' . $_POST['db_host'] . '\');';
		            $config[] = '    define(\'DB_USER\', \'' . $_POST['db_user'] . '\');';
		            $config[] = '    define(\'DB_PASS\', \'' . $_POST['db_pass'] . '\');';
		            $config[] = '    define(\'DB_NAME\', \'' . $_POST['db_name'] . '\');' . PHP_EOL;
		            $config[] = '    /**';
		            $config[] = '     * Automatically set by the installer.';
		            $config[] = '     */';
		            $config[] = '    define(\'DB_SETUP\', true);' . PHP_EOL;
		            $config[] = '?>';
		            // Puts the contents in the configuration file.
		            file_put_contents(SERVER_ROOT . '/library/config.php', implode(PHP_EOL, $config));
					// Creates the two sessions required for administrative
					// privileges.
					$_SESSION['SESS_ADMINID'] = 1;
					$_SESSION['SESS_ADMINLOGGEDIN'] = 1;
		        } else {
		        	// Returns the alert message to be sent to the user.
		        	self::set('error', $error);
		        	// Sends the user to the error page.
		        	$this->_action = 'error';
		        }
			} else {
				// Sends the user to an already installed error page.
				$this->_action = 'alreadyInstalled';
			}
		}
	}

?>