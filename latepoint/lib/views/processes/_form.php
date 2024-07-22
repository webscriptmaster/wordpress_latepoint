<?php
/* @var $process OsProcessModel */
?>
<form action=""
			data-os-action="<?php echo OsRouterHelper::build_route_name('processes', 'save'); ?>"
      data-os-after-call="latepoint_process_updated"
			class="os-process-form os-form-block os-form-block-type-<?php echo $process->event_type; ?> <?php if(empty($process->name)) echo 'os-is-editing'; ?> status-<?php echo $process->status ?>">
	<div class="os-form-block-i">
		<div class="os-form-block-header">
			<div class="os-form-block-drag"></div>
			<div class="os-form-block-name"><?php echo !empty($process->name) ? $process->name : __('New Process', 'latepoint'); ?></div>
			<div class="os-form-block-type"><?php echo $process->event_type; ?></div>
			<div class="os-form-block-edit-btn"><i class="latepoint-icon latepoint-icon-edit-3"></i></div>
		</div>
		<div class="os-form-block-params os-form-w">
      <div class="sub-section-row">
        <div class="sub-section-label">
          <h3><?php _e('Status', 'latepoint') ?></h3>
        </div>
        <div class="sub-section-content">
	        <?php echo OsFormHelper::select_field('process[status]', false, [LATEPOINT_STATUS_ACTIVE => __('Active', 'latepoint'), LATEPOINT_STATUS_DISABLED => __('Disabled', 'latepoint')], $process->status); ?>
        </div>
      </div>
      <div class="sub-section-row">
        <div class="sub-section-label">
          <h3><?php _e('Name', 'latepoint') ?></h3>
        </div>
        <div class="sub-section-content">
					<?php echo OsFormHelper::text_field('process[name]', '', $process->name, ['theme' => 'bordered', 'placeholder' => __('Process Name', 'latepoint'), 'class' => 'os-form-block-name-input']); ?>
        </div>
      </div>
      <div class="sub-section-row">
        <div class="sub-section-label">
          <h3><?php _e('Event Type', 'latepoint') ?></h3>
        </div>
        <div class="sub-section-content">
					<?php echo OsFormHelper::select_field('process[event][type]', false, \LatePoint\Misc\ProcessEvent::get_event_types_for_select(), $process->event->type,
						['class' => 'process-event-type-selector', 'data-route' => OsRouterHelper::build_route_name('processes', 'reload_event_trigger_conditions')]); ?>
        </div>
      </div>
			<div class="process-event-condition-wrapper">
				<?php echo OsProcessesHelper::trigger_conditions_html_for_event($process->event); ?>
			</div>
			<?php echo OsProcessesHelper::time_offset_html_for_event($process->event); ?>
      <div class="sub-section-row">
        <div class="sub-section-label">
          <h3><?php _e('Actions', 'latepoint') ?></h3>
        </div>
        <div class="sub-section-content">
					<?php
					if(!empty($process->actions)) {
						foreach ($process->actions as $action) {
							echo \LatePoint\Misc\ProcessAction::generate_form($action);
						}
					}
					?>
	        <a href="#" class="latepoint-btn latepoint-btn-block latepoint-btn-outline" data-os-after-call="latepoint_init_added_process_action_form" data-os-pass-this="yes" data-os-action="<?php echo OsRouterHelper::build_route_name('processes', 'new_action'); ?>" data-os-before-after="before">
		        <i class="latepoint-icon latepoint-icon-plus"></i>
		        <span><?php _e('Add Action', 'latepoint');  ?></span>
	        </a>
        </div>
      </div>
      <div class="os-form-block-buttons">
				<a href="#" class="latepoint-btn latepoint-btn-danger pull-left os-remove-process"
				   data-os-prompt="<?php _e('Are you sure you want to delete this process?', 'latepoint'); ?>"
				   data-os-after-call="latepoint_process_action_removed"
				   data-os-pass-this="yes"
				   data-os-action="<?php echo OsRouterHelper::build_route_name('processes', 'destroy'); ?>"
				   data-os-params="<?php echo OsUtilHelper::build_os_params(['id' => $process->id]) ?>"><?php _e('Delete', 'latepoint'); ?>
				</a>
	      <a href="#" class="latepoint-btn latepoint-btn-secondary os-run-process" data-route="<?php echo OsRouterHelper::build_route_name('processes', 'test_preview') ?>"><i class="latepoint-icon latepoint-icon-play-circle"></i><span><?php _e('Test this process', 'latepoint'); ?></span></a>
			  <button type="submit" class="os-form-block-save-btn latepoint-btn latepoint-btn-primary"><span><?php _e('Save Process', 'latepoint'); ?></span></button>
		  </div>
		</div>
	</div>
	<a href="#"
	   data-os-prompt="<?php _e('Are you sure you want to delete this process?', 'latepoint'); ?>"
	   data-os-after-call="latepoint_process_action_removed"
	   data-os-pass-this="yes"
	   data-os-action="<?php echo OsRouterHelper::build_route_name('processes', 'destroy'); ?>"
	   data-os-params="<?php echo OsUtilHelper::build_os_params(['id' => $process->id]) ?>" class="os-remove-form-block"><i class="latepoint-icon latepoint-icon-cross"></i></a>
	<?php echo OsFormHelper::hidden_field('process[id]', $process->id, ['class' => 'os-form-block-id']); ?>
</form>