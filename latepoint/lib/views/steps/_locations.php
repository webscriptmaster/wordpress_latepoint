<?php $preselected_location = (isset($restrictions['selected_location']) && !empty($restrictions['selected_location'])) ? new OslocationModel($restrictions['selected_location']) : false; ?>
<div class="step-locations-w latepoint-step-content" data-step-name="locations" data-clear-action="clear_step_locations">
  <?php 
  if(OsSettingsHelper::is_on('steps_show_location_categories') && !$preselected_location){
    // Generate categorized locations list
    OsLocationHelper::generate_locations_and_categories_list(false, $show_locations_arr);
  }else{
    OsLocationHelper::generate_locations_list($locations, $preselected_location);
  }
  echo OsFormHelper::hidden_field('booking[location_id]', $booking->location_id, [ 'class' => 'latepoint_location_id', 'skip_id' => true]); ?>
</div>


