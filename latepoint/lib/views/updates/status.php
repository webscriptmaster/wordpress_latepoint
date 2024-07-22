<div class="version-and-license-info-w">
	<div class="version-status-info" data-route="<?php echo OsRouterHelper::build_route_name('updates', 'check_version_status') ?>">
		<span class="loading"><?php _e('Checking Version Status', 'latepoint'); ?></span>
	</div>
	<?php if($is_license_active){ ?>
		<div class="active-license-info is-active">
			<div class="version-check-icon"></div>
			<h3>License Status: <strong>Active</strong></h3>
			<div>
				<span>Thank you for using LatePoint</span>
			</div>
			<div class="license-info-buttons-w">
				<a href="#" class="latepoint-show-license-details">
					<i class="latepoint-icon latepoint-icon-file-text"></i>
					<span>License Info</span>
				</a>
				<a href="#" class="os-deactivate-license-btn" data-os-action="<?php echo OsRouterHelper::build_route_name('updates', 'remove_license'); ?>" data-os-success-action="reload" class="">
					<i class="latepoint-icon latepoint-icon-slash"></i>
					<span>Deactivate</span>
				</a>
			</div>
			<div class="license-info-w" style="display: none;">
				<ul>
					<li><span>Name</span><strong><?php echo $license['full_name']; ?></strong></li>
					<li><span>Email</span><strong><?php echo $license['email']; ?></strong></li>
					<li><span>License Key</span><strong><?php echo OsUtilHelper::obfuscate_license($license['license_key']); ?></strong></li>
				</ul>
			</div>
		</div>
	<?php }else{ ?>
		<div class="active-license-info">
			<div class="version-warn-icon"></div>
			<h3>Activate Your LatePoint License</h3>
			<div>
				<span>Register your license to install plugin updates and addons.</span>
			</div>
			<div class="license-form-w">
				<?php include('_license_form.php'); ?>
			</div>
		</div>
	<?php } ?>
</div>
<div class="debug-info-wrapper" data-os-output-target="self" data-os-action-onload="<?php echo OsRouterHelper::build_route_name('debug', 'status') ?>"></div>