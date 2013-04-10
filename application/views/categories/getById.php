<article>

	<h1><?php echo $category['category_name']; ?></h1>

	<div class="items">

	<?php foreach ($products as $productByCat) { ?>
		<div class="itemEntry">
			<a href="<?php echo BASE_PATH . '/products/getById/' . $productByCat['product_ID']; ?>">
				<h3><?php echo $productByCat['product_name'] ?></h3>
				<p>
					<?php if ($productByCat['product_image'] != null) {
						echo '<img src="' . BASE_PATH . '/templates/img/products/' . $productByCat['product_image'] . '">';
					} else {
						echo '<img src="' . BASE_PATH . '/templates/img/' . 'NA.png' . '">';
					} ?>
				</p>
			</a>
			<a href="<?php echo BASE_PATH . '/basket/insert/' . $productByCat['product_ID']; ?>" class="addToBasket">Add To Basket</a>
		</div>
	<?php } ?>

	</div>

</article>