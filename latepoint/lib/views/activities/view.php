<?php
/* @var $activity_name string */
/* @var $activity_type string */
/* @var $content_html string */
/* @var $meta_html string */
/* @var $status_html string */
/* @var $status string */
?>
<div class="latepoint-lightbox-heading">
	<h2><?php echo $activity_name ?></h2>
</div>
<div class="latepoint-lightbox-content no-padding">
	<?php if($status && $status_html){ ?>
	<div class="activity-status-wrapper status-<?php echo $status; ?>">
		<div class="activity-status-content">
			<?php echo $status_html ?>
		</div>
	</div>
	<?php } ?>
	<div class="activity-preview-wrapper type-<?php echo $activity_type; ?>">
		<div class="activity-preview-content-wrapper">
			<?php echo $meta_html ?>
			<?php echo $content_html; ?>
		</div>
	</div>
</div>
<div class="latepoint-lightbox-footer">
	<button type="button" class="latepoint-btn latepoint-btn-danger" data-route="<?php echo OsRouterHelper::build_route_name('activities', 'destroy');?>">
		<i class="latepoint-icon latepoint-icon-trash"></i>
		<span><?php _e('Delete', 'latepoint'); ?></span>
	</button>
</div>