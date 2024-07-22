<?php 

class OsAgentHelper {

	public static function generate_bio(OsAgentModel $agent){
		?>
		<div class="os-item-details-popup" id="osItemDetailsPopupAgent_<?php echo $agent->id; ?>">
        <a href="#" class="os-item-details-popup-close"><span><?php _e('Close Details', 'latepoint'); ?></span><i class="latepoint-icon latepoint-icon-common-01"></i></a>
	      <div class="os-item-details-popup-inner">
        <div class="item-details-popup-head" style="background-image: url(<?php echo $agent->bio_image_url; ?>)">
          <h3><?php echo $agent->name_for_front; ?></h3>
          <div class="item-details-popup-title"><?php echo $agent->title; ?></div>
        </div>
        <div class="item-details-popup-content">
          <img class="bio-curve" src="<?php echo LATEPOINT_IMAGES_URL.'white-curve.png'; ?>" alt="">
          <div class="item-details-popup-features">
            <?php foreach($agent->features_arr as $feature){ ?>
              <div class="item-details-popup-feature">
                <div class="item-details-popup-feature-value"><?php echo $feature['value']; ?></div>
                <div class="item-details-popup-feature-label"><?php echo $feature['label']; ?></div>
              </div>
            <?php } ?>
          </div>
          <div class="item-details-popup-content-i">
            <?php echo $agent->bio; ?>
          </div>
        </div>
        </div>
      </div>
		<?php
	}

  public static function generate_day_schedule_info($filter){
    $today_date = new OsWpDateTime('today');
    $target_date = new OsWpDateTime($filter->date_from); ?>
    <div class="agent-schedule-info">
      <div class="agent-today-info">
        <?php echo ($target_date->format('Y-m-d') == $today_date->format('Y-m-d')) ? __('Today', 'latepoint') : $target_date->format(OsSettingsHelper::get_readable_date_format()); ?>
        <?php

        $booking_request = new \LatePoint\Misc\BookingRequest();
				$booking_request->agent_id = $filter->agent_id;
				$booking_request->start_date = $target_date->format('Y-m-d');
        $resources = OsResourceHelper::get_resources_grouped_by_day($booking_request, $target_date, $target_date);

				$day_work_periods = [];

				$periods = [];
				foreach($resources[$target_date->format('Y-m-d')] as $resource){
					if(!empty($resource->work_time_periods)){
						foreach($resource->work_time_periods as $work_time_period){
							if($work_time_period->start_time == $work_time_period->end_time) continue;
							$periods[] = $work_time_period->start_time.':'.$work_time_period->end_time;
						}
					}
				}
				$periods = array_unique($periods);
				foreach($periods as $work_time_period){
					$period = explode(':', $work_time_period);
					$work_time_period = new \LatePoint\Misc\WorkPeriod();
					$work_time_period->start_time = $period[0];
					$work_time_period->end_time = $period[1];
					$day_work_periods[] = $work_time_period;
				}

        $is_working_today = !empty($day_work_periods);
         ?>
        <span class="today-status <?php echo ($is_working_today) ? 'is-on-duty' : 'is-off-duty'; ?>"><?php echo ($is_working_today) ? __('On Duty', 'latepoint') : __('Off Duty', 'latepoint'); ?></span>
        <div class="today-schedule">
          <?php if($is_working_today){ ?>
            <?php foreach($day_work_periods as $period){
              echo '<span>' . OsTimeHelper::minutes_to_hours_and_minutes($period->start_time).' - '.OsTimeHelper::minutes_to_hours_and_minutes($period->end_time) . '</span>';
            } ?>
          <?php }else{
            _e('Not Available', 'latepoint');
          } ?>
        </div>
      </div>
      <div class="today-bookings">
        <?php _e('Bookings', 'latepoint'); ?>
        <div class="today-bookings-count"><?php echo OsBookingHelper::count_bookings($filter); ?></div>
      </div>
    </div>
    <?php
  }

  public static function get_full_name($agent){
  	return join(' ', array($agent->first_name, $agent->last_name));
  }



  public static function get_agent_ids_for_service_and_location($service_id = false, $location_id = false): array{
    $all_agent_ids = OsConnectorHelper::get_connected_object_ids('agent_id', ['service_id' => $service_id, 'location_id' => $location_id]);
    $agents = new OsAgentModel();
    $active_agent_ids = $agents->select('id')->should_be_active()->get_results(ARRAY_A);
    if($active_agent_ids){
      $active_agent_ids = array_column($active_agent_ids, 'id');
      $all_agent_ids = array_intersect($active_agent_ids, $all_agent_ids);
    }else{
      $all_agent_ids = [];
    }
    return $all_agent_ids;
  }


	/**
	 * @param bool $filter_allowed_records
	 * @return array
	 */
  public static function get_agents_list(bool $filter_allowed_records = false): array{
    $agents = new OsAgentModel();
		if($filter_allowed_records) $agents->filter_allowed_records();
    $agents = $agents->get_results_as_models();
    $agents_list = [];
    if($agents){
      foreach($agents as $agent){
        $agents_list[] = ['value' => $agent->id, 'label' => $agent->full_name];
      }
    }
    return $agents_list;
  }

  public static function get_avatar_url($agent){
    $default_avatar = LATEPOINT_DEFAULT_AVATAR_URL;
    return OsImageHelper::get_image_url_by_id($agent->avatar_image_id, 'thumbnail', $default_avatar);
  }

  public static function get_bio_image_url($agent){
    $default_bio_image = LATEPOINT_DEFAULT_AVATAR_URL;
    return OsImageHelper::get_image_url_by_id($agent->bio_image_id, 'large', $default_bio_image);
  }


  public static function count_agents(){
    $agents = new OsAgentModel();
    return $agents->count();
  }

}