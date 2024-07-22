<!DOCTYPE html>
<html lang="en" class="latepoint-clean">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>LatePoint</title>
	<link rel="stylesheet" href="<?php echo LATEPOINT_STYLESHEETS_URL . 'front.css'; ?>">
	<?php wp_head(); ?>
</head>
<body class="latepoint-clean-body with-pattern latepoint">
<div class="latepoint-w">
	<?php include($view); ?>
</div>
</body>
</html>