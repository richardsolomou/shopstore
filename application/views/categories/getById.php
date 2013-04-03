<article>

	<h1>
		<?php
			echo $category['category_name'];
			if (isset($_SESSION['SESS_ADMINLOGGEDIN'])) echo '<a href="' . ADMIN_PATH . 'categories' . DS . 'edit' . DS . $category['category_ID'] . '" class="btn pull-right small">Edit</a>';
		?>
	</h1>

	<?php if (isset($message) && isset($alert)) { ?>
		<div class="alert <?php echo $alert ?>"><?php echo $message ?></div>
	<?php } ?>

	<div class="items">

	<?php foreach ($products as $productByCat) { ?>
		<div class="itemEntry">
			<a href="<?php echo BASE_PATH . DS . 'products' . DS . 'getById' . DS . $productByCat['product_ID']; ?>">
				<h3><?php echo $productByCat['product_name'] ?></h3>
				<p>
					<?php if ($image = $productByCat['product_image']) {
						echo '<img src="' . $image . '">';
					} else {
						echo '<img src="' . BASE_PATH . DS . 'templates' . DS . 'img' . DS . 'NA.png' . '">';
					} ?>
				</p>
			</a>
			<a href="<?php echo BASE_PATH . DS . 'basket' . DS . 'insert' . DS . $productByCat['product_ID']; ?>" class="addToBasket">Add To Basket</a>
		</div>
	<?php } ?>

	</div>

</article>