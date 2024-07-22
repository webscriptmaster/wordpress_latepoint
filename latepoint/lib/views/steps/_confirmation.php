<div class="step-confirmation-w latepoint-step-content" data-step-name="confirmation">
  <?php do_action('latepoint_step_confirmation_before', $booking); ?>
  <div class="confirmation-head-info">
    <?php do_action('latepoint_step_confirmation_head_info_before', $booking); ?>
    <div class="confirmation-number"><?php _e('Confirmation #', 'latepoint'); ?> <strong><?php echo $booking->booking_code; ?></strong></div>
	  <div class="booking-confirmation-actions">
		  <div class="add-to-calendar-wrapper">
		    <a href="#" class="open-calendar-types ical-download-btn"><i class="latepoint-icon latepoint-icon-calendar"></i><span><?php _e('Add to Calendar', 'latepoint'); ?></span></a>
			  <?php echo OsBookingHelper::generate_add_to_calendar_links($booking); ?>
		  </div>
	    <a href="<?php echo $booking->print_link; ?>" class="print-booking-btn" target="_blank"><i class="latepoint-icon latepoint-icon-printer"></i><span><?php _e('Print', 'latepoint'); ?></span></a>
	  </div>
    <?php do_action('latepoint_step_confirmation_head_info_after', $booking); ?>
  </div>
  <div class="confirmation-info-w">
	  <?php include('partials/_booking_summary.php'); ?>
  </div>
  <?php
  // Tracking code
  if(!empty(OsSettingsHelper::get_settings_value('confirmation_step_tracking_code', ''))){
    echo '<div style="display: none;">'.OsReplacerHelper::replace_tracking_vars(OsSettingsHelper::get_settings_value('confirmation_step_tracking_code'), $booking).'</div>';
  }
  ?>
  <?php
  // show "create account" prompt where they can set a password for their account
  if(!empty($customer) && $customer->is_guest && (OsSettingsHelper::get_settings_value('steps_hide_registration_prompt') != 'on') && !OsSettingsHelper::is_on('steps_hide_login_register_tabs')){ ?>
    <div class="step-confirmation-set-password">
      <div class="set-password-fields">
        <?php echo OsFormHelper::password_field('customer[password]', __('Set Your Password', 'latepoint')); ?>
        <a href="#" class="latepoint-btn latepoint-btn-primary set-customer-password-btn" data-btn-action="<?php echo OsRouterHelper::build_route_name('customer_cabinet', 'set_account_password_on_booking_completion'); ?>"><?php _e('Save', 'latepoint'); ?></a>
      </div>
      <?php echo OsFormHelper::hidden_field('account_nonse', $customer->account_nonse); ?>
    </div>
    <div class="confirmation-cabinet-info">
    	<div class="confirmation-cabinet-text"><?php _e('You can now manage your appointments in your personal cabinet', 'latepoint'); ?></div>
    	<div class="confirmation-cabinet-link-w">
    		<a href="<?php echo OsSettingsHelper::get_customer_dashboard_url(); ?>" class="confirmation-cabinet-link" target="_blank"><?php _e('Open My Cabinet', 'latepoint'); ?></a>
    	</div>
    </div>
    <div class="info-box text-center">
      <?php _e('Did you know that you can create an account to manage your reservations and schedule new appointments?', 'latepoint'); ?>
      <div class="info-box-buttons">
        <a href="#" class="show-set-password-fields"><?php _e('Create Account', 'latepoint'); ?></a>
      </div>
    </div>
  <?php } ?>
</div>