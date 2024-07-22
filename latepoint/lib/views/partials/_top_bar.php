<div class="latepoint-top-bar-w">
	<a href="#" title="<?php _e('Menu', 'latepoint'); ?>" class="latepoint-top-iconed-link latepoint-mobile-top-menu-trigger">
		<i class="latepoint-icon latepoint-icon-menu"></i>
	</a>
	<div class="latepoint-top-search-w">
		<div class="latepoint-top-search-input-w">
			<i class="latepoint-icon latepoint-icon-x latepoint-mobile-top-search-trigger-cancel"></i>
			<input type="text" data-route="<?php echo OsRouterHelper::build_route_name('search', 'query_results') ?>"
			       class="latepoint-top-search"
			       placeholder="<?php _e('Start typing to find bookings, customers, agents or services...', 'latepoint'); ?>">
		</div>
		<div class="latepoint-top-search-results-w"></div>
	</div>
	<a href="#" title="<?php _e('Search', 'latepoint'); ?>"
	   class="latepoint-top-iconed-link latepoint-mobile-top-search-trigger"><i
			class="latepoint-icon latepoint-icon-search"></i></a>
	<?php do_action('latepoint_top_bar_before_actions'); ?>
	<a href="<?php echo OsRouterHelper::build_link(['activities', 'index']); ?>"
	   title="<?php _e('Activity Log', 'latepoint'); ?>"
	   class="latepoint-top-iconed-link latepoint-top-activity-trigger">
		<i class="latepoint-icon latepoint-icon-activity"></i>
	</a>
	<a href="<?php echo OsRouterHelper::build_link(['bookings', 'pending_approval']); ?>"
	   title="<?php _e('Pending Bookings', 'latepoint'); ?>"
	   class="latepoint-top-iconed-link latepoint-top-notifications-trigger">
		<i class="latepoint-icon latepoint-icon-inbox"></i>
		<?php
		$count_pending_bookings = OsBookingHelper::count_pending_bookings();
		if ($count_pending_bookings > 0) echo '<span class="notifications-count">' . $count_pending_bookings . '</span>'; ?>
	</a>
	<a href="#" <?php echo OsBookingHelper::quick_booking_btn_html(); ?>
	   title="<?php _e('New Appointment', 'latepoint'); ?>"
	   class="latepoint-mobile-top-new-appointment-btn-trigger latepoint-top-iconed-link">
		<i class="latepoint-icon latepoint-icon-plus-circle"></i>
	</a>
	<?php do_action('latepoint_top_bar_after_actions'); ?>
	<a href="#"
	   class="latepoint-top-new-appointment-btn latepoint-btn latepoint-btn-primary" <?php echo OsBookingHelper::quick_booking_btn_html(); ?>>
		<i class="latepoint-icon latepoint-icon-plus"></i>
		<span><?php _e('New Booking', 'latepoint'); ?></span>
	</a>
	<div class="latepoint-top-user-info-w">
		<div class="avatar-w top-user-info-toggler" style="background-image: url('<?php echo OsAuthHelper::get_current_user()->get_avatar_url(); ?>');"></div>
		<div class="latepoint-user-info-dropdown">
			<a href="#" class="latepoint-user-info-close top-user-info-toggler"><i class="latepoint-icon latepoint-icon-x"></i></a>
			<div class="latepoint-uid-head">
				<div class="uid-avatar-w">
					<div class="uid-avatar"
					     style="background-image: url('<?php echo OsAuthHelper::get_current_user()->get_avatar_url(); ?>');"></div>
				</div>
				<div class="uid-info">
					<h4><?php echo OsAuthHelper::get_current_user()->get_display_name(); ?></h4>
					<h5><?php echo OsAuthHelper::get_current_user()->get_user_type_label(); ?></h5>
				</div>
			</div>
			<?php do_action('latepoint_top_bar_mobile_after_user'); ?>
			<ul>
				<?php if (OsAuthHelper::get_current_user()->get_link_to_settings()) { ?>
					<li>
						<a href="<?php echo OsAuthHelper::get_current_user()->get_link_to_settings(); ?>">
							<i class="latepoint-icon latepoint-icon-ui-46"></i>
							<span><?php _e('Settings', 'latepoint'); ?></span>
						</a>
					</li>
				<?php } ?>
				<li>
					<a href="<?php echo wp_logout_url(); ?>">
						<i class="latepoint-icon latepoint-icon-log-in"></i>
						<span><?php _e('Logout', 'latepoint'); ?></span>
					</a>
				</li>
			</ul>
		</div>
	</div>
</div>