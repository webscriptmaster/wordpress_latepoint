<?php
/* @var $booking OsBookingModel */
/* @var $current_step string */
/* @var $restrictions array */
?>
<div class="latepoint-booking-params-w">
	<?php 
	$add_string_to_id = '_'.OsUtilHelper::random_text('hexdec', 8);

	echo '<div class="latepoint-restrictions-params">';
		echo OsFormHelper::hidden_field('restrictions[show_locations]', $restrictions['show_locations'], ['skip_id' => true]);
		echo OsFormHelper::hidden_field('restrictions[show_agents]', $restrictions['show_agents'], ['skip_id' => true]);
		echo OsFormHelper::hidden_field('restrictions[show_services]', $restrictions['show_services'], ['skip_id' => true]);
		echo OsFormHelper::hidden_field('restrictions[show_service_categories]', $restrictions['show_service_categories'], ['skip_id' => true]);
		echo OsFormHelper::hidden_field('restrictions[selected_service]', $restrictions['selected_service'], ['skip_id' => true]);
		echo OsFormHelper::hidden_field('restrictions[selected_service_category]', $restrictions['selected_service_category'], ['skip_id' => true]);
		echo OsFormHelper::hidden_field('restrictions[selected_duration]', $restrictions['selected_duration'], ['skip_id' => true]);
		echo OsFormHelper::hidden_field('restrictions[selected_total_attendies]', $restrictions['selected_total_attendies'], ['skip_id' => true]);
		echo OsFormHelper::hidden_field('restrictions[selected_location]', $restrictions['selected_location'], ['skip_id' => true]);
		echo OsFormHelper::hidden_field('restrictions[selected_agent]', $restrictions['selected_agent'], ['skip_id' => true]);
		echo OsFormHelper::hidden_field('restrictions[selected_start_date]', $restrictions['selected_start_date'], ['skip_id' => true]);
		echo OsFormHelper::hidden_field('restrictions[selected_start_time]', $restrictions['selected_start_time'], ['skip_id' => true]);
		echo OsFormHelper::hidden_field('restrictions[calendar_start_date]', $restrictions['calendar_start_date'], ['skip_id' => true]);
		echo OsFormHelper::hidden_field('restrictions[source_id]', $restrictions['source_id'], ['skip_id' => true]);
	echo '</div>';

	if(OsPaymentsHelper::get_default_payment_method()) echo OsFormHelper::hidden_field('booking[payment_method]', OsPaymentsHelper::get_default_payment_method(), [ 'class' => 'latepoint_payment_method', 'skip_id' => true]);
	echo OsFormHelper::hidden_field('booking[payment_token]', $booking->payment_token, [ 'class' => 'latepoint_payment_token', 'skip_id' => true]);
	echo OsFormHelper::hidden_field('booking[intent_key]', $booking->intent_key, [ 'class' => 'latepoint_booking_intent_key', 'skip_id' => true]);
	echo OsFormHelper::hidden_field('current_step', $current_step, ['class' => 'latepoint_current_step', 'skip_id' => true]);
	echo OsFormHelper::hidden_field('step_direction', 'next', ['class' => 'latepoint_step_direction', 'skip_id' => true]); 

	// if restrictions are set - create values for the restricted fields since their respected steps are being skipped
  if($restrictions['selected_agent']) echo OsFormHelper::hidden_field('booking[agent_id]', $restrictions['selected_agent'], [ 'class' => 'latepoint_agent_id', 'skip_id' => true]);
  if($restrictions['selected_location']) echo OsFormHelper::hidden_field('booking[location_id]', $restrictions['selected_location'], [ 'class' => 'latepoint_location_id', 'skip_id' => true]);
	// TODO show service related fields only if the whole step is skipped, otherwise they will also be duplicated on the service step itself
  if($restrictions['selected_service']) echo OsFormHelper::hidden_field('booking[service_id]', $restrictions['selected_service'], [ 'class' => 'latepoint_service_id', 'skip_id' => true]);
  if($restrictions['selected_duration']) echo OsFormHelper::hidden_field('booking[duration]', $restrictions['selected_duration'], [ 'class' => 'latepoint_duration', 'skip_id' => true]);
  if($restrictions['selected_total_attendies']) echo OsFormHelper::hidden_field('booking[total_attendies]', $restrictions['selected_total_attendies'], [ 'class' => 'latepoint_total_attendies', 'skip_id' => true]);

  if($restrictions['selected_start_date']) echo OsFormHelper::hidden_field('booking[start_date]', $restrictions['selected_start_date'], [ 'class' => 'latepoint_start_date', 'skip_id' => true]);
  if($restrictions['selected_start_time']) echo OsFormHelper::hidden_field('booking[start_time]', $restrictions['selected_start_time'], [ 'class' => 'latepoint_start_time', 'skip_id' => true]);

	do_action('latepoint_booking_params', $booking, $restrictions, $current_step, $add_string_to_id);
	?>
</div>