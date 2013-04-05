<!DOCTYPE HTML>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title><?php echo $title; ?></title>
		<link rel="stylesheet" href="<?php echo BASE_PATH . '/templates/css/style.css'; ?>" media="all">
		<!--[if lt IE 9]>
				<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
		<script src="<?php echo BASE_PATH . '/templates/js/layercms.js'; ?>"></script>
	</head>
	<body>

		<div id="wrapper">

			<header>

				<h1><a href="<?php echo BASE_PATH; ?>"><?php echo $title; ?></a></h1>

			</header>

			<?php if (isset($admin)) { ?>
			<div class="adminBar">
				<p>Welcome <?php echo $admin['admin_firstname'] . ' ' . $admin['admin_lastname']; ?><span class='pull-right'>Currently logged in as <strong><?php echo $admin['admin_username']; ?></strong>. <a href="<?php echo BASE_PATH . '/logout'; ?>">Logout?</a></span></p>
				<hr class="margin1">
				<ul>
					<li><a href="<?php echo BASE_PATH . '/categories/getList'; ?>">Categories</a></li>
					<li><a href="<?php echo BASE_PATH . '/products/getList'; ?>">Products</a></li>
					<li><a href="<?php echo BASE_PATH . '/reviews/getList'; ?>">Reviews</a></li>
					<li><a href="<?php echo BASE_PATH . '/customers/getList'; ?>">Customers</a></li>
					<li><a href="<?php echo BASE_PATH . '/administrators/getList'; ?>">Administrators</a></li>
					<li><a href="<?php echo BASE_PATH . '/currencies/getList'; ?>">Currencies</a></li>
					<li><a href="<?php echo BASE_PATH . '/settings/getList'; ?>">Settings</a></li>
				</ul>
				<div class="cleaner">&nbsp;</div>
			</div>
			<?php } ?>

			<section id="main">