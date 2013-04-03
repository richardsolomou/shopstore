<!DOCTYPE HTML>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title><?php echo $title; ?></title>
		<link rel="stylesheet" href="<?php echo BASE_PATH . DS . 'templates' . DS . 'css' . DS . 'style.css'; ?>" media="all">
		<!--[if lt IE 9]>
				<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
		<script src="<?php echo BASE_PATH . DS . 'templates' . DS . 'js' . DS . 'layercms.js'; ?>"></script>
	</head>
	<body>

		<div id="wrapper">

			<header>

				<h1><a href="<?php echo BASE_PATH; ?>"><?php echo $title; ?></a></h1>

			</header>

			<?php if (isset($admin)) { ?>
			<div class="adminBar">
				<p>Welcome <?php echo $admin['admin_firstname'] . ' ' . $admin['admin_lastname']; ?><span class='pull-right'>Currently logged in as <strong><?php echo $admin['admin_username']; ?></strong>. <a href="<?php echo ADMIN_PATH . 'logout'; ?>">Logout?</a></span></p>
				<hr class="margin1">
				<ul>
					<li><a href="<?php echo ADMIN_PATH . 'categories'; ?>">Categories</a></li>
					<li><a href="<?php echo ADMIN_PATH . 'products'; ?>">Products</a></li>
					<li><a href="<?php echo ADMIN_PATH . 'reviews'; ?>">Reviews</a></li>
					<li><a href="<?php echo ADMIN_PATH . 'customers'; ?>">Customers</a></li>
					<li><a href="<?php echo ADMIN_PATH . 'managers'; ?>">Managers</a></li>
					<li><a href="<?php echo ADMIN_PATH . 'currencies'; ?>">Currencies</a></li>
					<li><a href="<?php echo ADMIN_PATH . 'settings'; ?>">Settings</a></li>
					<li><a href="<?php echo ADMIN_PATH . 'reset'; ?>">Reset</a></li>
				</ul>
				<div class="cleaner">&nbsp;</div>
			</div>
			<?php } ?>

			<section id="main">