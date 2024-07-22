<?php
/* @var $job OsProcessJobModel */
/* @var $action_settings_html string */
/* @var $preview_html string */
/* @var $action_status_html string */
/* @var $action \LatePoint\Misc\ProcessAction */
?>
<div class="latepoint-lightbox-heading">
	<h2><?php echo $action->get_nice_type_name(); ?></h2>
</div>
<div class="latepoint-lightbox-content no-padding">
	<?php echo $action_status_html; ?>
	<div class="action-preview-wrapper type-<?php echo $action->type; ?>">
		<?php echo $preview_html; ?>
	</div>
</div>
<div class="latepoint-lightbox-footer right-aligned">
	<button type="button" data-os-after-call="reload_process_jobs_table" class="latepoint-btn latepoint-btn-primary" data-os-params="<?php echo OsUtilHelper::build_os_params(['action_ids' => [$action->id], 'job_id' => $job->id]); ?>" data-os-action="<?php echo OsRouterHelper::build_route_name('process_jobs', 'run_job');?>">
		<?php if($action_status_html){ ?>
			<i class="latepoint-icon latepoint-icon-refresh-cw"></i>
			<span><?php _e('Run Again', 'latepoint'); ?></span>
		<?php }else{ ?>
			<i class="latepoint-icon latepoint-icon-play-circle"></i>
			<span><?php _e('Run Now', 'latepoint'); ?></span>
		<?php } ?>
	</button>
</div>