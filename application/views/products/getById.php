<article>

	<h1>
		<?php
			echo $product['product_name'];
			if (isset($_SESSION['SESS_ADMINLOGGEDIN'])) echo '<a href="' . BASE_PATH . '/products/update/' . $product['product_ID'] . '" class="btn pull-right small">Edit</a>';
		?>
	</h1>

	<div class="items">

        <table>
            <tr>
            	<td>
            		<?php if ($product['product_image'] != null) {
                        echo '<img src="' . BASE_PATH . '/templates/img/products/' . $product['product_image'] . '">';
                    } else {
                        echo '<img src="' . BASE_PATH . '/templates/img/' . 'NA.png' . '">';
                    } ?>
            	</td>
            	<td class="centered">
            		<p><strong>Category:</strong> <?php echo $productCategory; ?></p>
            		<p><strong>Price:</strong> <?php echo $currencySymbol . $product['product_price']; ?></p>
                	<?php
	                	if ($reviewNumber > 0) {
	                		echo '<p><strong>Rating:</strong><br>';
	                		for ($x = 1; $x <= $reviewRatingAverage ; $x++) {
						        echo '<img src="' . BASE_PATH . '/templates/img/' . 'star.png">';
						    }
						    while ($x <= 5) {
						        echo '<img src="' . BASE_PATH . '/templates/img/' . 'blankstar.png">';
						        $x++;
						    }
						    echo '</p>';
						} else {
							echo 'This item hasn\'t been rated yet.<br><a href="' . BASE_PATH . '/reviews/insert/' . $product['product_ID'] . '">Be the first!</a>';
						}
					?>
					<?php if ($product['product_stock'] != 0) { ?>
        			<p><a href="<?php echo BASE_PATH . '/basket/insert/' . $product['product_ID']; ?>" class="highlight big">Add to Basket</a></p>
        			<?php } ?>
				</td>
            </tr>
            <tr>
            	<td colspan="2">
            		<p><strong>Description:</strong></p>
            		<?php echo $product['product_description']; ?>
            	</td>
            </tr>
            <tr>
            	<td colspan="2">
            		<?php if ($product['product_stock'] != 0) { ?>
            			<p><span="green"><strong>In stock</strong></span>: <?php echo $product['product_stock']; ?></p>
            		<?php } else { ?>
            			<p><span="red"><strong>Out of stock</strong></span></p>
            		<?php } ?>
            	</td>
            </tr>
        </table>
	    <hr>
		<p class="centered"><a href="<?php echo BASE_PATH . '/reviews/insert/' . $product['product_ID']; ?>" class="btn">Review this product</a></p>
	    <?php if ($reviewNumber > 0) { ?>
	    	<hr>
	    	<h2>Reviews</h2>
	    	<?php foreach ($reviews as $review) { ?>
	    		<?php $customer = $customerDispatch->_getCustomerById($review['customer_ID']); ?>
				<table class="bordered">
					<tr>
						<td><strong>By:</strong> <?php echo $customer['customer_firstname'] . " " . $customer['customer_lastname']; ?></td>
						<td class="righted"><strong>Subject:</strong> <?php echo $review['review_subject']; ?></td>
					</tr>
					<tr><td colspan="2"><?php echo $review['review_description']; ?></td></tr>
					<tr><td colspan="2" class="centered"><strong>Rating:</strong><br>
						<?php
	                		for($x = 1; $x <= $review['review_rating']; $x++) {
						        echo '<img src="' . BASE_PATH . '/templates/img/' . 'star.png">';
						    }
						    while ($x <= 5) {
						        echo '<img src="' . BASE_PATH . '/templates/img/' . 'blankstar.png">';
						        $x++;
						    }
						?></td></tr>
			    </table>
		    <?php } ?>
	    <?php } ?>
	</div>

</article>