<?php
/*
 * Copyright (c) 2023 LatePoint LLC. All rights reserved.
 */

/**
 * @var $available_marketing_systems array
 */
?>
<div class="latepoint-settings-w os-form-w">
  <form action="" data-os-action="<?php echo OsRouterHelper::build_route_name('settings', 'update'); ?>">
		<div class="os-section-header"><h3><?php _e('Marketing Systems', 'latepoint'); ?></h3></div>
		<?php
		if($available_marketing_systems){
			echo '<div class="os-togglable-items-w">';
				foreach($available_marketing_systems as $marketing_system){ ?>
			      <div class="os-togglable-item-w">
			        <div class="os-togglable-item-head">
			          <div class="os-toggler-w">
			            <?php echo OsFormHelper::toggler_field('settings[enable_'.$marketing_system['code'].']', false, OsMarketingSystemsHelper::is_external_marketing_system_enabled($marketing_system['code']), 'toggleMarketingSystemSettings_'.$marketing_system['code'], 'large'); ?>
			          </div>
			          <?php if(!empty($marketing_system['image_url'])) echo '<img class="os-togglable-item-logo-img" src="'.$marketing_system['image_url'].'"/>'; ?>
			          <div class="os-togglable-item-name"><?php echo $marketing_system['name'] ?></div>
			        </div>
			        <div class="os-togglable-item-body" style="<?php echo OsMarketingSystemsHelper::is_external_marketing_system_enabled($marketing_system['code']) ? '' : 'display: none'; ?>" id="toggleMarketingSystemSettings_<?php echo $marketing_system['code']; ?>">
			          <?php
								/**
								 * Hook your marketing system settings here
								 *
								 * @since 4.7.0
								 * @hook latepoint_external_marketing_system_settings
								 *
								 * @param {string} Code of the marketing system
								 */
			          do_action('latepoint_external_marketing_system_settings', $marketing_system['code']);
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
			echo OsAddonsHelper::generate_missing_addon_link(__('Install Marketing add-ons', 'latepoint'));
		} ?>
  </form>
</div>