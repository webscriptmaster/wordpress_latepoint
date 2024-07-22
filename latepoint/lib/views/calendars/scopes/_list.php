<?php
/**
 * @var $bookings OsBookingModel[]
 * @var $calendar_start OsWpDateTime
 */

?>

<?php  if(!empty($bookings)){
	$loop_year = $calendar_start->format('Y');
	$loop_month = $calendar_start->format('n');
	$loop_day = false;
	$total_locations = OsLocationHelper::count_locations(true);
	?>
	<div class="list-upcoming-bookings-w">
		<?php
		$first_booking_start_datetime = $bookings[0]->get_start_datetime_object();
		if($first_booking_start_datetime->format('Y-n') != $loop_year.'-'.$loop_month) echo '<div class="no-upcoming-bookings">'.__('No bookings', 'latepoint').'</div>';
		?>
		<?php foreach($bookings as $booking){ ?>
			<?php
			$booking_start_datetime = $booking->get_start_datetime_object();
			if($loop_day != $booking_start_datetime->format('d')){
				$loop_day = $booking_start_datetime->format('d');
				$is_new_day = true;
			}else{
				$is_new_day = false;
			}
			if($booking_start_datetime->format('Y') > $loop_year){
				for($m = $loop_month+1; $m < 12; $m++){
					echo '<div class="upcoming-bookings-month">'.OsUtilHelper::get_month_name_by_number($m).'</div>';
					echo '<div class="no-upcoming-bookings">'.__('No bookings', 'latepoint').'</div>';
				}
				$loop_month = 1;
				for($y = $loop_year + 1; $y < $booking_start_datetime->format('Y'); $y++){
					echo '<div class="upcoming-bookings-year">'.$y.'</div>';
					// loop months
					for($m=$loop_month+1;$m<=12;$m++){
						echo '<div class="upcoming-bookings-month">'.OsUtilHelper::get_month_name_by_number($m).'</div>';
						echo '<div class="no-upcoming-bookings">'.__('No bookings', 'latepoint').'</div>';
					}
					$loop_month = 1;
				}
				$loop_year = $booking_start_datetime->format('Y');
				echo '<div class="upcoming-bookings-year">'.$loop_year.'</div>';
			}
			if($booking_start_datetime->format('n') > $loop_month){
				for($m = $loop_month + 1; $m < $booking_start_datetime->format('n'); $m++){
					echo '<div class="upcoming-bookings-month">'.OsUtilHelper::get_month_name_by_number($m).'</div>';
					echo '<div class="no-upcoming-bookings">'.__('No bookings', 'latepoint').'</div>';
				}
				$loop_month = $booking_start_datetime->format('n');
				echo '<div class="upcoming-bookings-month">'.OsUtilHelper::get_month_name_by_number($loop_month).'</div>';
			}
			?>
			<?php $max_capacity = OsServiceHelper::get_max_capacity($booking->service); ?>
			<div class="upcoming-booking <?php echo ($is_new_day) ? 'is-new-day' : ''; ?>" <?php echo ($max_capacity > 1) ? OsBookingHelper::group_booking_btn_html($booking->id) : OsBookingHelper::quick_booking_btn_html($booking->id); ?>>
				<div class="booking-main-info">
					<div class="booking-color-elem" style="background-color: <?php echo $booking->service->bg_color; ?>"></div>
					<div class="booking-fancy-date">
						<div class="fancy-day"><?php echo $booking_start_datetime->format('d'); ?></div>
						<div class="fancy-month"><?php echo OsUtilHelper::get_month_name_by_number($booking_start_datetime->format('n'),true); ?></div>
					</div>
					<div class="booking-main-info-i">
			      <div class="avatar-w" style="background-image: url(<?php echo $booking->agent->get_avatar_url(); ?>);">
			        <div class="agent-info-tooltip"><?php echo $booking->agent->full_name; ?></div>
			      </div>
						<div class="booking-date-w">
							<div class="booking-service-name"><?php echo $booking->service->name; ?></div>
							<div class="booking-date-i">
								<div class="booking-date"><?php echo $booking->get_nice_start_date(true).', '; ?></div>
								<div class="booking-time"><?php echo $booking->get_nice_start_time(); ?>,</div>
								<div class="booking-time-left"><?php echo sprintf(__('in %s', 'latepoint'), $booking->time_left); ?></div>
								<?php if($total_locations > 1) echo '<div class="booking-location">'.$booking->location->name.'</div>'; ?>
								<div class="booking-attendees">
									<?php
									if($max_capacity > 1) {
										$total_attendies = $booking->total_attendies;
										echo '<div class="booked-count-label">'.sprintf(__('Booked %d of %d', 'latepoint'), $total_attendies, $max_capacity).'</div>';
										echo '<div class="booked-percentage">
											<div class="booked-bar" style="width: '.min(100, round($total_attendies / $max_capacity * 100)).'%;"></div>
										</div>';
									}else{
										echo '<div class="booking-attendee">';
										echo '<div class="avatar-w" style="background-image: url('.$booking->customer->get_avatar_url().');"></div>';
										echo '<div class="customer-name">'.$booking->customer->full_name.'</div>';
										echo '</div>';
									}
									?>
								</div>
							</div>
						</div>
					</div>
					<div class="booking-link">
						<i class="latepoint-icon latepoint-icon-arrow-right"></i>
					</div>
				</div>
			</div>
		<?php } ?>
	</div>
<?php }else{ ?>
	<div class="no-results-w">
	  <div class="icon-w"><i class="latepoint-icon latepoint-icon-book"></i></div>
	  <h2><?php _e('No Upcoming Appointments', 'latepoint'); ?></h2>
	  <a href="#" <?php echo OsBookingHelper::quick_booking_btn_html(); ?> class="latepoint-btn">
	    <i class="latepoint-icon latepoint-icon-plus-square"></i>
	    <span><?php _e('Create Appointment', 'latepoint'); ?></span>
	  </a>
	</div>
<?php } ?>