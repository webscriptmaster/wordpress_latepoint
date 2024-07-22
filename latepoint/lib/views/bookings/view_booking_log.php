<?php
/*
 * Copyright (c) 2022 LatePoint LLC. All rights reserved.
 */
?>

<?php
/* @var $activities OsActivityModel[] */
/* @var $booking OsBookingModel */
?>
<div class="booking-activity-log-panel-w side-sub-panel">
	<div class="os-form-header">
		<h2><?php _e('Activity Log', 'latepoint'); ?></h2>
		<a href="#" class="booking-activity-log-panel-close"><i class="latepoint-icon latepoint-icon-x"></i></a>
	</div>
	<div class="booking-activity-log-panel-i">
		<div class="booking-activities-list">
			<div class="quick-booking-info">
				<?php if($booking->ip_address) echo '<span>'.__('IP Address: ', 'latepoint').'</span><strong>'.$booking->ip_address.'</strong>'; ?>
				<?php if($booking->source_id) echo '<span>'.__('Source ID: ', 'latepoint').'</span><strong>'.$booking->source_id.'</strong>'; ?>
				<?php echo '<a href="'.$booking->source_url.'" target="_blank"><i class="latepoint-icon latepoint-icon-external-link"></i>'.__('Booking Page', 'latepoint').'</a>'; ?>
			</div>
			<?php
			foreach ($activities as $activity) {
				echo '<div class="booking-activity-row">';
					echo '<div class="booking-activity-name">' . $activity->name . '</div>';
					echo '<div class="spacer"></div>';
					echo '<div class="booking-activity-date">' . $activity->nice_created_at . '</div>';
					echo $activity->get_link_to_object('<i class="latepoint-icon latepoint-icon-file-text"></i>');
				echo '</div>';
			}
			?>
		</div>
	</div>
</div>
