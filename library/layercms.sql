CREATE TABLE products (
	product_ID int unsigned NOT NULL auto_increment,
	category_ID int unsigned NOT NULL,
	product_name varchar(100) NOT NULL,
	product_description text NULL,
	product_condition varchar(8) NOT NULL default "New",
	product_price varchar(8) NOT NULL default "0.00",
	product_stock int unsigned NOT NULL,
	product_image varchar(255) default NULL,
	PRIMARY KEY (product_ID)
);

CREATE TABLE reviews (
	review_ID int unsigned NOT NULL auto_increment,
	product_ID int unsigned NOT NULL,
	review_subject varchar(50) NOT NULL,
	review_description text NULL,
	review_rating int unsigned NOT NULL,
	customer_ID int unsigned NOT NULL,
	PRIMARY KEY (review_ID)
);

CREATE TABLE categories (
	category_ID int unsigned NOT NULL auto_increment,
	category_name varchar(50) NOT NULL,
	category_parent_ID int NULL default "0",
	PRIMARY KEY (category_ID)
);

CREATE TABLE customers (
	customer_ID int unsigned NOT NULL auto_increment,
	customer_username varchar(50) NOT NULL,
	customer_password varchar(50) NOT NULL,
	customer_firstname varchar(50) NOT NULL,
	customer_lastname varchar(50) NOT NULL,
	customer_address1 varchar(150) NOT NULL,
	customer_address2 varchar(150) NULL,
	customer_postcode varchar(10) NOT NULL,
	customer_phone varchar(20) NOT NULL,
	customer_email varchar(150) NOT NULL,
	PRIMARY KEY (customer_ID),
	UNIQUE KEY `customer_username` (`customer_username`)
);

CREATE TABLE basketItems (
	basket_ID int unsigned NOT NULL auto_increment,
	basket_quantity int unsigned NOT NULL default "1",
	order_ID int unsigned NOT NULL,
	product_ID int unsigned NOT NULL,
	PRIMARY KEY (basket_ID)
);

CREATE TABLE orders (
	order_ID int unsigned NOT NULL auto_increment,
	order_date DATETIME NOT NULL default "0000-00-00 00:00:00",
	order_status varchar(15) NOT NULL,
	order_total float NOT NULL,
	customer_ID int unsigned NOT NULL,
	PRIMARY KEY (order_ID)
);

CREATE TABLE administrators (
	admin_ID int unsigned NOT NULL auto_increment,
	admin_username varchar(50) NOT NULL,
	admin_password varchar(50) NOT NULL,
	admin_firstname varchar(50) NOT NULL,
	admin_lastname varchar(50) NOT NULL,
	admin_email varchar(150) NOT NULL,
	PRIMARY KEY (admin_ID),
	UNIQUE KEY `admin_username` (`admin_username`)
);

CREATE TABLE currencies (
	currency_ID int unsigned NOT NULL auto_increment,
	currency_name varchar(30) NOT NULL,
	currency_code char(3) NOT NULL,
	currency_symbol varchar(8) NOT NULL,
	PRIMARY KEY (currency_ID)
);

CREATE TABLE settings (
	setting_ID int unsigned NOT NULL auto_increment,
	setting_column varchar(150) NOT NULL,
	setting_value varchar(150) NULL,
	PRIMARY KEY (setting_ID),
	UNIQUE KEY `setting_column` (`setting_column`)
);