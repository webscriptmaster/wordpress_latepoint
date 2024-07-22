<?php
/**
 * @var $booking OsBookingModel
 * @var $is_upcoming_booking bool
 */
?>
<div class="customer-booking status-<?php echo $booking->status; ?>" data-id="<?php echo $booking->id; ?>" data-route-name="<?php echo OsRouterHelper::build_route_name('customer_cabinet', 'reload_booking_tile'); ?>">
	<h6 class="customer-booking-service-name"><?php echo $booking->service->name; ?></h6>
	<div class="customer-booking-datetime">
  <?php
  if($booking->start_date){
	  $booking_start_datetime = $booking->get_nice_start_datetime();
	  $booking_start_datetime = apply_filters('latepoint_booking_summary_formatted_booking_start_datetime', $booking_start_datetime, $booking);
		echo $booking_start_datetime;
  }
	?>
	</div>

	<?php if($is_upcoming_booking){ ?>
		<div class="customer-booking-buttons">
			<?php if(OsCustomerHelper::can_reschedule_booking($booking)){ ?>
				<a href="#" class="latepoint-btn latepoint-btn-primary latepoint-request-booking-reschedule latepoint-btn-link" data-os-after-call="latepoint_init_reschedule" data-os-lightbox-classes="width-400 reschedule-calendar-wrapper" data-os-action="<?php echo OsRouterHelper::build_route_name('customer_cabinet', 'request_reschedule_calendar'); ?>" data-os-params="<?php echo OsUtilHelper::build_os_params(['booking_id' => $booking->id]) ?>" data-os-output-target="lightbox">
					<span><?php _e('Reschedule', 'latepoint'); ?></span>
				</a>
			<?php } ?>
			<?php if(OsCustomerHelper::can_cancel_booking($booking)){ ?>
				<a href="#" class="latepoint-btn latepoint-btn-danger latepoint-btn-link"
				   data-os-prompt="<?php _e('Are you sure you want to cancel this appointment?', 'latepoint'); ?>"
					   data-os-success-action="reload"
					   data-os-action="<?php echo OsRouterHelper::build_route_name('customer_cabinet', 'request_cancellation'); ?>"
					   data-os-params="<?php echo OsUtilHelper::build_os_params(['id' => $booking->id]) ?>"
					<i class="latepoint-icon latepoint-icon-ui-24"></i>
					<span><?php _e('Cancel', 'latepoint'); ?></span>
				</a>
			<?php } ?>
		</div>
	<?php } ?>
		<div class="customer-booking-service-color"></div>

	<div class="customer-booking-info">
		<div class="customer-booking-info-row">
			<span class="booking-info-label"><?php _e('Agent', 'latepoint'); ?></span>
			<span class="booking-info-value"><?php echo $booking->agent->full_name; ?></span>
		</div>
		<div class="customer-booking-info-row">
			<span class="booking-info-label"><?php _e('Status', 'latepoint'); ?></span>
			<span class="booking-info-value status-<?php echo $booking->status; ?>"><?php echo $booking->nice_status; ?></span>
		</div>
		<?php do_action('latepoint_customer_dashboard_after_booking_info_tile', $booking); ?>
	</div>
	<div class="customer-booking-bottom-actions">
		<?php if($is_upcoming_booking){ ?>
			<div class="add-to-calendar-wrapper">
				<a href="#" class="open-calendar-types latepoint-btn latepoint-btn-primary latepoint-btn-outline latepoint-btn-block">
					<i class="latepoint-icon latepoint-icon-plus-circle"></i>
					<span><?php _e('Add to Calendar', 'latepoint'); ?></span>
				</a>
				<?php echo OsBookingHelper::generate_add_to_calendar_links($booking); ?>
			</div>
		<?php } ?>
		<div class="load-booking-summary-btn-w">
			<a href="#"
			   class="latepoint-btn latepoint-btn-primary latepoint-btn-outline latepoint-btn-block"
			   data-os-after-call="latepoint_init_booking_summary_lightbox"
			   data-os-params="<?php echo OsUtilHelper::build_os_params(['booking_id' => $booking->id]) ?>"
			   data-os-action="<?php echo OsRouterHelper::build_route_name('customer_cabinet', 'view_summary_in_lightbox'); ?>"
			   data-os-output-target="lightbox"
				data-os-lightbox-classes="width-500 customer-dashboard-booking-summary-lightbox">
				<i class="latepoint-icon latepoint-icon-list"></i>
				<span><?php _e('Summary', 'latepoint'); ?></span>
			</a>
		</div>
	</div>
</div>