<?php
/* @var $action_settings_html string */
/* @var $preview_html string */
/* @var $action \LatePoint\Misc\ProcessAction */
?>
<div class="latepoint-lightbox-heading">
	<h2><?php echo $action->get_nice_type_name().' '.__('Test', 'latepoint'); ?></h2>
</div>
<div class="latepoint-lightbox-content no-padding">
	<div class="action-settings-wrapper">
		<?php echo $action_settings_html ?>
	</div>
	<div class="action-preview-wrapper type-<?php echo $action->type; ?>">
		<?php echo $preview_html; ?>
	</div>
</div>
<div class="latepoint-lightbox-footer right-aligned">
	<button type="button" class="latepoint-btn latepoint-btn-primary latepoint-run-action-btn" data-route="<?php echo OsRouterHelper::build_route_name('processes', 'action_test_run');?>">
		<i class="latepoint-icon latepoint-icon-play-circle"></i>
		<span><?php _e('Run Now', 'latepoint'); ?></span>
	</button>
</div>