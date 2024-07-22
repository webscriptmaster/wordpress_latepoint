<div class="latepoint-system-status-w">
	<div class="os-accordion-wrapper">
		<div class="os-accordion-title">
			<i class="latepoint-icon latepoint-icon-file-text"></i>
			<h3><?php _e('System Info', 'latepoint'); ?></h3></div>
		<div class="os-accordion-content">
			<ul>
				<li>
					<?php
					_e('LatePoint Plugin Version:', 'latepoint'); ?> <strong><?php echo LATEPOINT_VERSION; ?></strong>
				</li>
				<li>
					<?php
					_e('LatePoint Database Version:', 'latepoint'); ?>
					<strong><?php echo OsSettingsHelper::get_db_version(); ?></strong>
					<?php echo '<a href="#" class="reset-db-version-link" data-os-action="' . OsRouterHelper::build_route_name('debug', 'reset_plugin_db_version') . '" 
					                      data-os-success-action="reload"><i class="latepoint-icon latepoint-icon-refresh-cw"></i><span>' . __('reset', 'latepoint') . '</span></a>'; ?>
				</li>
				<li>
					<?php
					_e('PHP Version:', 'latepoint'); ?> <strong><?php echo phpversion(); ?></strong>
				</li>
				<li>
					<?php
					global $wpdb;
					_e('MySQL Version:', 'latepoint'); ?> <strong><?php echo $wpdb->db_version(); ?></strong>
				</li>
				<li>
					<?php
					global $wpdb;
					_e('WordPress Version:', 'latepoint'); ?> <strong><?php echo get_bloginfo('version'); ?></strong>
				</li>
			</ul>
		</div>
	</div>
	<div class="os-accordion-wrapper">
		<div class="os-accordion-title">
			<i class="latepoint-icon latepoint-icon-box"></i>
			<h3><?php _e('Installed Addons', 'latepoint'); ?></h3></div>
		<div class="os-accordion-content">
			<div class="installed-addons-wrapper">
				<?php foreach ($addons as $addon) {
					if (!is_plugin_active($addon->wp_plugin_path)) continue;

					$addon_data = get_plugin_data(OsAddonsHelper::get_addon_plugin_path($addon->wp_plugin_path));
					$installed_version = (isset($addon_data['Version'])) ? $addon_data['Version'] : '1.0.0';
					$update_available_html = (version_compare($addon->version, $installed_version) > 0) ? '<span class="os-iab-update-available">' . __('Update Available', 'latepoint') . '</span>' : '';
					$current_addon_db_version = get_option($addon->wp_plugin_name . '_addon_db_version');
					echo '<div class="os-installed-addon-box">';
					echo '<h4>' . $addon->name . '</h4>';
					echo '<div class="os-iab-version-info">' . $update_available_html . '
								<span>' . __('Core:', 'latepoint') . '</span><strong>' . $installed_version . '</strong>
								<span>' . __('Database:', 'latepoint') . '</span><strong>' . $current_addon_db_version . '</strong>
								<a class="reset-db-version-link" href="#" data-os-action="' . OsRouterHelper::build_route_name('debug', 'reset_addon_db_version') . '" 
						                      data-os-params="' . OsUtilHelper::build_os_params(['plugin_name' => $addon->wp_plugin_name]) . '" 
						                      data-os-success-action="reload"><i class="latepoint-icon latepoint-icon-refresh-cw"></i><span>' . __('reset', 'latepoint') . '</span></a>
							</div>';
					echo '</div>';
				} ?>
			</div>
		</div>
	</div>
</div>