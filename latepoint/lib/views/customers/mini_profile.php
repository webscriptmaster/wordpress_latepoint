<?php
/** @var $customer OsCustomerModel */
/** @var $upcoming_booking OsBookingModel */
?>
<div class="os-mini-customer-profile-w">
	<a href="#" class="os-floating-popup-close"><i class="latepoint-icon latepoint-icon-x"></i></a>
	<div class="os-mc-info-w">
		<div class="os-mc-avatar" style="background-image: url(<?php echo $customer->get_avatar_url(); ?>)"></div>
		<div class="os-mc-info">
			<div class="mc-name"><?php echo $customer->full_name; ?><a target="_blank" href="<?php echo OsRouterHelper::build_link(['customers', 'edit_form'], ['id' => $customer->id]); ?>"><i class="latepoint-icon latepoint-icon-external-link"></i></a></div>
			<?php if (!empty($customer->email)) { ?><div class="mc-info-list-item"><span><?php _e('email:', 'latepoint'); ?></span><strong><?php echo $customer->email; ?></strong></div><?php } ?>
			<?php if (!empty($customer->phone)) { ?><div class="mc-info-list-item"><span><?php _e('phone:', 'latepoint'); ?></span><strong><?php echo $customer->phone; ?></strong></div><?php } ?>
		</div>
	</div>
	<div class="os-mc-sub-info">
		<div class="os-mc-chart">
			<?php if(isset($pie_chart_data) && !empty($pie_chart_data['values'])){ ?>
				<div class="os-mc-heading"><?php _e('Total', 'latepoint'); ?></div>
				<div class="os-mc-chart-i">
					<div class="os-mc-totals"><?php echo $customer->get_total_bookings_count(true); ?></div>
					<canvas class="os-customer-donut-chart" width="90" height="90"  
						data-chart-labels="<?php echo implode(',', $pie_chart_data['labels']); ?>" 
						data-chart-colors="<?php echo implode(',', $pie_chart_data['colors']); ?>" 
						data-chart-values="<?php echo implode(',', $pie_chart_data['values']); ?>"></canvas>
					</div>
			<?php } ?>
		</div>
		<div class="os-mc-upcoming-appointments-w">
			<div class="os-mc-heading"><?php _e('Next Appointment', 'latepoint'); ?></div>
			<div class="os-mc-upcoming-appointments">
				<?php if($upcoming_booking){ ?>
					<div class="os-upcoming-appointment">
						<div class="appointment-color-elem" style="background-color: <?php echo $upcoming_booking->service->bg_color; ?>"></div>
						<div class="appointment-service-name"><?php echo $upcoming_booking->service->name; ?></div>
						<div class="appointment-date-w">
							<div class="appointment-date-i">
								<div class="appointment-date"><?php echo $upcoming_booking->nice_start_date; ?></div>
								<div class="appointment-time"><?php echo implode('-', array($upcoming_booking->nice_start_time, $upcoming_booking->nice_end_time)); ?></div>
							</div>
				      <div class="avatar-w" style="background-image: url(<?php echo $upcoming_booking->agent->get_avatar_url(); ?>);">
				      	<div class="agent-info-tooltip"><?php echo $upcoming_booking->agent->full_name; ?></div>
				      </div>
						</div>
					</div>
					<?php
				}else{
					echo '<div class="os-nothing">'.__('No Upcoming Appointments', 'latepoint').'</div>';
				} ?>
			</div>
		</div>
	</div>
</div>