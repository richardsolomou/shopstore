<article>

	<h1><?php echo $category['category_name']; ?></h1>

	<div class="items">

	<?php foreach ($products as $productByCat) { ?>
		<?php $image = ($productByCat['product_image'] != null) ? BASE_PATH . '/templates/img/products/' . $productByCat['product_ID'] . $productByCat['product_image'] : BASE_PATH . '/templates/img/' . 'NA.png'; ?>
		<div class="itemEntry" style="background: url('<?php echo $image; ?>');">
			<a href="<?php echo BASE_PATH . '/products/getById/' . $productByCat['product_ID']; ?>"><h3><?php echo $productByCat['product_name'] ?></h3></a>
			<a href="<?php echo BASE_PATH . '/basket/insert/' . $productByCat['product_ID']; ?>" class="addToBasket">Add To Basket</a>
		</div>
	<?php } ?>

	</div>

</article>