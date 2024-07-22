<?php
/*
 * Copyright (c) 2023 LatePoint LLC. All rights reserved.
 */

/**
 * @var $available_meeting_systems array
 */
?>
<div class="latepoint-settings-w os-form-w">
  <form action="" data-os-action="<?php echo OsRouterHelper::build_route_name('settings', 'update'); ?>">
		<div class="os-section-header"><h3><?php _e('Video Meeting Systems', 'latepoint'); ?></h3></div>
		<?php
		if($available_meeting_systems){
			echo '<div class="os-togglable-items-w">';
				foreach($available_meeting_systems as $meeting_system){ ?>
			      <div class="os-togglable-item-w">
			        <div class="os-togglable-item-head">
			          <div class="os-toggler-w">
			            <?php echo OsFormHelper::toggler_field('settings[enable_'.$meeting_system['code'].']', false, OsMeetingSystemsHelper::is_external_meeting_system_enabled($meeting_system['code']), 'toggleMeetingSystemSettings_'.$meeting_system['code'], 'large'); ?>
			          </div>
			          <?php if(!empty($meeting_system['image_url'])) echo '<img class="os-togglable-item-logo-img" src="'.$meeting_system['image_url'].'"/>'; ?>
			          <div class="os-togglable-item-name"><?php echo $meeting_system['name'] ?></div>
			        </div>
			        <div class="os-togglable-item-body" style="<?php echo OsMeetingSystemsHelper::is_external_meeting_system_enabled($meeting_system['code']) ? '' : 'display: none'; ?>" id="toggleMeetingSystemSettings_<?php echo $meeting_system['code']; ?>">
			          <?php
								/**
								 * Hook your meeting system settings here
								 *
								 * @since 4.7.0
								 * @hook latepoint_external_meeting_system_settings
								 *
								 * @param {string} Code of the meeting system
								 */
			          do_action('latepoint_external_meeting_system_settings', $meeting_system['code']);
								?>
			        </div>
			      </div>
				  <?php
				}
			echo '</div>';
	    echo '<div class="os-form-buttons">';
	      echo OsFormHelper::button('submit', __('Save Settings', 'latepoint'), 'submit', ['class' => 'latepoint-btn']);
	    echo '</div>';
		}else{
			echo OsAddonsHelper::generate_missing_addon_link(__('Install Zoom or Google Meet add-on', 'latepoint'));
		} ?>
  </form>
</div>