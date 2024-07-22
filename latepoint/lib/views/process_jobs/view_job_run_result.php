<?php
/*
 * Copyright (c) 2022 LatePoint LLC. All rights reserved.
 */

/* @var $process_name string */
/* @var $content_html string */
/* @var $meta_html string */
/* @var $status_html string */
/* @var $status string */
?>
<div class="latepoint-lightbox-heading">
	<h2><?php echo $process_name ?></h2>
</div>
<div class="latepoint-lightbox-content no-padding">
	<div class="activity-status-wrapper status-<?php echo $status; ?>">
		<div class="activity-status-content">
			<?php echo $status_html ?>
		</div>
	</div>
	<div class="activity-preview-wrapper">
		<div class="activity-preview-content-wrapper">
			<?php echo $meta_html ?>
			<?php echo $content_html; ?>
		</div>
	</div>
</div>