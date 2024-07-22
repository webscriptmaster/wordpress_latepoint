<?php
/**
 * @var $available_calendars array
 */
?>
<div class="latepoint-settings-w os-form-w">
  <form action="" data-os-action="<?php echo OsRouterHelper::build_route_name('settings', 'update'); ?>">
		<div class="os-section-header"><h3><?php _e('External Calendars', 'latepoint'); ?></h3></div>
		<?php
		if($available_calendars){
			echo '<div class="os-togglable-items-w">';
				foreach($available_calendars as $calendar){ ?>
			      <div class="os-togglable-item-w">
			        <div class="os-togglable-item-head">
			          <div class="os-toggler-w">
			            <?php echo OsFormHelper::toggler_field('settings[enable_'.$calendar['code'].']', false, OsCalendarHelper::is_external_calendar_enabled($calendar['code']), 'toggleCalendarSettings_'.$calendar['code'], 'large'); ?>
			          </div>
			          <?php if(!empty($calendar['image_url'])) echo '<img class="os-togglable-item-logo-img" src="'.$calendar['image_url'].'"/>'; ?>
			          <div class="os-togglable-item-name"><?php echo $calendar['name'] ?></div>
			        </div>
			        <div class="os-togglable-item-body" style="<?php echo OsCalendarHelper::is_external_calendar_enabled($calendar['code']) ? '' : 'display: none'; ?>" id="toggleCalendarSettings_<?php echo $calendar['code']; ?>">
			          <?php
								/**
								 * Hook your external calendar settings here
								 *
								 * @since 4.7.0
								 * @hook latepoint_external_calendar_settings
								 *
								 * @param {string} Code of the external calendar
								 */
			          do_action('latepoint_external_calendar_settings', $calendar['code']); ?>
			        </div>
			      </div>
				  <?php
				}
			echo '</div>';
	    echo '<div class="os-form-buttons">';
	      echo OsFormHelper::button('submit', __('Save Settings', 'latepoint'), 'submit', ['class' => 'latepoint-btn']);
	    echo '</div>';
		}else{
			echo OsAddonsHelper::generate_missing_addon_link(__('Install Google Calendar add-on', 'latepoint'));
		} ?>
  </form>
</div>