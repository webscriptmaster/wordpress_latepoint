<?php
$preselected_service = (isset($restrictions['selected_service']) && !empty($restrictions['selected_service'])) ? new OsServiceModel($restrictions['selected_service']) : false;
$sub_step_class = '';
if($preselected_service){
  if($preselected_service->should_show_capacity_selector() && empty($restrictions['selected_total_attendies'])) $sub_step_class = 'selecting-total-attendies';
  if((count($preselected_service->get_all_durations_arr()) > 1) && empty($restrictions['selected_duration'])) $sub_step_class = 'selecting-service-duration';
}
?>
<div class="step-services-w latepoint-step-content <?php echo $sub_step_class; ?>" data-step-name="services"  data-clear-action="clear_step_services">
  <div class="latepoint-step-content-text-centered">
    <h4><?php _e('Select Service Duration', 'latepoint'); ?></h4>
    <div><?php _e('You need to select service duration, the price of your service will depend on duration.', 'latepoint'); ?></div>
  </div>
  <div class="select-total-attendies-w style-centered">
    <div class="select-total-attendies-label">
      <h4><?php _e('How Many People?', 'latepoint'); ?></h4>
      <div class="sta-sub-label"><?php _e('Maximum capacity is', 'latepoint'); ?> <span><?php echo ($preselected_service) ? $preselected_service->capacity_max : 1 ?></span></div>
    </div>
    <div class="total-attendies-selector-w" data-min-capacity="<?php echo ($preselected_service) ? $preselected_service->capacity_min : 1 ?>" data-max-capacity="<?php echo ($preselected_service) ? $preselected_service->capacity_max : 1 ?>">
      <div class="total-attendies-selector total-attendies-selector-minus"><i class="latepoint-icon latepoint-icon-minus"></i></div>
      <input type="text" data-summary-singular="<?php _e('Person', 'latepoint'); ?>" data-summary-plural="<?php _e('People', 'latepoint'); ?>" name="booking[total_attendies]" class="total-attendies-selector-input latepoint_total_attendies" value="<?php echo ($preselected_service) ? max($booking->total_attendies, $preselected_service->capacity_min) : $booking->total_attendies; ?>" placeholder="<?php _e('Qty', 'latepoint'); ?>">
      <div class="total-attendies-selector total-attendies-selector-plus"><i class="latepoint-icon latepoint-icon-plus"></i></div>
    </div>
  </div>
  <?php 
  if(OsSettingsHelper::steps_show_service_categories() && !$preselected_service){
    // Generate categorized services list
    OsBookingHelper::generate_services_and_categories_list(false, ['show_service_categories_arr' => $show_service_categories_arr,
																																					    'show_services_arr' => $show_services_arr,
																																					    'preselected_category' => $preselected_category,
																																					    'preselected_duration' => $preselected_duration,
																																					    'preselected_total_attendies' => $preselected_total_attendies,
    ]);
  }else{
    OsBookingHelper::generate_services_list($services, $preselected_service, $preselected_duration, $preselected_total_attendies);
  } ?>
  <?php 
    echo OsFormHelper::hidden_field('booking[service_id]', $booking->service_id, [ 'class' => 'latepoint_service_id', 'skip_id' => true]);
    echo OsFormHelper::hidden_field('booking[duration]', $booking->duration, [ 'class' => 'latepoint_duration', 'skip_id' => true]);
  ?>
</div>