<html>
	<head>
		<title><?php echo $metaTitle; ?></title>
	</head>
	<body>
		<h1><?php echo $pageTitle ? $pageTitle : $metaTitle; ?></h1>
		<hr>
		<?php NOCMS::the_content(); ?>
	</body>
</html>