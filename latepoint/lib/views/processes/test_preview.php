<?php
/* @var $process OsProcessModel */
/* @var $action_settings_html string */
/* @var $action \LatePoint\Misc\ProcessAction */
?>
<div class="latepoint-lightbox-heading">
	<h2><?php echo $process->name.' '.__('Test', 'latepoint'); ?></h2>
</div>
<div class="latepoint-lightbox-content no-padding">
	<div class="action-settings-wrapper">
		<?php echo $action_settings_html ?>
	</div>
	<div class="action-preview-wrapper">
		<h3><?php _e('Actions to trigger:', 'latepoint'); ?></h3>
		<div class="actions-to-run-wrapper">
		<?php
		if(!empty($process->actions)){
			foreach($process->actions as $action){
				if($action->status != LATEPOINT_STATUS_ACTIVE) continue;
				echo '<div class="action-to-run" data-id="'.$action->id.'">'.OsFormHelper::toggler_field('action['.$action->id.']', $action->get_nice_type_name(), true).'</div>';
			}
		}else{
			echo '<div class="latepoint-message latepoint-message-subtle">'.__('No actions were created for this process. Create actions first in order to test them.', 'latepoint').'</div>';
		}
		?>
		</div>
	</div>
</div>
<div class="latepoint-lightbox-footer right-aligned">
	<button type="button" class="latepoint-btn latepoint-btn-primary latepoint-run-process-btn" data-route="<?php echo OsRouterHelper::build_route_name('processes', 'test_run');?>">
		<i class="latepoint-icon latepoint-icon-play-circle"></i>
		<span><?php _e('Run Now', 'latepoint'); ?></span>
	</button>
</div>