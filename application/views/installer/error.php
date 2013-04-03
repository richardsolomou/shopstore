<article id="full">

    <h1><?php echo $title ?></h1>

    <?php foreach ($error as $err) { ?>
    	<div class="alert"><?php echo $err; ?></div>
	<?php } ?>

    <a href="<?php echo BASE_PATH . DS . 'installer'; ?>" class="btn">Back to Installer</a>

</article>