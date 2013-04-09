<?php if ($count > 0) { ?>
	<ul>
		<?php foreach ($search as $productSearch) { ?>
			<li><a href="<?php echo BASE_PATH . '/products/getById/' . $productSearch['product_ID']; ?>"><?php echo $productSearch['product_name']; ?></a></li>
		<?php } ?>
	</ul>
<?php } else { ?>
	<p>No suggestions</p>
<?php } ?>