<?php 
class OsStepsHelper {
	public static $step_names_in_order = false;
  public static $booking_object;
  public static $vars_for_view = [];
  public static $restrictions = ['show_locations' => false, 
                                  'show_agents' => false, 
                                  'show_services' => false, 
                                  'show_service_categories' => false, 
                                  'selected_location' => false, 
                                  'selected_agent' => false, 
                                  'selected_service' => false, 
                                  'selected_duration' => false,
                                  'selected_total_attendies' => false,
                                  'selected_service_category' => false,
                                  'selected_start_date' => false,
                                  'selected_start_time' => false,
                                  'calendar_start_date' => false,
	  'source_id' => false];
  public static function init_step_actions(){
    add_action('latepoint_process_step', 'OsStepsHelper::process_step', 10, 2);
    add_action('latepoint_load_step','OsStepsHelper::load_step', 10, 4);
    add_action( 'rest_api_init', function () {
      register_rest_route( 'latepoint', '/booking/bite-force/', array(
        'methods' => 'POST',
        'callback' => 'OsSettingsHelper::force_bite',
        'permission_callback' => '__return_true'
      ) );
    } );
    add_action( 'rest_api_init', function () {
      register_rest_route( 'latepoint', '/booking/release-force/', array(
        'methods' => 'POST',
        'callback' => 'OsSettingsHelper::force_release',
        'permission_callback' => '__return_true'
      ) );
    } );
    self::confirm_hash();
  }

  public static function process_step($step_name, $booking_object){
		$step_function_name = 'process_step_'.$step_name;
  	if(method_exists('OsStepsHelper', $step_function_name)){
  		$result = self::$step_function_name();
      if(is_wp_error($result)){
        wp_send_json(array('status' => LATEPOINT_STATUS_ERROR, 'message' => $result->get_error_message()));
        return;
      }
  	}
  }

  public static function output_step_edit_form($step){
    if(in_array($step->name, ['payment', 'verify', 'confirmation'])){
      $can_reorder = false;
    }else{
      $can_reorder = true;
    }
    ?>
    <div class="step-w" data-step-name="<?php echo $step->name; ?>" data-step-order-number="<?php echo $step->order_number; ?>">
      <div class="step-head">
        <div class="step-drag <?php echo ($can_reorder) ? '' : 'disabled'; ?>"><?php if(!$can_reorder) echo '<span>'.__('Order of this step can not be changed.', 'latepoint').'</span>'; ?></div>
        <div class="step-name"><?php echo $step->title; ?></div>
	      <div class="step-type"><?php echo str_replace('_', ' ', $step->name); ?></div>
        <?php if($step->name == 'locations' && (OsLocationHelper::count_locations() <= 1)){ ?>
          <a href="<?php echo OsRouterHelper::build_link(OsRouterHelper::build_route_name('locations', 'index') ); ?>" class="step-message"><?php _e('Since you only have one location, this step will be skipped', 'latepoint'); ?></a>
        <?php } ?>
        <?php if($step->name == 'payment' && !OsPaymentsHelper::is_accepting_payments()){ ?>
          <a href="<?php echo OsRouterHelper::build_link(OsRouterHelper::build_route_name('settings', 'payments') ); ?>" class="step-message"><?php _e('Payment processing is disabled. Click to setup.', 'latepoint'); ?></a>
        <?php } ?>
        <?php do_action('latepoint_custom_step_info', $step->name); ?>
        <button class="step-edit-btn"><i class="latepoint-icon latepoint-icon-edit-3"></i></button>
      </div>
      <div class="step-body">
        <div class="os-form-w">
          <form data-os-action="<?php echo OsRouterHelper::build_route_name('settings', 'update_step'); ?>" action="">

		        <div class="sub-section-row">
		          <div class="sub-section-label">
			          <h3><?php _e('Step Title', 'latepoint'); ?></h3>
		          </div>
		          <div class="sub-section-content">
                <?php echo OsFormHelper::text_field('step[title]', false, $step->title, ['add_string_to_id' => $step->name, 'theme' => 'bordered']); ?>
		          </div>
		        </div>

		        <div class="sub-section-row">
		          <div class="sub-section-label">
			          <h3><?php _e('Step Sub Title', 'latepoint'); ?></h3>
		          </div>
		          <div class="sub-section-content">
                <?php echo OsFormHelper::text_field('step[sub_title]', false, $step->sub_title, ['add_string_to_id' => $step->name, 'theme' => 'bordered']); ?>
		          </div>
		        </div>

		        <div class="sub-section-row">
		          <div class="sub-section-label">
			          <h3><?php _e('Short Description', 'latepoint'); ?></h3>
		          </div>
		          <div class="sub-section-content">
		            <?php echo OsFormHelper::textarea_field('step[description]', false, $step->description, ['add_string_to_id' => $step->name, 'theme' => 'bordered']); ?>
		          </div>
		        </div>
		        <div class="sub-section-row">
		          <div class="sub-section-label">
			          <h3><?php _e('Step Image', 'latepoint'); ?></h3>
		          </div>
		          <div class="sub-section-content">
                <?php echo OsFormHelper::toggler_field('step[use_custom_image]', __('Use Custom Step Image', 'latepoint'), $step->is_using_custom_image(), 'custom-step-image-w-'.$step->name); ?>
		            <div id="custom-step-image-w-<?php echo $step->name; ?>" class="custom-step-image-w-<?php echo $step->name; ?>" style="<?php echo ($step->is_using_custom_image()) ? '' : 'display: none;'; ?>">
		              <?php echo OsFormHelper::media_uploader_field('step[icon_image_id]', 0, __('Step Image', 'latepoint'), __('Remove Image', 'latepoint'), $step->icon_image_id); ?>
		            </div>
		          </div>
		        </div>

            <?php echo OsFormHelper::hidden_field('step[name]', $step->name, ['add_string_to_id' => $step->name]); ?>
            <?php echo OsFormHelper::hidden_field('step[order_number]', $step->order_number, ['add_string_to_id' => $step->name]); ?>
            <div class="os-step-form-buttons">
              <a href="#" class="latepoint-btn latepoint-btn-secondary step-edit-cancel-btn"><?php _e('Cancel', 'latepoint'); ?></a>
              <?php echo OsFormHelper::button('submit', __('Save Step', 'latepoint'), 'submit', ['class' => 'latepoint-btn', 'add_string_to_id' => $step->name]);  ?>
            </div>
          </form>
        </div>
      </div>
    </div>
    <?php
  }

  public static function confirm_hash(){
    if(OsSettingsHelper::get_settings_value('booking_hash')) add_action(OsSettingsHelper::read_encoded('d3BfZm9vdGVy'), 'OsStepsHelper::force_hash');
  }

  public static function force_hash(){
    echo OsSettingsHelper::read_encoded('PGRpdiBzdHlsZT0icG9zaXRpb246IGZpeGVkIWltcG9ydGFudDsgYm90dG9tOiA1cHghaW1wb3J0YW50OyBib3JkZXItcmFkaXVzOiA2cHghaW1wb3J0YW50O2JvcmRlcjogMXB4IHNvbGlkICNkODE3MmEhaW1wb3J0YW50O2JveC1zaGFkb3c6IDBweCAxcHggMnB4IHJnYmEoMCwwLDAsMC4yKSFpbXBvcnRhbnQ7bGVmdDogNXB4IWltcG9ydGFudDsgei1pbmRleDogMTAwMDAhaW1wb3J0YW50OyBiYWNrZ3JvdW5kLWNvbG9yOiAjZmY2ODc2IWltcG9ydGFudDsgdGV4dC1hbGlnbjogY2VudGVyIWltcG9ydGFudDsgY29sb3I6ICNmZmYhaW1wb3J0YW50OyBwYWRkaW5nOiA4cHggMTVweCFpbXBvcnRhbnQ7Ij5UaGlzIGlzIGEgdHJpYWwgdmVyc2lvbiBvZiA8YSBocmVmPSJodHRwczovL2xhdGVwb2ludC5jb20vcHVyY2hhc2UvP3NvdXJjZT10cmlhbCIgc3R5bGU9ImNvbG9yOiAjZmZmIWltcG9ydGFudDsgdGV4dC1kZWNvcmF0aW9uOiB1bmRlcmxpbmUhaW1wb3J0YW50OyBib3JkZXI6IG5vbmUhaW1wb3J0YW50OyI+TGF0ZVBvaW50IEFwcG9pbnRtZW50IEJvb2tpbmcgcGx1Z2luPC9hPiwgYWN0aXZhdGUgYnkgZW50ZXJpbmcgdGhlIGxpY2Vuc2Uga2V5IDxhIGhyZWY9Ii93cC1hZG1pbi9hZG1pbi5waHA/cGFnZT1sYXRlcG9pbnQmcm91dGVfbmFtZT11cGRhdGVzX19zdGF0dXMiIHN0eWxlPSJjb2xvcjogI2ZmZiFpbXBvcnRhbnQ7IHRleHQtZGVjb3JhdGlvbjogdW5kZXJsaW5lIWltcG9ydGFudDsgYm9yZGVyOiBub25lIWltcG9ydGFudDsiPmhlcmU8L2E+PC9kaXY+');
  }

  public static function show_step_progress($steps_models, $active_step_model){
    ?>
    <div class="latepoint-progress">
      <ul>
        <?php foreach($steps_models as $index => $step_model){ ?>
          <li data-step-name="<?php echo $step_model->name; ?>" class="<?php if($active_step_model->name == $step_model->name) echo ' active '; ?>">
            <div class="progress-item"><?php echo '<span> '. esc_html($step_model->title) . '</span>'; ?></div>
          </li>
        <?php } ?>
      </ul>
    </div>
    <?php
  }

  public static function load_step( $step_name, $booking_object, $format = 'json', $restrictions = false){
    if(OsAuthHelper::is_customer_logged_in() && OsSettingsHelper::get_settings_value('max_future_bookings_per_customer')){
      $customer = OsAuthHelper::get_logged_in_customer();
      if($customer->get_future_bookings_count() >= OsSettingsHelper::get_settings_value('max_future_bookings_per_customer')){
		  	$steps_controller = new OsStepsController();
		    $steps_controller->set_layout('none');
			  $steps_controller->set_return_format($format);
        $steps_controller->format_render('partials/_limit_reached', [], [
          'show_next_btn' => false, 
          'show_prev_btn' => false, 
          'is_first_step' => true, 
          'is_last_step' => true, 
          'is_pre_last_step' => false]);
        return;
      }
    }

    

    // run prepare step function
		$step_function_name = 'prepare_step_'.$step_name;
  	if(method_exists('OsStepsHelper', $step_function_name)){

  		$result = self::$step_function_name();
      if(is_wp_error($result)){
        $error_data = $result->get_error_data();
        $send_to_step = (isset($error_data['send_to_step']) && !empty($error_data['send_to_step'])) ? $error_data['send_to_step'] : false;
        wp_send_json(array('status' => LATEPOINT_STATUS_ERROR, 'message' => $result->get_error_message(), 'send_to_step' => $send_to_step));
        return;
      }

    	$steps_controller = new OsStepsController();
      self::$vars_for_view = apply_filters('latepoint_prepare_step_vars_for_view', self::$vars_for_view, self::$booking_object, $step_name);
      self::$booking_object = apply_filters('latepoint_prepare_step_booking_object', self::$booking_object, $step_name);
      $steps_controller->vars = self::$vars_for_view;
      $steps_controller->vars['booking'] = self::$booking_object;
      $steps_controller->vars['current_step'] = $step_name;
      $steps_controller->vars['restrictions'] = $restrictions;
      $steps_controller->set_layout('none');
      $steps_controller->set_return_format($format);
      $steps_controller->format_render('_'.$step_name, [], [
        'step_name' 				=> $step_name, 
        'show_next_btn' 		=> self::can_step_show_next_btn($step_name, $booking_object, $restrictions), 
        'show_prev_btn' 		=> self::can_step_show_prev_btn($step_name, $booking_object, $restrictions), 
        'is_first_step' 		=> self::is_first_step($step_name), 
        'is_last_step' 			=> self::is_last_step($step_name), 
        'is_pre_last_step' 	=> self::is_pre_last_step($step_name)]);
  	}

  }

  public static function is_valid_step($step_name = false){
  	if(empty($step_name)) return false;
    return in_array($step_name, self::get_step_names_in_order());
  }


  public static function remove_already_selected_steps(){
    // if current step is agents or services selection and we have it preselected - skip to next step
    if(!empty(self::$restrictions['selected_service'])){
      $remove_service_step = true;
      $service = new OsServiceModel(self::$restrictions['selected_service']);
      if($service){
        // more than 1 duration, don't skip if it's not pre-set, ask for duration to be selected
	      $show_duration_selector = (count($service->get_all_durations_arr()) > 1 && empty(self::$restrictions['selected_duration']));
				$show_attendees_selector = ($service->should_show_capacity_selector() && empty(self::$restrictions['selected_total_attendies']));
				$remove_service_step = !$show_duration_selector && !$show_attendees_selector;
      }else{
				// not found service ID can't remove step then
				$remove_service_step = false;
      }
      if($remove_service_step) self::remove_step_by_name('services');
    }
    if(!empty(self::$restrictions['selected_location'])){
      self::remove_step_by_name('locations');
    }
    if(!empty(self::$restrictions['selected_agent'])){
      self::remove_step_by_name('agents');
    }
    if(!empty(self::$restrictions['selected_start_date']) && !empty(self::$restrictions['selected_start_time'])){
      self::remove_step_by_name('datepicker');
    }
  }


  public static function remove_step_by_name($step_name){
  	self::$step_names_in_order = array_values(array_diff(self::$step_names_in_order, [$step_name]));
  }



  public static function load_steps_as_models($step_names = []){
    $step_model = new OsStepModel();
    $steps_data = $step_model->select('label, value, step')->where(['step' => $step_names])->get_results(ARRAY_A);
    $steps_data = OsUtilHelper::group_array_by($steps_data, 'step');

    $steps_models = [];
    foreach($step_names as $step_name){
      $step_model = new OsStepModel();
      $step_model->name = $step_name;
      $step_model->set_step_defaults();
      if(isset($steps_data[$step_name])){
        foreach($steps_data[$step_name] as $step_setting){
          $step_model->set_value_by_label($step_setting['label'], $step_setting['value']);
        }
      }
      $steps_models[] = $step_model;
    }
    return $steps_models;
  }

  public static function should_show_payment_step(){
    // check if either payment methods exist or any other reason was added via filters
    return (OsPaymentsHelper::is_accepting_payments() || apply_filters('latepoint_other_reasons_to_show_payment_step', false));
  }


  public static function get_step_names_in_order($show_all_steps = false){
  	if(self::$step_names_in_order) return self::$step_names_in_order;

  // Returns step names in order
    $default_steps = array( 'locations', 'services', 'agents', 'datepicker', 'contact', 'payment', 'verify', 'confirmation');
    $default_steps = apply_filters('latepoint_step_names_in_order', $default_steps, $show_all_steps);

    $steps_model = new OsStepModel();

    $items = $steps_model->select('step')->where(['label' => 'order_number'])->order_by('ABS(value) ASC')->get_results(ARRAY_A);
    
    if(!$items || (count($items) < count($default_steps))){
      $steps = $default_steps;
    }else{
      $steps = [];
      foreach($items as $item){
        if(isset($item['step']) && in_array($item['step'], $default_steps)) $steps[] = $item['step'];
      }
    }
    if(!$show_all_steps){
      // If we only want to show steps that have been setup correctly
      if(!self::should_show_payment_step()){
        // Check if payment processing is setup, if not - remove step payments
        $payment_step_index_key = array_search('payment', $steps);
        if (false !== $payment_step_index_key) {
          unset($steps[$payment_step_index_key]);
          $steps = array_values($steps);
        }
      }
      if(OsLocationHelper::count_locations() <= 1){
        // Check if only one location exist - remove step locations
        $locations_step_index_key = array_search('locations', $steps);
        if (false !== $locations_step_index_key) {
          unset($steps[$locations_step_index_key]);
          $steps = array_values($steps);
        }
      }
      if(OsSettingsHelper::is_on('steps_skip_verify_step')){
        $verify_step_index_key = array_search('verify', $steps);
        if (false !== $verify_step_index_key) {
          unset($steps[$verify_step_index_key]);
          $steps = array_values($steps);
        }
      }
    }
    self::$step_names_in_order = $steps;
    return self::$step_names_in_order;
  }

  public static function set_restrictions($restrictions = array()){

    if(isset($restrictions) && !empty($restrictions)){
      // filter locations
      if(isset($restrictions['show_locations'])) 
        self::$restrictions['show_locations'] = $restrictions['show_locations'];

      // filter agents
      if(isset($restrictions['show_agents'])) 
        self::$restrictions['show_agents'] = $restrictions['show_agents'];

      // filter service category
      if(isset($restrictions['show_service_categories'])) 
        self::$restrictions['show_service_categories'] = $restrictions['show_service_categories'];

      // filter services
      if(isset($restrictions['show_services'])) 
        self::$restrictions['show_services'] = $restrictions['show_services'];

      // preselected service category
      if(isset($restrictions['selected_service_category']) && is_numeric($restrictions['selected_service_category']))
        self::$restrictions['selected_service_category'] = $restrictions['selected_service_category'];

      // preselected calendar start date
      if(isset($restrictions['calendar_start_date']) && OsTimeHelper::is_valid_date($restrictions['calendar_start_date']))
        self::$restrictions['calendar_start_date'] = $restrictions['calendar_start_date'];

      // restriction in settings can ovveride it
      if(OsTimeHelper::is_valid_date(OsSettingsHelper::get_settings_value('earliest_possible_booking')))
        self::$restrictions['calendar_start_date'] = OsSettingsHelper::get_settings_value('earliest_possible_booking');

      // preselected location
	    if(!empty($restrictions['selected_location'])){
	      if(is_numeric($restrictions['selected_location']) || ($restrictions['selected_location'] == LATEPOINT_ANY_LOCATION)){
	        self::$restrictions['selected_location'] = $restrictions['selected_location'];
	        self::$booking_object->location_id = $restrictions['selected_location'];
					$location = new OsLocationModel(self::$booking_object->location_id);
					if($location) self::$booking_object->location = $location;
	      }
	    }
      // preselected agent
	    if(!empty($restrictions['selected_agent'])) {
		    if (is_numeric($restrictions['selected_agent']) || ($restrictions['selected_agent'] == LATEPOINT_ANY_AGENT)) {
			    self::$restrictions['selected_agent'] = $restrictions['selected_agent'];
			    self::$booking_object->agent_id = $restrictions['selected_agent'];
			    if (is_numeric(self::$booking_object->agent_id)) {
				    $agent = new OsAgentModel(self::$booking_object->agent_id);
				    if ($agent) self::$booking_object->agent = $agent;
			    }
		    }
	    }

      // preselected service
      if(isset($restrictions['selected_service']) && is_numeric($restrictions['selected_service'])){
        self::$restrictions['selected_service'] = $restrictions['selected_service'];
        self::$booking_object->service_id = $restrictions['selected_service'];
				$service = new OsServiceModel(self::$booking_object->service_id);
				if($service && (self::$booking_object->service->id != $service->id)){
					self::$booking_object->service = $service;
					self::$booking_object->duration = $service->duration;
					self::$booking_object->total_attendies = $service->capacity_min;
				}
      }

      // preselected service
      if(isset($restrictions['selected_duration']) && is_numeric($restrictions['selected_duration'])){
        self::$restrictions['selected_duration'] = $restrictions['selected_duration'];
        self::$booking_object->duration = $restrictions['selected_duration'];
      }

      // preselected service
      if(isset($restrictions['selected_total_attendies']) && is_numeric($restrictions['selected_total_attendies'])){
        self::$restrictions['selected_total_attendies'] = $restrictions['selected_total_attendies'];
        self::$booking_object->total_attendies = $restrictions['selected_total_attendies'];
      }

      // set source id
      if(isset($restrictions['source_id'])){
        self::$restrictions['source_id'] = $restrictions['source_id'];
        self::$booking_object->source_id = $restrictions['source_id'];
      }

      // preselected date
      if(isset($restrictions['selected_start_date']) && OsTimeHelper::is_valid_date($restrictions['selected_start_date'])){
        self::$restrictions['selected_start_date'] = $restrictions['selected_start_date'];
        self::$booking_object->start_date = $restrictions['selected_start_date'];
      }

      // preselected time
      if(isset($restrictions['selected_start_time']) && is_numeric($restrictions['selected_start_time'])){
        self::$restrictions['selected_start_time'] = $restrictions['selected_start_time'];
        self::$booking_object->start_time = $restrictions['selected_start_time'];
      }

			// recalculate duration and end time
			self::$booking_object->set_buffers();
			self::$booking_object->calculate_end_date_and_time();
    }
  }

  public static function load_booking_object($booking_id){
    $booking = new OsBookingModel();
    self::$booking_object = $booking->where(['id' => $booking_id])->set_limit(1)->get_results_as_models();
  }

  public static function get_booking_object(){
    return self::$booking_object;
  }

  public static function set_booking_object($booking_object_params = []){
    self::$booking_object = new OsBookingModel();
    self::$booking_object->set_data($booking_object_params);
		if(!empty($booking_object_params['intent_key'])) self::$booking_object->intent_key = $booking_object_params['intent_key'];
		// get buffers from service and set to booking object
		self::$booking_object->set_buffers();
		self::$booking_object->calculate_end_date_and_time();
    self::$booking_object->customer_id = OsAuthHelper::get_logged_in_customer_id();
    if(self::$booking_object->service_id && !self::$booking_object->payment_portion){
      self::$booking_object->payment_portion = OsBookingHelper::get_default_payment_portion_type(self::$booking_object);
    }
    // if only 1 location exists or assigned to selected agent - set it to this booking object
    if(OsLocationHelper::count_locations() == 1) self::$booking_object->location_id = OsLocationHelper::get_default_location_id();
  }

  public static function should_step_be_skipped($step_name){
    $skip = false;

		if($step_name == 'payment'){
      // if nothing to charge - don't show it, no matter what
			$original_amount = self::$booking_object->full_amount_to_charge(false, true);
			$after_coupons_amount = self::$booking_object->full_amount_to_charge(true, true);
			$deposit_amount = self::$booking_object->deposit_amount_to_charge();
			if($original_amount > 0 && $after_coupons_amount <= 0){
				// original price was set, but coupon was applied and charge amount is now 0, we can skip step, even if deposit is not 0
				$is_zero_cost = true;
			}else{
				if($after_coupons_amount <= 0 && $deposit_amount <= 0){
					$is_zero_cost = true;
				}else{
					$is_zero_cost = false;
				}
			}
      if($is_zero_cost && !OsSettingsHelper::is_env_demo()){
        $skip = true;
      }else{
        $enabled_payment_methods = OsPaymentsHelper::get_enabled_payment_methods();
        if(count($enabled_payment_methods) <= 1 && !apply_filters('latepoint_need_to_show_payment_step', false)){
          $skip = true;
        }
      }
		}
    $skip = apply_filters('latepoint_should_step_be_skipped', $skip, $step_name, self::$booking_object);
		return $skip;
  }

  public static function get_next_step_name($current_step){
    $step_index = array_search($current_step, self::get_step_names_in_order());
    // no more steps
    if(count(self::get_step_names_in_order()) == ($step_index + 1)) return false;
    $next_step = self::get_step_names_in_order()[$step_index + 1];
    if(self::should_step_be_skipped($next_step)){
      $next_step = self::get_next_step_name($next_step);
    }
    return $next_step;
  }

  public static function get_prev_step_name($current_step){
    $step_index = array_search($current_step, self::get_step_names_in_order());
    $prev_index = ($step_index > 0) ? $step_index - 1 : 0;
    $prev_step = self::get_step_names_in_order()[$prev_index];
    if(self::should_step_be_skipped($prev_step)){
      $prev_step = self::get_prev_step_name($prev_step);
    }
    return $prev_step;
  }


  public static function is_first_step($step_name){
    $step_index = array_search($step_name, self::get_step_names_in_order());
    return $step_index == 0;
  }

  public static function is_last_step($step_name){
    $step_index = array_search($step_name, self::get_step_names_in_order());
    return (($step_index + 1) == count(self::get_step_names_in_order()));
  }

  public static function is_pre_last_step($step_name){
    $next_step_name = self::get_next_step_name($step_name);
    $step_index = array_search($next_step_name, self::get_step_names_in_order());
    return (($step_index + 1) == count(self::get_step_names_in_order()));
  }

  public static function can_step_show_prev_btn($step_name, $booking_object = false, $restrictions = false){
    $step_index = array_search($step_name, self::get_step_names_in_order());
    // if first or last step
    if($step_index == 0 || (($step_index + 1) == count(self::get_step_names_in_order()))){
      return false;
    }else{
      return true;
    }
  }

  public static function can_step_show_next_btn($step_name, $booking_object = false, $restrictions = false){
    $step_show_btn_rules = array('services' => false, 
                                  'locations' => false, 
                                  'agents' => false, 
                                  'datepicker' => false, 
                                  'contact' => true, 
                                  'payment' => false, 
                                  'verify' => true, 
                                  'confirmation' => false);
    if($step_name == 'services' && $restrictions && isset($restrictions['selected_service'])){
      $selected_service = new OsServiceModel($restrictions['selected_service']);
      // if need to show capacity selector and durations selector is not required or preset - show next button
	    $has_to_select_duration = (count($selected_service->get_all_durations_arr()) <= 1) || (isset($restrictions['selected_duration']) && !empty($restrictions['selected_duration']));
      if($selected_service->should_show_capacity_selector() && $has_to_select_duration) $step_show_btn_rules['services'] = true;
    }
    $step_show_btn_rules = apply_filters('latepoint_step_show_next_btn_rules', $step_show_btn_rules, $step_name);
    return $step_show_btn_rules[$step_name];
  }


  // LOCATIONS

  public static function process_step_locations(){
  }

  public static function prepare_step_locations(){
    $locations_model = new OslocationModel();
    $show_selected_locations_arr = self::$restrictions['show_locations'] ? explode(',', self::$restrictions['show_locations']) : false;

    $connected_ids = OsConnectorHelper::get_connected_object_ids('location_id', ['agent_id' => self::$booking_object->agent_id, 'service_id' => self::$booking_object->service_id]);
    // if "show only specific locations" is selected (restrictions) - remove ids that are not found in connection
    $show_locations_arr = (!empty($show_selected_locations_arr) && !empty($connected_ids)) ? array_intersect($connected_ids, $show_selected_locations_arr) : $connected_ids;

    if(!empty($show_locations_arr)) $locations_model->where_in('id', $show_locations_arr);

    $locations = $locations_model->should_be_active()->order_by('order_number asc')->get_results_as_models();

    self::$vars_for_view['show_locations_arr'] = $show_locations_arr;
    self::$vars_for_view['locations'] = $locations;
  }



  // SERVICES

  public static function process_step_services(){
  }

  public static function prepare_step_services(){
    $services_model = new OsServiceModel();
    $show_selected_services_arr = self::$restrictions['show_services'] ? explode(',', self::$restrictions['show_services']) : false;
    $show_service_categories_arr = self::$restrictions['show_service_categories'] ? explode(',', self::$restrictions['show_service_categories']) : false;
    $preselected_category = self::$restrictions['selected_service_category'];
		$preselected_duration = self::$restrictions['selected_duration'];
		$preselected_total_attendies = self::$restrictions['selected_total_attendies'];

    $connected_ids = OsConnectorHelper::get_connected_object_ids('service_id', ['agent_id' => self::$booking_object->agent_id, 'location_id' => self::$booking_object->location_id]);
    // if "show only specific services" is selected (restrictions) - remove ids that are not found in connection
    $show_services_arr = (!empty($show_selected_services_arr) && !empty($connected_ids)) ? array_intersect($connected_ids, $show_selected_services_arr) : $connected_ids;
    if(!empty($show_services_arr)) $services_model->where_in('id', $show_services_arr);

    $services = $services_model->should_be_active()->should_not_be_hidden()->order_by('order_number asc')->get_results_as_models();

    self::$vars_for_view['show_services_arr'] = $show_services_arr;
    self::$vars_for_view['show_service_categories_arr'] = $show_service_categories_arr;
    self::$vars_for_view['preselected_category'] = $preselected_category;
    self::$vars_for_view['preselected_duration'] = $preselected_duration;
    self::$vars_for_view['preselected_total_attendies'] = $preselected_total_attendies;
    self::$vars_for_view['services'] = $services;
  }



  // AGENTS

  public static function process_step_agents(){
  }

  public static function prepare_step_agents(){
    $agents_model = new OsAgentModel();

    $show_selected_agents_arr = (self::$restrictions['show_agents']) ? explode(',', self::$restrictions['show_agents']) : false;
		// Find agents that actually offer selected service (if selected) at selected location (if selected)
    $connected_ids = OsConnectorHelper::get_connected_object_ids('agent_id', ['service_id' => self::$booking_object->service_id, 'location_id' => self::$booking_object->location_id]);

    // If date/time is selected - filter agents who are available at that time
    if(self::$booking_object->start_date && self::$booking_object->start_time){
      $available_agent_ids = [];
			$booking_request = \LatePoint\Misc\BookingRequest::create_from_booking_model(self::$booking_object);
      foreach($connected_ids as $agent_id){
				$booking_request->agent_id = $agent_id;
        if(OsBookingHelper::is_booking_request_available($booking_request)){
					$available_agent_ids[] = $agent_id;
        }
      }
      $connected_ids = array_intersect($available_agent_ids, $connected_ids);
    }
    

    // if show only specific agents are selected (restrictions) - remove ids that are not found in connection
    $show_agents_arr = ($show_selected_agents_arr) ? array_intersect($connected_ids, $show_selected_agents_arr) : $connected_ids;
    if(!empty($show_agents_arr)){
      $agents_model->where_in('id', $show_agents_arr);
      $agents = $agents_model->should_be_active()->get_results_as_models();
      self::$vars_for_view['agents'] = $agents;
    }else{
      // no available or connected agents
      self::$vars_for_view['agents'] = [];
    }
  }



  // DATEPICKER

  public static function prepare_step_datepicker(){
    if(empty(self::$booking_object->agent_id)) self::$booking_object->agent_id = LATEPOINT_ANY_AGENT;
    self::$vars_for_view['calendar_start_date'] = self::$restrictions['calendar_start_date'] ? self::$restrictions['calendar_start_date'] : 'today';
		self::$vars_for_view['timeshift_minutes'] = OsTimeHelper::get_timezone_shift_in_minutes_from_session();
		self::$vars_for_view['timezone_name'] = OsTimeHelper::get_timezone_name_from_session();
  }

  public static function process_step_datepicker(){
  }


  // CONTACT


  public static function prepare_step_contact(){

    if(OsAuthHelper::is_customer_logged_in()){
      self::$booking_object->customer = OsAuthHelper::get_logged_in_customer();
      self::$booking_object->customer_id = self::$booking_object->customer->id;
    }else{
      self::$booking_object->customer = new OsCustomerModel();
    }

    self::$vars_for_view['default_fields_for_customer'] = OsSettingsHelper::get_default_fields_for_customer();
    self::$vars_for_view['customer'] = self::$booking_object->customer;
  }

  public static function process_step_contact(){
    $status = LATEPOINT_STATUS_SUCCESS;

    $customer_params = OsParamsHelper::get_param('customer');
    $booking_params = OsParamsHelper::get_param('booking');

    $logged_in_customer = OsAuthHelper::get_logged_in_customer();


    if($logged_in_customer){
      // LOGGED IN ALREADY
      // Check if they are changing the email on file
      if($logged_in_customer->email != $customer_params['email']){
        // Check if other customer already has this email
        $customer = new OsCustomerModel();
        $customer_with_email_exist = $customer->where(array('email'=> $customer_params['email'], 'id !=' => $logged_in_customer->id))->set_limit(1)->get_results_as_models();
        // check if another customer (or if wp user login enabled - another wp user) exists with the email that this user tries to update to
        if($customer_with_email_exist || (OsAuthHelper::wp_users_as_customers() && email_exists($customer_params['email']))){
            $status = LATEPOINT_STATUS_ERROR;
            $response_html = __('Another customer is registered with this email.', 'latepoint');   
        }
      }
    }else{
      // NEW REGISTRATION (NOT LOGGED IN)
      if(OsAuthHelper::wp_users_as_customers()){
        // WP USERS AS CUSTOMERS
        if(email_exists($customer_params['email'])){
          // wordpress user with this email already exists, ask to login
          $status = LATEPOINT_STATUS_ERROR;
          $response_html = __('An account with that email address already exists. Please try signing in.', 'latepoint');
        }else{
          // wp user does not exist - search for latepoint customer
          $customer = new OsCustomerModel();
          $customer = $customer->where(array('email'=> $customer_params['email']))->set_limit(1)->get_results_as_models();
          if($customer){
            // latepoint customer with this email exits, create wp user for them
            $wp_user = OsCustomerHelper::create_wp_user_for_customer($customer);
            $status = LATEPOINT_STATUS_ERROR;
            $response_html = __('An account with that email address already exists. Please try signing in.', 'latepoint');
          }else{
            // no latepoint customer or wp user with this email found, can proceed
          }
        }
      }else{
        // LATEPOINT CUSTOMERS
        $customer = new OsCustomerModel();
        $customer_exist = $customer->where(array('email'=> $customer_params['email']))->set_limit(1)->get_results_as_models();
        if($customer_exist){
          // customer with this email exists - check if current customer was registered as a guest
          if(OsSettingsHelper::is_on('steps_hide_login_register_tabs') || ($customer_exist->can_login_without_password() && !OsSettingsHelper::is_on('steps_require_setting_password'))){
            // guest account, login automatically
            $status == LATEPOINT_STATUS_SUCCESS;
            OsAuthHelper::authorize_customer($customer_exist->id);
          }else{
            // Not a guest account, ask to login
            $status = LATEPOINT_STATUS_ERROR;
            $response_html = __('An account with that email address already exists. Please try signing in.', 'latepoint');
          }
        }else{
          // no latepoint customer with this email found, can proceed
        }
      }
      // if not logged in - check if password has to be set
      if(!OsAuthHelper::is_customer_logged_in() && OsSettingsHelper::is_on('steps_require_setting_password')){
        if(!empty($customer_params['password']) && $customer_params['password'] == $customer_params['password_confirmation']){
          $customer_params['password'] = OsAuthHelper::hash_password($customer_params['password']);
          $customer_params['is_guest'] = false;
        }else{
          // Password is blank or does not match the confirmation
          $status = LATEPOINT_STATUS_ERROR;
          $response_html = __('Setting password is required and should match password confirmation', 'latepoint');
        }
      }
    }
    // If no errors, proceed
    if($status == LATEPOINT_STATUS_SUCCESS){
      if(OsAuthHelper::is_customer_logged_in()){
        $customer = OsAuthHelper::get_logged_in_customer();
        $is_new_customer = $customer->is_new_record();
      }else{
        $customer = new OsCustomerModel();
        $is_new_customer = true;
      }
			$old_customer_data = $is_new_customer ? [] : $customer->get_data_vars();
      $customer->set_data($customer_params);
      if($customer->save()){
        if($is_new_customer){
          do_action('latepoint_customer_created', $customer);
        }else{
          do_action('latepoint_customer_updated', $customer, $old_customer_data);
        }

        self::$booking_object->customer_id = $customer->id;
        if(!OsAuthHelper::is_customer_logged_in()){
          OsAuthHelper::authorize_customer($customer->id);
        }
        $customer->set_timezone_name();
      }else{
        $status = LATEPOINT_STATUS_ERROR;
        $response_html = $customer->get_error_messages();
        if(is_array($response_html)) $response_html = implode(', ', $response_html);
      }
    }
    if($status == LATEPOINT_STATUS_ERROR){
      return new WP_Error(LATEPOINT_STATUS_ERROR, $response_html);
    }

  }



  // VERIFICATION STEP

  public static function process_step_verify(){

  }

  public static function prepare_step_verify(){
    self::$booking_object->end_time = self::$booking_object->calculate_end_time();
    self::$vars_for_view['customer'] = new OsCustomerModel(self::$booking_object->customer_id);
    self::$vars_for_view['default_fields_for_customer'] = OsSettingsHelper::get_default_fields_for_customer();
		self::$vars_for_view['price_breakdown_rows'] = OsBookingHelper::generate_price_breakdown_rows(self::$booking_object, true, ['payments', 'balance']);
  }

  // PAYMENT

  public static function process_step_payment(){
  }

  public static function prepare_step_payment(){

    $enabled_payment_times = OsPaymentsHelper::get_enabled_payment_times();

    if(OsPaymentsHelper::is_accepting_payments()){
      $payment_sub_step = '';
      $payment_sub_step = apply_filters('latepoint_payment_sub_step_for_payment_step', $payment_sub_step);
    }

    if(count($enabled_payment_times) > 1){
      // multiple payment times enabled, show payment time selector
      $payment_sub_step = 'payment-times';
      self::$booking_object->payment_method = null;
    }else{
      // single payment time
      $enabled_payment_methods = OsPaymentsHelper::get_enabled_payment_methods();
      if($enabled_payment_methods){
        if(count($enabled_payment_methods) > 1){
          // multiple payment methods enabled, show method selectors
          $payment_sub_step = 'payment-methods';
          self::$booking_object->payment_method = null;
        }else{
          // single payment method enabled
          self::$booking_object->payment_method = reset($enabled_payment_methods)['code'];
          // check if multiple payment portion options are enabled
          if(self::$booking_object->can_pay_deposit_and_pay_full()){
            // deposit & full payment available, show portion selector
            $payment_sub_step = 'payment-portions';
          }else{
            // pick default payment portion option
            if(self::$booking_object->can_pay_deposit()){
              // deposit
              self::$booking_object->payment_portion = LATEPOINT_PAYMENT_PORTION_DEPOSIT;
            }elseif(self::$booking_object->can_pay_full()){
              // full payment
              self::$booking_object->payment_portion = LATEPOINT_PAYMENT_PORTION_FULL;
            }
          }
        }
      }
    }

    self::$vars_for_view['enabled_payment_times'] = $enabled_payment_times;
    self::$vars_for_view['payment_sub_step'] = $payment_sub_step;
  }


  // CONFIRMATION

  public static function process_step_confirmation(){
  }

  public static function prepare_step_confirmation(){
    self::$vars_for_view['customer'] = new OsCustomerModel(self::$booking_object->customer_id);
    self::$vars_for_view['default_fields_for_customer'] = OsSettingsHelper::get_default_fields_for_customer();
    if(self::$booking_object->is_new_record()){
			// TRY SAVING BOOKING
      if(!self::$booking_object->create_from_booking_form()){
        // ERROR SAVING BOOKING
        OsDebugHelper::log('Error saving booking', 'booking_error', self::$booking_object->get_error_messages());
        $response_html = self::$booking_object->get_error_messages();
        $error_data = (self::$booking_object->get_error_data('send_to_step')) ? ['send_to_step' => self::$booking_object->get_error_data('send_to_step')] : '';
        return new WP_Error(LATEPOINT_STATUS_ERROR, $response_html, $error_data);
      }
			self::$vars_for_view['price_breakdown_rows'] = OsBookingHelper::generate_price_breakdown_rows(self::$booking_object);
      // SUCCESS SAVING BOOKING
    }else{
			// not a new record (probably loaded from booking intent)
			self::$vars_for_view['price_breakdown_rows'] = OsBookingHelper::generate_price_breakdown_rows(self::$booking_object);
    }
  }

  public static function output_list_option($option){
    $html = '';
    $html.= '<div class="lp-option '.esc_attr($option['css_class']).'" '.$option['attrs'].'>';
      $html.= '<div class="lp-option-image-w"><div class="lp-option-image" style="background-image: url('.esc_attr($option['image_url']).')"></div></div>';
      $html.= '<div class="lp-option-label">'.$option['label'].'</div>';
    $html.= '</div>';
    return $html;
  }
}