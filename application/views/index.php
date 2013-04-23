<article>

	<?php
		// Get the top five products.
		$topProducts = array();
		$productReviewNumberDispatch = new ProductsController('products', '_getProductReviewNumber');
		$productReviewsDispatch = new ProductsController('products', '_getProductReviews');
		$reviewsExist = 0;
		foreach ($products as $product) {
			$productReviewNumber = $productReviewNumberDispatch->_getProductReviewNumber($product['product_ID']);
			if ($productReviewNumber > 0) $reviewsExist = 1;
			$productReviews = $productReviewsDispatch->_getProductReviews($product['product_ID']);
			$reviewRatingAverage = 0;
			foreach ($productReviews as $productReview) {
				$reviewRatingAverage += $productReview['review_rating'] / $productReviewNumber;
			}
			$productAndReview = array('product_ID' => $product['product_ID'], 'review_rating' => $reviewRatingAverage);
			$topProducts = array_merge($topProducts, array($productAndReview));
		}
		if ($reviewsExist == 1) {
			$topProductRating = 0;
			foreach ($topProducts as $topProduct) {
				if ($topProduct['review_rating'] >= $topProductRating) {
					$bestRatedProductID = $topProduct['product_ID'];
					$topProductRating = $topProduct['review_rating'];
				}
			}
			$bestRatedProductDispatch = new ProductsController('products', '_getProductById');
			$bestRatedProduct = $bestRatedProductDispatch->_getProductById($bestRatedProductID);
			$bestRatedProductImage = ($bestRatedProduct['product_image'] != null) ? BASE_PATH . '/templates/img/products/' . $bestRatedProduct['product_ID'] . $bestRatedProduct['product_image'] : BASE_PATH . '/templates/img/' . 'NA.png';
	?>
		<h1>Best Rated Product</h1>
		<div class="itemEntry" style="background: url('<?php echo $bestRatedProductImage; ?>') no-repeat;" onclick="location.href='<?php echo BASE_PATH . '/products/getById/' . $bestRatedProduct['product_ID']; ?>'">
			<h3><?php echo $bestRatedProduct['product_name'] ?></h3>
		</div>
		<div class="cleaner">&nbsp;</div>
		<p>&nbsp;</p>
	<?php } ?>

	<h1>Categories</h1>

	<?php
		// Gets all the categories.
		$categoryDispatch = new CategoriesController('categories', '_getProductCountByCat');
		foreach ($categories as $category) {
			 // Runs the method on every loop
            $productNumber = $categoryDispatch->_getProductCountByCat($category['category_ID']);
            if ($productNumber > 0) {
	?>
		<a class="highlight" href="<?php echo BASE_PATH . '/categories/getById/' . $category['category_ID']; ?>"><?php echo $category['category_name'] ?></a>&nbsp;
	<?php
			}
		}
	?>

	<div class="cleaner">&nbsp;</div>
	<p>&nbsp;</p>
	<h1>Products</h1>

	<div class="items">

	<?php foreach ($products as $product) { ?>
		<?php $image = ($product['product_image'] != null) ? BASE_PATH . '/templates/img/products/' . $product['product_ID'] . $product['product_image'] : BASE_PATH . '/templates/img/' . 'NA.png'; ?>
		<div class="itemEntry" style="background: url('<?php echo $image; ?>') no-repeat;" onclick="location.href='<?php echo BASE_PATH . '/products/getById/' . $product['product_ID']; ?>'">
			<h3><?php echo $product['product_name'] ?></h3>
		</div>
	<?php } ?>

	</div>	

</article>