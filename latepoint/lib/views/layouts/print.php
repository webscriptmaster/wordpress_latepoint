<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title></title>
	<link rel="stylesheet" href="<?php echo LATEPOINT_STYLESHEETS_URL . 'front.css' ?>">
</head>
<body>
<div class="latepoint-w">
	<?php include($view); ?>
</div>
<script type="text/javascript">window.onload = function () {
    window.print();
  }</script>
</body>
</html>