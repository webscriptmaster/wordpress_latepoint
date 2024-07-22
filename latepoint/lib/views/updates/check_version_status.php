<?php if ($version_info['extra_html']) { ?>
	<?php echo '<div>' . $version_info['extra_html'] . '</div>'; ?>
<?php } ?>
<?php if (version_compare($version_info['version'], LATEPOINT_VERSION) > 0) { ?>
	<div class="new-version-message">
		<h3><?php _e('Update is Available', 'latepoint') ?></h3>
		<div class="version-warn-icon"></div>
		<div class="new-version-info">
			<div class="version-info-text">
				<span><?php echo sprintf(__('LatePoint %s is available.', 'latepoint'), '<strong>'.$version_info['version'].'</strong>'); ?></span>
				<span><?php echo sprintf(__('You\'re running version %s', 'latepoint'), '<strong>'.LATEPOINT_VERSION.'</strong>'); ?></span>
			</div>
			<div class="version-buttons-w">


			<?php if (OsLicenseHelper::is_license_active()) { ?>
				<a class="update-latepoint-btn" href="#" data-os-success-action="reload"
				   data-os-action="<?php echo OsRouterHelper::build_route_name('updates', 'update_plugin'); ?>">
					<i class="latepoint-icon latepoint-icon-grid-18"></i>
					<span><?php _e('Update Now', 'latepoint'); ?></span>
				</a>
			<?php } else {
				echo '<span class="key-prompt">Enter your purchase key below to enable updates</span>';
			} ?>
			<a href="https://latepoint.com/changelog/" target="_blank" class="view-changelog-link">
				<i class="latepoint-icon latepoint-icon-external-link"></i>
				<span><?php _e('View Changelog', 'latepoint'); ?></span>
			</a>
			</div>
		</div>
		<div class="new-version-update-prompt">
		</div>
	</div>
<?php } else { ?>
	<div class="new-version-message is-latest">
		<h3><?php _e('You are using the latest version', 'latepoint') ?></h3>
		<div class="version-check-icon"></div>
		<div class="current-version-info">
			<span><?php _e('Installed Version: ', 'latepoint') ?><strong><?php echo LATEPOINT_VERSION; ?></strong></span>
		</div>
		<div class="version-buttons-w">
			<a href="https://latepoint.com/changelog/" target="_blank" class="view-changelog-link">
				<i class="latepoint-icon latepoint-icon-external-link"></i>
				<span><?php _e('View Changelog', 'latepoint'); ?></span>
			</a>
			<a href="<?php echo $version_info['link']; ?>" target="_blank">
				<i class="latepoint-icon latepoint-icon-globe"></i>
				<span><?php _e('Official Website', 'latepoint'); ?></span>
			</a>
		</div>
	</div>
<?php } ?>