<?php 

class OsShortcodesHelper {

	// [latepoint_resources]
	public static function shortcode_latepoint_resources($atts){
      $atts = shortcode_atts( array(
				'button_caption' => __('Book Now', 'latepoint'),
        'items' => 'services',
				'item_ids' => '',
				'group_ids' => '',
	      'columns' => 4,
	      'limit' => false,
	      'button_border_radius' => false,
				'button_bg_color' => false,
				'button_text_color' => false,
				'button_font_size' => false,
        'show_locations' => false,
        'show_agents' => false,
        'show_services' => false,
        'show_service_categories' => false,
        'selected_location' => false,
        'selected_agent' => false,
        'selected_service' => false,
        'selected_duration' => false,
        'selected_total_attendees' => false,
        'selected_service_category' => false,
        'calendar_start_date' => false,
        'selected_start_date' => false,
        'selected_start_time' => false,
        'hide_side_panel' => false,
        'hide_summary' => false,
        'source_id' => false,
      ), $atts );


      // Data attributes setup
      $data_atts = '';
      if(($atts['items'] != 'locations') && $atts['show_locations']) $data_atts.= 'data-show-locations="'.$atts['show_locations'].'" ';
      if(($atts['items'] != 'agents') && $atts['show_agents']) $data_atts.= 'data-show-agents="'.$atts['show_agents'].'" ';
      if(($atts['items'] != 'services') && $atts['show_services']) $data_atts.= 'data-show-services="'.$atts['show_services'].'" ';
      if(($atts['items'] != 'services') && $atts['show_service_categories']) $data_atts.= 'data-show-service-categories="'.$atts['show_service_categories'].'" ';
      if(($atts['items'] != 'locations') && $atts['selected_location']) $data_atts.= 'data-selected-location="'.$atts['selected_location'].'" ';
      if(($atts['items'] != 'agents') && $atts['selected_agent']) $data_atts.= 'data-selected-agent="'.$atts['selected_agent'].'" ';
      if(($atts['items'] != 'services') && $atts['selected_service']) $data_atts.= 'data-selected-service="'.$atts['selected_service'].'" ';
      if($atts['selected_duration']) $data_atts.= 'data-selected-duration="'.$atts['selected_duration'].'" ';
      if($atts['selected_total_attendees']) $data_atts.= 'data-selected-total-attendees="'.$atts['selected_total_attendees'].'" ';
      if(($atts['items'] != 'services') && $atts['selected_service_category']) $data_atts.= 'data-selected-service-category="'.$atts['selected_service_category'].'" ';
      if($atts['calendar_start_date']) $data_atts.= 'data-calendar-start-date="'.$atts['calendar_start_date'].'" ';
      if($atts['selected_start_date']) $data_atts.= 'data-selected-start-date="'.$atts['selected_start_date'].'" ';
      if($atts['selected_start_time']) $data_atts.= 'data-selected-start-time="'.$atts['selected_start_time'].'" ';
      if($atts['hide_side_panel'] == 'yes') $data_atts.= 'data-hide-side-panel="yes" ';
      if($atts['hide_summary'] == 'yes') $data_atts.= 'data-hide-summary="yes" ';
      if($atts['source_id']) $data_atts.= 'data-source-id="'.$atts['source_id'].'" ';

			$button_style = '';
      if($atts['button_bg_color']) $button_style.= 'background-color: '.$atts['button_bg_color'].'!important;';
      if($atts['button_text_color']) $button_style.= 'color: '.$atts['button_text_color'].'!important;';
      if($atts['button_font_size']) $button_style.= 'font-size: '.$atts['button_font_size'].'!important;';
      if($atts['button_border_radius']) $button_style.= 'border-radius: '.$atts['button_border_radius'].'!important;';

      if($button_style != '') $button_style = 'style="'.$button_style.'"';

			$output = '';
			$output.= '<div class="latepoint-resources-items-w resources-columns-'.$atts['columns'].'">';

			if($atts['item_ids']){
				$ids = OsUtilHelper::explode_and_trim($atts['item_ids']);
				$clean_item_ids = OsUtilHelper::clean_numeric_ids($ids);
			}else{
				$clean_item_ids = [];
			}
			if($atts['group_ids']){
				$ids = OsUtilHelper::explode_and_trim($atts['group_ids']);
				$clean_group_ids = OsUtilHelper::clean_numeric_ids($ids);
			}else{
				$clean_group_ids = [];
			}
			switch($atts['items']){
				case 'services':
					$services = new OsServiceModel();
					if($atts['limit'] && is_numeric($atts['limit'])) $services->set_limit($atts['limit']);
					if($clean_item_ids) $services->where(['id' => $clean_item_ids]);
					if($clean_group_ids) $services->where(['category_id' => $clean_group_ids]);
					$services = $services->should_be_active()->order_by('order_number asc')->get_results_as_models();
					foreach($services as $service){
						$output.= '<div class="resource-item">';
							$output.= !empty($service->description_image_id) ? '<div class="ri-media" style="background-image: url('.$service->get_description_image_url().')"></div>' : '';
							$output.= '<div class="ri-name"><h3>'.$service->name.'</h3></div>';

              if($service->price_min > 0){
								$service_price_formatted = ($service->price_min != $service->price_max) ? __('Starts at', 'latepoint').' '.$service->price_min_formatted : $service->price_min_formatted;
              }else{
								$service_price_formatted = '';
              }
							$output.= !empty($service_price_formatted) ? '<div class="ri-price">'.$service_price_formatted.'</div>' : '';
							$output.= !empty($service->short_description) ? '<div class="ri-description">'.$service->short_description.'</div>' : '';
							$output.= '<div class="ri-buttons"><a href="#"  '.$data_atts.' '.$button_style.' class="latepoint-btn latepoint-btn-primary os_trigger_booking" data-selected-service="'.$service->id.'">'.$atts['button_caption'].'</a></div>';
						$output.= '</div>';
					}
					break;
				case 'agents':
					$agents = new OsAgentModel();
					if($atts['limit'] && is_numeric($atts['limit'])) $agents->set_limit($atts['limit']);
					if($atts['item_ids']){
						$ids = OsUtilHelper::explode_and_trim($atts['item_ids']);
						$ids = OsUtilHelper::clean_numeric_ids($ids);
						if($ids) $agents->where(['id' => $ids]);
					}
					if($clean_item_ids) $agents->where(['id' => $clean_item_ids]);
					$agents = $agents->should_be_active()->get_results_as_models();
					foreach($agents as $agent){
						$output.= '<div class="resource-item ri-centered">';
							$output.= !empty($agent->avatar_image_id) ? '<div class="ri-avatar" style="background-image: url('.$agent->get_avatar_url().')"></div>' : '';
							$output.= '<div class="ri-name"><h3>'.$agent->full_name.'</h3></div>';
							$output.= !empty($agent->title) ? '<div class="ri-title">'.$agent->title.'</div>' : '';
							$output.= !empty($agent->short_description) ? '<div class="ri-description">'.$agent->short_description.'</div>' : '';
							$output.= '<div class="ri-buttons"><a href="#"  '.$data_atts.' '.$button_style.' class="latepoint-btn latepoint-btn-primary os_trigger_booking latepoint-btn-block" data-selected-agent="'.$agent->id.'">'.$atts['button_caption'].'</a></div>';
						$output.= '</div>';
					}
					break;
				case 'locations':
					$locations = new OsLocationModel();
					if($atts['limit'] && is_numeric($atts['limit'])) $locations->set_limit($atts['limit']);
					if($clean_item_ids) $locations->where(['id' => $clean_item_ids]);
					if($clean_group_ids) $locations->where(['category_id' => $clean_group_ids]);
					$locations = $locations->should_be_active()->order_by('order_number asc')->get_results_as_models();
					foreach($locations as $location){
						$output.= '<div class="resource-item">';
							$output.= !empty($location->full_address) ? '<div class="ri-map">'.$location->get_google_maps_iframe(200).'</div>' : '';
							$output.= '<div class="ri-name"><h3>'.$location->name.'</h3></div>';
							$output.= !empty($location->full_address) ? '<div class="ri-description">'.$location->full_address.'<a href="'.$location->get_google_maps_link().'" target="_blank" class="ri-external-link"><i class="latepoint-icon latepoint-icon-external-link"></i></a></div>' : '';
							$output.= '<div class="ri-buttons"><a href="#"  '.$data_atts.' '.$button_style.' class="latepoint-btn latepoint-btn-primary os_trigger_booking" data-selected-location="'.$location->id.'">'.$atts['button_caption'].'</a></div>';
						$output.= '</div>';
					}
					break;
			}
			$output.= '</div>';
		return $output;
	}

  // [latepoint_book_form]
  public static function shortcode_latepoint_book_form( $atts, $content = "" ) {
      $atts = shortcode_atts( array(
          'show_locations' => false,
          'show_agents' => false,
          'show_services' => false,
          'show_service_categories' => false,
          'selected_location' => false,
          'selected_agent' => false,
          'selected_service' => false,
          'selected_duration' => false,
          'selected_total_attendees' => false,
          'selected_service_category' => false,
          'calendar_start_date' => false,
          'selected_start_date' => false,
          'selected_start_time' => false,
          'hide_side_panel' => false,
          'hide_summary' => false,
	        'source_id' => false
      ), $atts );
      $nonce = wp_create_nonce("latepoint_nonce");

      $data_atts = '';


      // Data attributes setup
      $restrictions = [];
      if($atts['show_locations']) $restrictions['show_locations'] = $atts['show_locations'];
      if($atts['show_agents']) $restrictions['show_agents'] = $atts['show_agents'];
      if($atts['show_services']) $restrictions['show_services'] = $atts['show_services'];
      if($atts['show_service_categories']) $restrictions['show_service_categories'] = $atts['show_service_categories'];
      if($atts['selected_location']) $restrictions['selected_location'] = $atts['selected_location'];
      if($atts['selected_agent']) $restrictions['selected_agent'] = $atts['selected_agent'];
      if($atts['selected_service']) $restrictions['selected_service'] = $atts['selected_service'];
      if($atts['selected_duration']) $restrictions['selected_duration'] = $atts['selected_duration'];
      if($atts['selected_total_attendees']) $restrictions['selected_total_attendies'] = $atts['selected_total_attendees'];
      if($atts['selected_service_category']) $restrictions['selected_service_category'] = $atts['selected_service_category'];
      if($atts['calendar_start_date']) $restrictions['calendar_start_date'] = $atts['calendar_start_date'];
      if($atts['selected_start_date']) $restrictions['selected_start_date'] = $atts['selected_start_date'];
      if($atts['selected_start_time']) $restrictions['selected_start_time'] = $atts['selected_start_time'];
      if($atts['source_id']) $restrictions['source_id'] = $atts['source_id'];


      $steps_controller = new OsStepsController();
      $summary_class = ($atts['hide_summary'] == 'yes') ? '' : 'latepoint-with-summary';
      $side_panel_class = ($atts['hide_side_panel'] == 'yes') ? 'latepoint-hide-side-panel' : '';
      $output = '<div class="latepoint-w latepoint-shortcode-booking-form '.$summary_class.' '.$side_panel_class.'">';
      $output.= $steps_controller->start($restrictions, false);
      $output.= '</div>';
      return $output;
  }


  // [latepoint_book_button]
  public static function shortcode_latepoint_book_button( $atts, $content = "" ) {
      $atts = shortcode_atts( array(
          'caption' => __('Book Appointment', 'latepoint'),
          'bg_color' => false,
          'text_color' => false,
          'font_size' => false,
          'border' => false,
          'border_radius' => false,
          'margin' => false,
          'padding' => false,
          'css' => false,
          'show_locations' => false,
          'show_agents' => false,
          'show_services' => false,
          'show_service_categories' => false,
          'selected_location' => false,
          'selected_agent' => false,
          'selected_service' => false,
          'selected_duration' => false,
          'selected_total_attendees' => false,
          'selected_service_category' => false,
          'calendar_start_date' => false,
          'selected_start_date' => false,
          'selected_start_time' => false,
          'hide_side_panel' => false,
          'hide_summary' => false,
          'source_id' => false,
	      'align' => false
      ), $atts );

      $nonce = wp_create_nonce("latepoint_nonce");

      $style = '';
      $data_atts = '';

      // Style setup
      if($atts['bg_color']) $style.= 'background-color: '.$atts['bg_color'].';';
      if($atts['text_color']) $style.= 'color: '.$atts['text_color'].';';
      if($atts['font_size']) $style.= 'font-size: '.$atts['font_size'].';';
      if($atts['border']) $style.= 'border: '.$atts['border'].';';
      if($atts['border_radius']) $style.= 'border-radius: '.$atts['border_radius'].';';
      if($atts['margin']) $style.= 'margin: '.$atts['margin'].';';
      if($atts['padding']) $style.= 'padding: '.$atts['padding'].';';


      // Data attributes setup
      if($atts['show_locations']) $data_atts.= 'data-show-locations="'.$atts['show_locations'].'" ';
      if($atts['show_agents']) $data_atts.= 'data-show-agents="'.$atts['show_agents'].'" ';
      if($atts['show_services']) $data_atts.= 'data-show-services="'.$atts['show_services'].'" ';
      if($atts['show_service_categories']) $data_atts.= 'data-show-service-categories="'.$atts['show_service_categories'].'" ';
      if($atts['selected_location']) $data_atts.= 'data-selected-location="'.$atts['selected_location'].'" ';
      if($atts['selected_agent']) $data_atts.= 'data-selected-agent="'.$atts['selected_agent'].'" ';
      if($atts['selected_service']) $data_atts.= 'data-selected-service="'.$atts['selected_service'].'" ';
      if($atts['selected_duration']) $data_atts.= 'data-selected-duration="'.$atts['selected_duration'].'" ';
      if($atts['selected_total_attendees']) $data_atts.= 'data-selected-total-attendees="'.$atts['selected_total_attendees'].'" ';
      if($atts['selected_service_category']) $data_atts.= 'data-selected-service-category="'.$atts['selected_service_category'].'" ';
      if($atts['calendar_start_date']) $data_atts.= 'data-calendar-start-date="'.$atts['calendar_start_date'].'" ';
      if($atts['selected_start_date']) $data_atts.= 'data-selected-start-date="'.$atts['selected_start_date'].'" ';
      if($atts['selected_start_time']) $data_atts.= 'data-selected-start-time="'.$atts['selected_start_time'].'" ';
      if($atts['hide_side_panel'] == 'yes') $data_atts.= 'data-hide-side-panel="yes" ';
      if($atts['hide_summary'] == 'yes') $data_atts.= 'data-hide-summary="yes" ';
      if($atts['source_id']) $data_atts.= 'data-source-id="'.$atts['source_id'].'" ';



      if(($style == '') && $atts['css']) $style = $atts['css'];

      if($style != '') $style = 'style="'.$style.'"';


			if($atts['align']){
				$before_html = '<div class="latepoint-book-button-wrapper latepoint-book-button-align-'.$atts['align'].'">';
				$after_html = '</div>';
			}else{
				$before_html = '';
				$after_html = '';
			}
      $output = $before_html.'<div class="latepoint-book-button os_trigger_booking" '.$data_atts.' '.$style.' data-nonce="'.$nonce.'">'.esc_attr($atts['caption']).'</div>'.$after_html;
      
      return $output;
  }

  // [latepoint_customer_dashboard]
  public static function shortcode_latepoint_customer_dashboard($atts){
    $atts = shortcode_atts( array(
        'caption' => __('Book Appointment', 'latepoint')
    ), $atts );

    $customerCabinetController = new OsCustomerCabinetController();
    $output = $customerCabinetController->dashboard();
    return $output;
  }

  // [latepoint_customer_login]
  public static function shortcode_latepoint_customer_login($atts){
    $atts = shortcode_atts( array(
        'caption' => __('Book Appointment', 'latepoint')
    ), $atts );

    $customerCabinetController = new OsCustomerCabinetController();
    $output = $customerCabinetController->login();
    return $output;
  }

}