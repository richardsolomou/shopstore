<article>

	<h1>Categories</h1>

	<?php foreach ($categories as $category) { ?>
		<a class="highlight" href="<?php echo BASE_PATH . '/categories/getById/' . $category['category_ID']; ?>"><?php echo $category['category_name'] ?></a>&nbsp;
	<?php } ?>

	<p>&nbsp;</p>

	<h1>Products</h1>

	<div class="items">

	<?php foreach ($products as $product) { ?>
		<?php $image = ($product['product_image'] != null) ? BASE_PATH . '/templates/img/products/' . $product['product_ID'] . $product['product_image'] : BASE_PATH . '/templates/img/' . 'NA.png'; ?>
		<div class="itemEntry" style="background: url('<?php echo $image; ?>');">
			<a href="<?php echo BASE_PATH . '/products/getById/' . $product['product_ID']; ?>"><h3><?php echo $product['product_name'] ?></h3></a>
			<a href="<?php echo BASE_PATH . '/basket/insert/' . $product['product_ID']; ?>" class="addToBasket">Add To Basket</a>
		</div>
	<?php } ?>

	</div>	

</article>