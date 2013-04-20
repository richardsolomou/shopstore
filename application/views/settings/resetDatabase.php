<article>

	<h1> <?php echo $pageTitle ?></h1>

	<div class="alert"><strong>WARNING:</strong> This is irreversible.</div>

	<p>Are you sure you would like to reset the database?</p>

	<form action="<?php echo BASE_PATH . '/settings/resetDatabase'; ?>" method="post">

		<input type="hidden" id="operation" name="operation" value="true">

		<div class="centered">
		<input type="submit" class="btn" name="submit" value="Yes">
		<a href="<?php echo BASE_PATH . '/settings/getList'; ?>" class="btn">No</a>
		</div>

	</form>

</article>