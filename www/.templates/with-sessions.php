<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title><?php echo $metaTitle; ?></title>
		<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
		<script>
		$($.getJSON("/stuff/ajax.php", function (data) {
				$.each(data, function (k, v) {
					$('#' + k).html(v);
				})
			}
		));
		</script>
	</head>
	<body>
		<h1><?php echo $pageTitle ? $pageTitle : $metaTitle; ?></h1>
		<?php NOCMS::the_content(); ?>
	</body>
</html>