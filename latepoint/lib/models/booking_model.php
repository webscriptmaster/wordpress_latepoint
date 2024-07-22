<?php

/**
 * @property OsCustomerModel $customer
 * @property OsAgentModel $agent
 * @property OsServiceModel $service
 * @property OsLocationModel $location
 */
class OsBookingModel extends OsModel{
  var $id,
      $booking_code,
      $service_id,
      $customer_id,
      $agent_id,
      $location_id,
      $buffer_before = 0,
      $buffer_after = 0,
      $status,
      $payment_status = LATEPOINT_PAYMENT_STATUS_NOT_PAID,
      $start_date,
      $end_date,
      $start_time,
      $end_time,
		  $start_datetime_utc,
		  $end_datetime_utc,
      $payment_method,
      $payment_portion,
      $payment_token,
      $intent_key,
      $ip_address,
      $source_id,
      $source_url,
      $coupon_code,
      $coupon_discount,
      $duration,
      $price,
		  $subtotal,
      $total_attendies = 1,
      $customer_comment,
      $total_attendies_sum = 1,
      $total_customers = 1,
	  $meta_class = 'OsBookingMetaModel',
      $updated_at,
      $created_at;

  function __construct($id = false){
    parent::__construct();
    $this->table_name = LATEPOINT_TABLE_BOOKINGS;
    $this->nice_names = array('service_id' => __('Service', 'latepoint'), 
                              'agent_id' => __('Agent', 'latepoint'));

    if($id){
      $this->load_by_id($id);
    }
  }

	public function filter_allowed_records(): OsModel{
		if(!OsRolesHelper::are_all_records_allowed()) {
			if (!OsRolesHelper::are_all_records_allowed('agent')) {
				$this->filter_where_conditions(['agent_id' => OsRolesHelper::get_allowed_records('agent')]);
			}
			if (!OsRolesHelper::are_all_records_allowed('location')) {
				$this->filter_where_conditions(['location_id' => OsRolesHelper::get_allowed_records('location')]);
			}
			if (!OsRolesHelper::are_all_records_allowed('service')) {
				$this->filter_where_conditions(['service_id' => OsRolesHelper::get_allowed_records('service')]);
			}
		}
		return $this;
	}

	public function properties_to_query(): array{
		return [
			'service_id' => __('Service', 'latepoint'),
			'agent_id' => __('Agent', 'latepoint'),
			'status' => __('Status', 'latepoint'),
			'payment_status' => __('Payment Status', 'latepoint'),
			'start_datetime_utc' => __('Start Time', 'latepoint'),
		];
	}

	// gets price from model itself
	public function get_formatted_price(){
		return OsMoneyHelper::format_price($this->price, true, true);
	}

	public function generate_data_vars(): array{
		$vars = [
			'id' => $this->id,
      'booking_code' => $this->booking_code,
      'start_datetime' => $this->format_start_date_and_time_rfc3339(),
      'end_datetime' => $this->format_end_date_and_time_rfc3339(),
      'service_name' => $this->service->name,
      'duration' => $this->duration,
      'price' => $this->formatted_price,
      'subtotal_amount' => $this->subtotal * 1,
      'total_amount' => $this->price * 1,
      'balance_due_amount' => $this->get_total_balance_due(),
			'total_payments' => $this->get_total_amount_paid_from_transactions(),
			'payment_method' => $this->payment_method,
			'payment_portion' => $this->payment_portion,
      'customer_comment' => $this->customer_comment,
      'status' => $this->status,
      'payment_status' => $this->payment_status,
      'start_date' => $this->format_start_date(),
      'start_time' => OsTimeHelper::minutes_to_hours_and_minutes($this->start_time),
      'timezone' => OsTimeHelper::get_wp_timezone_name(),
      'agent' => $this->agent->get_data_vars(),
      'customer' => $this->customer->get_data_vars(),
			'transactions' => [],
			'source_id' => $this->source_id,
			'source_url' => $this->source_url,
			'created_datetime' => $this->format_created_datetime_rfc3339(),
			'price_breakdown' => $this->get_readable_price_breakdown()
		];
		$transactions = $this->get_transactions();
		if($transactions){
			foreach($transactions as $transaction){
				$vars['transactions'][] = $transaction->get_data_vars();
			}
		}
		return $vars;
	}


	public function get_readable_price_breakdown($include_totals = false): array{
		$price_breakdown = json_decode($this->get_meta_by_key('price_breakdown', ''), true);
		$readable_price_breakdown = [];
		if(empty($price_breakdown)) return $readable_price_breakdown;
		if(!empty($price_breakdown['before_subtotal'])){
			$readable_price_breakdown['line_items'] = OsBookingHelper::unfold_price_breakdown_row($price_breakdown['before_subtotal']);
		}
		if($include_totals) $readable_price_breakdown['subtotal_amount'] = $this->subtotal * 1;
		if(!empty($price_breakdown['after_subtotal'])){
			$readable_price_breakdown['adjustments'] = OsBookingHelper::unfold_price_breakdown_row($price_breakdown['after_subtotal']);
		}

		if($include_totals) $readable_price_breakdown['total_amount'] = $this->price * 1;
		if($include_totals) $readable_price_breakdown['total_payments'] = $this->get_total_amount_paid_from_transactions();
		if($include_totals) $readable_price_breakdown['balance_due_amount'] = $this->get_total_balance_due();

		return $readable_price_breakdown;
	}


	protected function params_to_sanitize(){
		return ['price' => 'money',
			'coupon_discount' => 'money',
			];
	}

	public function is_upcoming(): bool{
		$start_time_utc = new OsWpDateTime($this->start_datetime_utc, new DateTimeZone('UTC'));
		$now_time_utc = new OsWpDateTime('now', new DateTimeZone('UTC'));
		return ($start_time_utc > $now_time_utc);
	}

	public function set_utc_datetimes(){
		if(empty($this->start_date) || empty($this->end_date)) return;
		$start_datetime = OsWpDateTime::os_createFromFormat('Y-m-d H:i:s', $this->start_date.' '.OsTimeHelper::minutes_to_army_hours_and_minutes($this->start_time).':00');
		$end_datetime = OsWpDateTime::os_createFromFormat('Y-m-d H:i:s', $this->end_date.' '.OsTimeHelper::minutes_to_army_hours_and_minutes($this->end_time).':00');

		$this->start_datetime_utc = OsWpDateTime::datetime_in_utc($start_datetime, 'Y-m-d H:i:s');
		$this->end_datetime_utc = OsWpDateTime::datetime_in_utc($end_datetime, 'Y-m-d H:i:s');
	}

  public function get_total_balance_due($recalculate = false){
		$total = ($recalculate) ? $this->full_amount_to_charge() : $this->price;
		$payments = $this->get_total_amount_paid_from_transactions();
		return $total -$payments;
  }

  public function get_total_amount_paid_from_transactions(){
    $transactions_model = new OsTransactionModel();
    $transactions = $transactions_model->select('amount')->where(['booking_id' => $this->id])->get_results();
    $total = 0;
    foreach($transactions as $transaction){
      $total+= (float)$transaction->amount;
    }
    return $total;
  }

	public function delete($id = false){
    if(!$id && isset($this->id)){
      $id = $this->id;
    }

    $transactions = new OsTransactionModel();
    $transactions->delete_where(['booking_id' => $id]);
    $booking_metas = new OsBookingMetaModel();
    $booking_metas->delete_where(['object_id' => $id]);
		$process_jobs = new OsProcessJobModel();
    $process_jobs->delete_where(['object_id' => $id, 'object_model_type' => 'booking']);


    return parent::delete($id);
	}

  public function delete_meta_by_key($meta_key){
    if($this->is_new_record()) return true;

    $meta = new OsBookingMetaModel();
    return $meta->delete_by_key($meta_key, $this->id);
  }

  public function get_payment_status_nice_name($default = false){
    $payment_statuses = OsBookingHelper::get_payment_statuses_list();
    $nice_name = (!empty($this->payment_status) && isset($payment_statuses[$this->payment_status])) ? $payment_statuses[$this->payment_status] : $default;
    return $nice_name;
  }

  public function get_payment_portion_nice_name($default = false){
    $payment_portions = OsPaymentsHelper::get_payment_portions_list();
    $nice_name = (!empty($this->payment_portion) && isset($payment_portions[$this->payment_portion])) ? $payment_portions[$this->payment_portion] : $default;
    return $nice_name;
  }

  public function get_payment_method_nice_name($default = false){
    $payment_methods = OsBookingHelper::get_payment_methods_select_list();
    $nice_name = (!empty($this->payment_method) && isset($payment_methods[$this->payment_method])) ? $payment_methods[$this->payment_method] : $default;
    return $nice_name;
  }

	public function get_url_for_add_to_calendar_button(string $calendar_type):string{
		switch($calendar_type){
			case 'google':
				$url = 'https://calendar.google.com/calendar/render';
				$params = [
					'action' => 'TEMPLATE',
					'text' => $this->service->name,
					'dates' => $this->get_start_datetime_object(new DateTimeZone('UTC'))->format('Ymd\THis\Z').'/'.$this->get_end_datetime_object(new DateTimeZone('UTC'))->format('Ymd\THis\Z')
					];
				if(!empty($this->location->full_address)) $params['location'] = $this->location->full_address;
			break;
			case 'outlook':
				$url = 'https://outlook.live.com/calendar/0/deeplink/compose';
				$params = [
					'path' => '/calendar/action/compose',
					'rru' => 'addevent',
					'startdt' => $this->get_start_datetime_object(new DateTimeZone('UTC'))->format('Y-m-d\TH:i:s\Z'),
					'enddt' => $this->get_end_datetime_object(new DateTimeZone('UTC'))->format('Y-m-d\TH:i:s\Z'),
					'subject' => $this->service->name,
				];
			break;
		}
		/**
		 * Generate params for the add to calendar link
		 *
		 * @since 4.8.1
		 * @hook latepoint_build_add_to_calendar_link_params
		 *
		 * @param {array} $params Array of parameters that will be converted into a param query
		 * @param {string} $calendar_type Type of calendar the link is requested for
		 * @param {OsBookingModel} $booking A booking object
		 * @returns {array} The filtered array of appointment attributes
		 */
		$params = apply_filters('latepoint_build_add_to_calendar_link_params', $params, $calendar_type, $this);

		$url = $url.'?'.http_build_query($params);
		/**
		 * URL for the link for a button to add appointment to calendar
		 *
		 * @since 4.8.1
		 * @hook latepoint_build_add_to_calendar_link_url
		 *
		 * @param {array} $params Array of parameters that will be converted into a param query
		 * @param {string} $calendar_type Type of calendar the link is requested for
		 * @param {OsBookingModel} $booking A booking object
		 * @returns {string} The filtered url of adding appointment to calendar
		 */
		return apply_filters('latepoint_build_add_to_calendar_link_url', $url, $calendar_type, $this);
	}

  public function get_ical_download_link($key = false){
    return ($key) ? OsRouterHelper::build_admin_post_link(['manage_booking_by_key', 'ical_download'], ['key' => $key]) : OsRouterHelper::build_admin_post_link(['customer_cabinet', 'ical_download'], ['latepoint_booking_id' => $this->id]);
  }

  public function get_print_link($key = false){
    return ($key) ? OsRouterHelper::build_admin_post_link(['manage_booking_by_key', 'print_booking_info'], ['key' => $key]) : OsRouterHelper::build_admin_post_link(['customer_cabinet', 'print_booking_info'], ['latepoint_booking_id' => $this->id]);
  }

  public function get_meta_by_key($meta_key, $default = false){
    if($this->is_new_record()) return $default;

    $meta = new OsBookingMetaModel();
    return $meta->get_by_key($meta_key, $this->id, $default);
  }

  public function save_meta_by_key($meta_key, $meta_value){
    if($this->is_new_record()) return false;

    $meta = new OsBookingMetaModel();
    return $meta->save_by_key($meta_key, $meta_value, $this->id);
  }

	public function set_custom_end_time_and_date($booking_params){
		$end_ampm = isset($booking_params['end_time']['ampm']) ? $booking_params['end_time']['ampm'] : false;
		$this->end_time = OsTimeHelper::convert_time_to_minutes($booking_params['end_time']['formatted_value'], $end_ampm);
		if($this->end_time <= $this->start_time){
			// it's next day
      $date_obj = new OsWpDateTime($this->start_date);
      $this->end_date = $date_obj->modify('+1 day')->format('Y-m-d');
		}
		return $this;
	}

  public function calculate_end_date(){
		if(empty($this->start_time) || empty($this->start_date)) return $this->start_date;
    if(($this->start_time + $this->get_total_duration()) >= (24 * 60)){
      $date_obj = new OsWpDateTime($this->start_date);
      $end_date = $date_obj->modify('+1 day')->format('Y-m-d');
    }else{
      $end_date = $this->start_date;
    }
    return $end_date;
  }


  public function calculate_end_time(){
    $end_time = (int)$this->start_time + (int)$this->get_total_duration();
    // continues to next day?
    if($end_time > (24 * 60)){
      $end_time = $end_time - (24 * 60);
    }
    return $end_time;
  }

	public function calculate_end_date_and_time(){
		$this->end_time = $this->calculate_end_time();
		$this->end_date = $this->calculate_end_date();
	}

	public function prepare_data_before_it_is_set($data){
		if(isset($data['start_time']['formatted_value'])){
      $start_ampm = isset($data['start_time']['ampm']) ? $data['start_time']['ampm'] : false;
      $end_ampm = isset($data['end_time']['ampm']) ? $data['end_time']['ampm'] : false;
      $data['start_time'] = OsTimeHelper::convert_time_to_minutes($data['start_time']['formatted_value'], $start_ampm);
      $data['end_time'] = OsTimeHelper::convert_time_to_minutes($data['end_time']['formatted_value'], $end_ampm);
    }
		return $data;
	}

	public function after_data_was_set($data){
		$this->calculate_end_date_and_time();
	}

	public function set_buffers(){
		if($this->service_id){
			$service = new OsServiceModel($this->service_id);
			if($service){
				$this->buffer_before = $service->buffer_before;
				$this->buffer_after = $service->buffer_after;
			}
		}
	}

  public function get_total_duration($calculate_from_start_and_end = false){
		if($calculate_from_start_and_end){
			if($this->start_date == $this->end_date){
				// same day
				$total_duration = $this->end_time - $this->start_time;
			}else{
				// TODO calculate how many days difference there is, if difference is more than 1 day - account for that
				$total_duration = 60*24 - $this->start_time + $this->end_time;
			}
		}else{
			if($this->duration){
				$total_duration = $this->duration;
			}else{
	      $total_duration = $this->service->duration;
			}
	    $total_duration = apply_filters('latepoint_calculated_total_duration', $total_duration, $this);
		}
    return (int) $total_duration;
  }


  public function get_start_time_shifted_for_customer(){
    $start_time = OsTimeHelper::shift_time_by_minutes($this->start_time, $this->customer->get_timeshift_in_minutes());
    return $start_time;
  }
  public function get_end_time_shifted_for_customer(){
    $end_time = OsTimeHelper::shift_time_by_minutes($this->end_time, $this->customer->get_timeshift_in_minutes());
    return $end_time;
  }

  public function get_nice_created_at($include_time = true){
		$format = $include_time ? OsSettingsHelper::get_readable_date_format().' '.OsSettingsHelper::get_readable_time_format() : OsSettingsHelper::get_readable_date_format();
    return date_format(date_create_from_format('Y-m-d H:i:s', $this->created_at), $format);
  }


	/**
	 * @return bool
	 *
	 * Create new booking from the booking form/button on the frontend
	 */
  public function create_from_booking_form($load_customer_from_session = true): bool{

		// check if there is a booking intent connected to this booking and if it's already converted into a booking
    if(!empty($this->intent_key)){
			$converted_booking_id = OsBookingIntentHelper::get_booking_id_from_intent_key($this->intent_key);
			if($converted_booking_id){
				$this->load_by_id($converted_booking_id);
				return true;
			}
		}
		if($load_customer_from_session){
	    $customer = OsAuthHelper::get_logged_in_customer();
		}else{
			$customer = new OsCustomerModel($this->customer_id);
		}

		// agent, service and customer should be set
    if($this->service_id && $this->agent_id && $this->customer_id && $customer && $customer->id && ($this->customer_id == $customer->id)){
      $service = new OsServiceModel($this->service_id);

      if ($this->agent_id == LATEPOINT_ANY_AGENT && $this->location_id == LATEPOINT_ANY_LOCATION) {
				// both location and agent are set to any
	      $connections = new OsConnectorModel();
				$connection_groups = $connections->select(LATEPOINT_TABLE_AGENTS_SERVICES.'.agent_id, '.LATEPOINT_TABLE_AGENTS_SERVICES.'.location_id')
					->where(['service_id' => $this->service_id, LATEPOINT_TABLE_AGENTS.'.status' => LATEPOINT_AGENT_STATUS_ACTIVE, LATEPOINT_TABLE_LOCATIONS.'.status' => LATEPOINT_LOCATION_STATUS_ACTIVE])
					->join(LATEPOINT_TABLE_AGENTS, ['id' => LATEPOINT_TABLE_AGENTS_SERVICES.'.agent_id'])
					->join(LATEPOINT_TABLE_LOCATIONS, ['id' => LATEPOINT_TABLE_AGENTS_SERVICES.'.location_id'])
					->get_results(ARRAY_A);
				if(empty($connection_groups)){
					// no active locations and agents are connected to this service
		      $this->add_error('send_to_step', __('Unfortunately there are no active resources that can offer selected serivce, please select another service.', 'latepoint'), 'service');
					return false;
				}else{
					foreach($connection_groups as $connection){
						$this->location_id = $connection['location_id'];
			      $this->agent_id = OsBookingHelper::get_any_agent_for_booking_by_rule($this);
						// available agent found in this location - break the loop
						if($this->agent_id) break;
					}
					if (!$this->agent_id) {
			      $this->add_error('send_to_step', __('Unfortunately the selected time slot is not available anymore, please select another timeslot.', 'latepoint'), 'datepicker');
			      return false;
		      }
				}


      }elseif ($this->agent_id == LATEPOINT_ANY_AGENT) {
	      $this->agent_id = OsBookingHelper::get_any_agent_for_booking_by_rule($this);
	      if (!$this->agent_id) {
		      $this->add_error('send_to_step', __('Unfortunately the selected time slot is not available anymore, please select another timeslot.', 'latepoint'), 'datepicker');
		      return false;
	      }
      }elseif ($this->location_id == LATEPOINT_ANY_LOCATION) {
	      $this->location_id = OsBookingHelper::get_any_location_for_booking_by_rule($this);
	      if (!$this->location_id) {
		      $this->add_error('send_to_step', __('Unfortunately the selected time slot is not available anymore, please select another timeslot.', 'latepoint'), 'datepicker');
		      return false;
	      }
      } else {
	      // check if booking time is still available
	      if (!OsBookingHelper::is_booking_request_available(\LatePoint\Misc\BookingRequest::create_from_booking_model($this))) {
		      $error_message = __('Unfortunately the selected time slot is not available anymore, please select another timeslot.', 'latepoint');
		      $this->add_error('send_to_step', $error_message, 'datepicker');
		      return false;
	      }
      }


      $this->end_time = $this->calculate_end_time();
      $this->end_date = $this->calculate_end_date();
			$this->set_utc_datetimes();
      $this->buffer_before = $service->buffer_before;
      $this->buffer_after = $service->buffer_after;

      $this->subtotal = $this->full_amount_to_charge(false, false);
      $this->price = $this->full_amount_to_charge();
      $this->customer_comment = $customer->notes;

      // process payment if there is amount due
      if(($this->amount_to_charge() > 0) && !OsSettingsHelper::is_env_demo()){
        $transaction = OsPaymentsHelper::process_payment_for_booking($this);
				if($transaction && $transaction->status = LATEPOINT_TRANSACTION_STATUS_APPROVED){
					// update payment status of the booking
					if($this->payment_portion == LATEPOINT_PAYMENT_PORTION_FULL){
						$this->payment_status = LATEPOINT_PAYMENT_STATUS_FULLY_PAID;
					}elseif($this->payment_portion == LATEPOINT_PAYMENT_PORTION_DEPOSIT){
						$this->payment_status = LATEPOINT_PAYMENT_STATUS_PARTIALLY_PAID;
					}
				}
      }else{
        $transaction = false;
      }


			// check if there is a booking intent connected to this booking and if it's already converted into a booking
	    if(!empty($this->intent_key)){
				$converted_booking_id = OsBookingIntentHelper::get_booking_id_from_intent_key($this->intent_key);
				if($converted_booking_id){
					$this->load_by_id($converted_booking_id);
					return true;
				}
			}

      apply_filters('latepoint_before_booking_save_frontend', $this);

      if($this->get_error()){
        error_log(print_r($this->get_error_messages(), true));
        return false;
      }

      try{
				// get intent if exists to convert instantly when booking is saved
        if(!empty($this->intent_key)){
          $booking_intent = new OsBookingIntentModel();
          $booking_intent = $booking_intent->where(['intent_key' => $this->intent_key])->set_limit(1)->get_results_as_models();
        }else{
					$booking_intent = false;
        }
        // save booking
        if($this->save()){
          if($booking_intent){
						$booking_intent->converted_to_booking($this);
          }
          if($transaction){
            $transaction->booking_id = $this->id;
            if($transaction->save()){
		          do_action('latepoint_transaction_created', $transaction);
            }else{
							OsDebugHelper::log('Error creating transaction', 'transaction_error', $transaction->get_error_messages());
		          error_log(print_r($transaction->get_error_messages(), true));
		        }
          }
					// hide "payments & credits" row if we are not accepting payments
					$rows_to_hide = (!OsPaymentsHelper::is_accepting_payments()) ? ['payments'] : [];
					$this->save_meta_by_key('price_breakdown', json_encode(OsBookingHelper::generate_price_breakdown_rows($this, true, $rows_to_hide)));
          try{
            do_action('latepoint_booking_created', $this);
          }catch (Exception $e) {
            error_log($e->getMessage());
          }
          return true;
        }else{
					OsDebugHelper::log('Error creating booking', 'booking_error', $this->get_error_messages());
          error_log(print_r($this->get_error_messages(), true));
          return false;
        }
      }catch(Exception $e) {
        error_log($e->getMessage());
      }
    }else{
      if(!$this->service_id){
        $this->add_error('missing_service', __('You have to select a service', 'latepoint'));
      }
      if(!$this->agent_id){
        $this->add_error('missing_agent', __('You have to select an agent', 'latepoint'));
      }
      if(!$this->customer_id){
        $this->add_error('missing_customer', __('Customer Not Found', 'latepoint'));
      }
      if(!$customer){
        $this->add_error('missing_customer', __('You have to be logged in', 'latepoint'));
      }
			OsDebugHelper::log('Customer not logged in', 'customer_error', print_r($customer, true));
      OsDebugHelper::log('Error saving booking', 'booking_error', 'Agent: '.$this->agent_id.', Service: '.$this->service_id.', Booking Customer: '.$this->customer_id);
      return false;
    }
    return true;
  }

  public function get_nice_status(){
    return OsBookingHelper::get_nice_status_name($this->status);
  }

  public function get_nice_payment_status(){
    return OsBookingHelper::get_nice_payment_status_name($this->payment_status);
  }

  public function get_latest_bookings_sorted_by_status($args = array()){
    $args = array_merge(array('service_id' => false, 'customer_id' => false, 'agent_id' => false, 'location_id' => false, 'limit' => false, 'offset' => false), $args);

    $bookings = new OsBookingModel();
    $query_args = array();
    if($args['service_id']) $query_args['service_id'] = $args['service_id'];
    if($args['customer_id']) $query_args['customer_id'] = $args['customer_id'];
    if($args['agent_id']) $query_args['agent_id'] = $args['agent_id'];
    if($args['location_id']) $query_args['location_id'] = $args['location_id'];
    if($args['limit']) $bookings->set_limit($args['limit']);
    if($args['offset']) $bookings->set_offset($args['offset']);

    return $bookings->where($query_args)->should_not_be_cancelled()->order_by("status != '".LATEPOINT_BOOKING_STATUS_PENDING."' asc, start_date asc, start_time asc")->get_results_as_models();

  }

  
  public function should_not_be_cancelled(){
    return $this->where([$this->table_name.'.status !=' => LATEPOINT_BOOKING_STATUS_CANCELLED]);
  }

  public function should_be_cancelled(){
    return $this->where([$this->table_name.'.status' => LATEPOINT_BOOKING_STATUS_CANCELLED]);
  }

  public function should_be_approved(){
    return $this->where([$this->table_name.'.status' => LATEPOINT_BOOKING_STATUS_APPROVED]);
  }

  public function should_be_in_future(){
    return $this->where(['OR' => ['start_date >' => OsTimeHelper::today_date('Y-m-d'), 
                                                'AND' => ['start_date' => OsTimeHelper::today_date('Y-m-d'),
                                                               'start_time >' => OsTimeHelper::get_current_minutes()]]]);
  }


  public function get_upcoming_bookings($agent_id = false, $customer_id = false, $service_id = false, $location_id = false, int $limit = 3){
    $bookings = new OsBookingModel();
    $args = array('OR' => array('start_date >' => OsTimeHelper::today_date('Y-m-d'), 
                                'AND' => array('start_date' => OsTimeHelper::today_date('Y-m-d'),
                                               'start_time >' => OsTimeHelper::get_current_minutes())));
    if($service_id) $args['service_id'] = $service_id;
    if($customer_id) $args['customer_id'] = $customer_id;
    if($agent_id) $args['agent_id'] = $agent_id;
    if($location_id) $args['location_id'] = $location_id;

		$args = OsAuthHelper::get_current_user()->clean_query_args($args);

    return $bookings->should_be_approved()
      ->select('*, count(id) as total_customers, sum(total_attendies) as total_attendies_sum')
      ->group_by('start_datetime_utc, agent_id, service_id, location_id')
      ->where($args)
      ->set_limit($limit)
      ->order_by('start_datetime_utc asc')
      ->get_results_as_models();

  }

  public function get_nice_start_time_for_customer(){
    return $this->format_start_date_and_time(OsTimeHelper::get_time_format(), false, $this->customer->get_selected_timezone_obj());
  }

  public function get_nice_end_time_for_customer(){
    return $this->format_end_date_and_time(OsTimeHelper::get_time_format(), false, $this->customer->get_selected_timezone_obj());
  }

  public function get_nice_start_date_for_customer(){
    return $this->format_start_date_and_time(OsSettingsHelper::get_readable_date_format(), false, $this->customer->get_selected_timezone_obj());
  }

  public function get_nice_start_datetime_for_customer(){
    return $this->format_start_date_and_time(OsSettingsHelper::get_readable_datetime_format(), false, $this->customer->get_selected_timezone_obj());
  }

  public function get_nice_start_time(){
    return OsTimeHelper::minutes_to_hours_and_minutes($this->start_time);
  }

  public function get_nice_end_time(){
    return OsTimeHelper::minutes_to_hours_and_minutes($this->end_time);
  }

  public function get_nice_end_date($hide_year_if_current = false){
    $d = OsWpDateTime::os_createFromFormat("Y-m-d", $this->end_date);
    if(!$d) return 'n/a';
    if($hide_year_if_current && ($d->format('Y') == OsTimeHelper::today_date('Y'))){
			$format = OsSettingsHelper::get_readable_date_format(true);
    }else{
			$format = OsSettingsHelper::get_readable_date_format();
    }
    return OsUtilHelper::translate_months($d->format($format));
  }

  public function get_nice_start_date($hide_year_if_current = false){
    $d = OsWpDateTime::os_createFromFormat("Y-m-d", $this->start_date);
    if(!$d) return 'n/a';
    if($hide_year_if_current && ($d->format('Y') == OsTimeHelper::today_date('Y'))){
			$format = OsSettingsHelper::get_readable_date_format(true);
    }else{
			$format = OsSettingsHelper::get_readable_date_format();
    }
    return OsUtilHelper::translate_months($d->format($format));
  }


	/**
	 * @param $hide_if_today bool
	 * @param $hide_year_if_current bool
	 * @return string
	 */
  public function get_nice_start_datetime(bool $hide_if_today = true, bool $hide_year_if_current = true): string{
    if($hide_if_today && $this->start_date == OsTimeHelper::today_date('Y-m-d')){
      $date = __('Today', 'latepoint');
    }else{
      $date = $this->get_nice_start_date($hide_year_if_current);
    }
    return $date.', '.$this->get_nice_start_time();
  }

  public function get_nice_end_datetime($hide_if_today = true, $hide_year_if_current = true){
    if($hide_if_today && $this->end_date == OsTimeHelper::today_date('Y-m-d')){
      $date = __('Today', 'latepoint');
    }else{
      $date = $this->get_nice_end_date($hide_year_if_current);
    }
    return $date.', '.$this->get_nice_end_time();
  }


  public function format_end_date_and_time($format = "Y-m-d H:i:s", $input_timezone = false, $output_timezone = false){
    if(!$input_timezone) $input_timezone = OsTimeHelper::get_wp_timezone();
    if(!$output_timezone) $output_timezone = OsTimeHelper::get_wp_timezone();

    $date = OsWpDateTime::os_createFromFormat("Y-m-d H:i:s", $this->end_date.' '.OsTimeHelper::minutes_to_army_hours_and_minutes($this->end_time).':00', $input_timezone);
    $date->setTimeZone($output_timezone);
    return OsUtilHelper::translate_months($date->format($format));
  }

  public function format_start_date(){
    if(empty($this->start_date)){
      $date = new OsWpDateTime();
      $this->start_date = $date->format('Y-m-d');
    }else{
      $date = OsWpDateTime::os_createFromFormat("Y-m-d", $this->start_date);
    }
    return $date->format(OsSettingsHelper::get_date_format());
  }

  public function format_start_date_and_time($format = "Y-m-d H:i:s", $input_timezone = false, $output_timezone = false){
    if(!$input_timezone) $input_timezone = OsTimeHelper::get_wp_timezone();
    if(!$output_timezone) $output_timezone = OsTimeHelper::get_wp_timezone();

		if(is_null($this->start_time) || $this->start_time === ''){
			// no time set yet (could be because summary is reloaded when date is picked, before the time is picked)
	    $date = OsWpDateTime::os_createFromFormat("Y-m-d", $this->start_date);
			if($date){
	      return OsUtilHelper::translate_months($date->format(OsSettingsHelper::get_readable_date_format()));
	    }else{
	      return __('Invalid Date/Time', 'latepoint');
	    }
		}else{
			// both date & time are set, update timezone and translate
	    $date = OsWpDateTime::os_createFromFormat("Y-m-d H:i:s", $this->start_date.' '.OsTimeHelper::minutes_to_army_hours_and_minutes($this->start_time).':00', $input_timezone);
	    if($date){
	      $date->setTimeZone($output_timezone);
	      return OsUtilHelper::translate_months($date->format($format));
	    }else{
	      return __('Invalid Date/Time', 'latepoint');
	    }
		}
  }

	public function format_created_datetime_rfc3339(){
		$datetime = OsTimeHelper::date_from_db($this->created_at);
		$datetime->setTimezone(new DateTimeZone("UTC"));
		return $datetime->format(\DateTime::RFC3339);
	}

  public function format_start_date_and_time_rfc3339(){
    return $this->format_start_date_and_time(\DateTime::RFC3339);
  }

  public function format_end_date_and_time_rfc3339(){
    return $this->format_end_date_and_time(\DateTime::RFC3339);
  }

  public function format_start_date_and_time_for_google(){
    return $this->format_start_date_and_time(\DateTime::RFC3339);
  }

  public function format_end_date_and_time_for_google(){
    return $this->format_end_date_and_time(\DateTime::RFC3339);
  }

	/*
	 * Checks if the booking has passed
	 */
	public function time_status(){
		try{
	    $now_datetime = OsTimeHelper::now_datetime_utc();
			$booking_start = new OsWpDateTime($this->start_datetime_utc, new DateTimeZone('UTC'));
			$booking_end = new OsWpDateTime($this->end_datetime_utc, new DateTimeZone('UTC'));
			if(($now_datetime <= $booking_end) && ($now_datetime >= $booking_start)){
				return 'now';
			}elseif($now_datetime <= $booking_start){
				return 'upcoming';
			}else{
				return 'past';
			}
		}catch(Exception $e){
			return 'past';
    }

	}

  protected function get_time_left(){
    $now_datetime = new OsWpDateTime('now');
    $booking_datetime = OsWpDateTime::os_createFromFormat("Y-m-d H:i:s", $this->format_start_date_and_time());
    $css_class = 'left-days';

    if($booking_datetime){
      $diff = $now_datetime->diff($booking_datetime);
      if($diff->d > 0){
        $left = $diff->format('%a '.__('days', 'latepoint'));
      }else{
        if($diff->h > 0){
          $css_class = 'left-hours';
          $left = $diff->format('%h '.__('hours', 'latepoint'));
        }else{
          $css_class = 'left-minutes';
          $left = $diff->format('%i '.__('minutes', 'latepoint'));
        }
      }
    }else{
      $left = 'n/a';
    }

    return '<span class="time-left '.$css_class.'">'.$left.'</span>';
  }


  protected function get_agent(){
    if($this->agent_id){
      if(!isset($this->agent) || (isset($this->agent) && ($this->agent->id != $this->agent_id))){
        $this->agent = new OsAgentModel($this->agent_id);
      }
    }else{
      $this->agent = new OsAgentModel();
    }
    return $this->agent;
  }

  public function get_agent_full_name(){
    if($this->agent_id == LATEPOINT_ANY_AGENT){
      return __('Any Available Agent', 'latepoint');
    }else{
      return $this->agent->full_name;
    }
  }


  public function get_location(){
    if($this->location_id){
			// if location has not been initialized yet, or location_id is different from the one initialized - init again
      if(empty($this->location) || ($this->location->id != $this->location_id)){
        $this->location = new OsLocationModel($this->location_id);
      }
    }else{
      $this->location = new OsLocationModel();
    }
    return $this->location;
  }

  protected function get_customer(){
    if($this->customer_id){
      if(!isset($this->customer) || (isset($this->customer) && ($this->customer->id != $this->customer_id))){
        $this->customer = new OsCustomerModel($this->customer_id);
      }
    }else{
      $this->customer = new OsCustomerModel();
    }
    return $this->customer;
  }

  protected function get_transactions(){
    $transactions_model = new OsTransactionModel();
    $transactions = $transactions_model->where(['booking_id' => $this->id])->get_results_as_models();
		$this->transactions = $transactions;
    return $this->transactions;
  }


  protected function get_service(){
    if($this->service_id){
      if(!isset($this->service) || (isset($this->service) && ($this->service->id != $this->service_id))){
        $this->service = new OsServiceModel($this->service_id);
      }
    }else{
      $this->service = new OsServiceModel();
    }
    return $this->service;
  }

  public function get_start_datetime_object(DateTimeZone $timezone = null){
		if(empty($timezone)) $timezone = OsTimeHelper::get_wp_timezone();
		if(empty($this->start_datetime_utc)){
			// fix data, probably an older booking from the time when we didn't store UTC date
			$this->start_datetime_utc = $this->generate_start_datetime_in_db_format();
		}
		$booking_start_datetime = OsTimeHelper::date_from_db($this->start_datetime_utc, false, new DateTimeZone('UTC'));
		if($booking_start_datetime){
			$booking_start_datetime->setTimezone($timezone);
		}else{
			OsDebugHelper::log('Error generating start date and time for booking ID: '.$this->id, 'corrupt_booking_data');
		}
    return $booking_start_datetime;
  }

  public function get_end_datetime_object(DateTimeZone $timezone = null){
		if(empty($timezone)) $timezone = OsTimeHelper::get_wp_timezone();
		if(empty($this->end_datetime_utc)){
			// fix data, probably an older booking from the time when we didn't store UTC date
			$this->end_datetime_utc = $this->generate_end_datetime_in_db_format();
		}
		$booking_end_datetime = OsTimeHelper::date_from_db($this->end_datetime_utc, false, new DateTimeZone('UTC'));
		if($booking_end_datetime){
			$booking_end_datetime->setTimezone($timezone);
		}else{
			OsDebugHelper::log('Error generating end date and time for booking ID: '.$this->id, 'corrupt_booking_data');
		}
    return $booking_end_datetime;
  }


  public function generate_start_datetime_in_db_format(string $timezone = 'UTC'):string{
		// start_time and start_date is legacy stored in wordpress timezone
    $dateTime = new OsWpDateTime($this->start_date . ' 00:00:00', OsTimeHelper::get_wp_timezone());
    $dateTime->modify('+'. $this->start_time .' minutes');
		$dateTime->setTimezone(new DateTimeZone($timezone));

    return $dateTime->format('Y-m-d H:i:s');
  }


  public function generate_end_datetime_in_db_format(string $timezone = 'UTC'):string{
		// end_time and end_date is legacy stored in wordpress timezone
    $dateTime = new OsWpDateTime($this->end_date . ' 00:00:00', OsTimeHelper::get_wp_timezone());
    $dateTime->modify('+'. $this->end_time .' minutes');
		$dateTime->setTimezone(new DateTimeZone($timezone));

    return $dateTime->format('Y-m-d H:i:s');
  }


  protected function before_save(){
		// TODO check for uniqueness
    if(empty($this->booking_code)) $this->booking_code = strtoupper(OsUtilHelper::random_text('distinct', 7));
    if(empty($this->end_date)) $this->end_date = $this->calculate_end_date();
    if(empty($this->payment_method)) $this->payment_method = OsPaymentsHelper::get_default_payment_method();
    if(empty($this->source_url)) $this->source_url = $_SERVER['HTTP_REFERER'];
    if(empty($this->status)) $this->status = $this->get_default_booking_status();
    if(empty($this->ip_address)) $this->ip_address = $_SERVER['REMOTE_ADDR'];
    if(empty($this->total_attendies)) $this->total_attendies = 1;
		if(empty($this->duration) && $this->service_id){
			$service = new OsServiceModel($this->service_id);
			$this->duration = $service->duration;
		}
  }

  public function get_default_booking_status(){
    return OsBookingHelper::get_default_booking_status($this->service_id);
  }

  public function update_status($new_status){
		if($new_status == $this->status){
			return true;
		}else{
			if(!in_array($new_status, array_keys(OsBookingHelper::get_statuses_list()))){
				$this->add_error('invalid_booking_status', 'Invalid booking status');
				return false;
			}
			$old_booking = clone $this;
	    $this->status = $new_status;
	    $result = $this->update_attributes(['status' => $new_status]);
			if($result){
			  do_action('latepoint_booking_updated', $this, $old_booking);
				return true;
			}else{
				return false;
			}
		}
  }

  public function save_avatar($image_id = false){
    if((false === $image_id) && $this->image_id) $image_id = $this->image_id;
    if($image_id && $this->post_id){
      set_post_thumbnail($this->post_id, $image_id);
      $this->image_id = $image_id;
    }
    return $this->image_id;
  }
  
  public function can_pay_deposit_and_pay_full(){
    return (OsPaymentsHelper::is_accepting_payments() && ($this->full_amount_to_charge() > 0) && ($this->deposit_amount_to_charge() > 0));
  }

  public function can_pay_deposit(){
    return ($this->deposit_amount_to_charge() > 0);
  }

  public function can_pay_full(){
    return ($this->full_amount_to_charge() > 0);
  }

  public function specs_calculate_price_to_charge($payment_method = false){
    if($this->payment_portion == LATEPOINT_PAYMENT_PORTION_DEPOSIT){
      return $this->specs_calculate_deposit_price_to_charge($payment_method = false);
    }else{
      return $this->specs_calculate_full_price_to_charge($payment_method = false);
    }
  }

  public function amount_to_charge(){
    if($this->payment_portion == LATEPOINT_PAYMENT_PORTION_DEPOSIT){
      return $this->deposit_amount_to_charge();
    }else{
      return $this->full_amount_to_charge();
    }
  }

	/**
	 * @param $apply_coupons
	 * @param $apply_taxes
	 * @return mixed|void
	 *
	 * Returns full amount to charge in database format 1999.0000
	 *
	 */
  public function full_amount_to_charge($apply_coupons = true, $apply_taxes = true){
    return OsMoneyHelper::calculate_full_amount_to_charge($this, $apply_coupons, $apply_taxes);
  }

	/**
	 * @param $apply_coupons
	 * @return mixed|void
	 *
	 * Returns deposit amount to charge in database format 1999.0000
	 *
	 */
  public function deposit_amount_to_charge($apply_coupons = true){
    return OsMoneyHelper::calculate_deposit_amount_to_charge($this, $apply_coupons);
  }


  public function specs_calculate_full_price_to_charge($payment_method = false){
    if(!$payment_method) $payment_method = $this->payment_method;
    return OsPaymentsHelper::convert_charge_amount_to_requirements($this->full_amount_to_charge(), $payment_method);
  }

  public function specs_calculate_deposit_price_to_charge($payment_method = false){
    if(!$payment_method) $payment_method = $this->payment_method;
    return OsPaymentsHelper::convert_charge_amount_to_requirements($this->deposit_amount_to_charge(), $payment_method);
  }


	/**
	 * @param $apply_coupons bool
	 * @param $apply_taxes bool
	 * @return string
	 *
	 * calculates the price and formats it to display
	 */
  public function formatted_full_price(bool $apply_coupons = true, bool $apply_taxes = true): string{
    return OsMoneyHelper::format_price($this->full_amount_to_charge($apply_coupons, $apply_taxes));
  }

	// calculates the deposit and formats it to display
  public function formatted_deposit_price($apply_coupons = true){
    return OsMoneyHelper::format_price($this->deposit_amount_to_charge($apply_coupons), true, false);
  }


  protected function allowed_params($role = 'admin'){
    $allowed_params = array('service_id',
                            'booking_code',
                            'agent_id',
                            'customer_id',
                            'location_id',
                            'start_date',
                            'end_date',
                            'start_time',
                            'end_time',
														'start_datetime_utc',
														'end_datetime_utc',
                            'payment_method',
                            'payment_portion',
                            'payment_token',
                            'intent_key',
                            'ip_address',
                            'source_id',
                            'source_url',
                            'buffer_before',
                            'duration',
                            'buffer_after',
                            'coupon_code',
                            'coupon_discount',
                            'total_attendies',
                            'customer_comment',
                            'total_attendies_sum',
                            'total_customers',
                            'status',
	    'payment_status');
    return $allowed_params;
  }


  protected function params_to_save($role = 'admin'){
    $params_to_save = array('service_id',
														'booking_code',
                            'agent_id',
                            'customer_id',
                            'location_id',
                            'start_date',
                            'end_date',
                            'start_time',
                            'end_time',
														'start_datetime_utc',
														'end_datetime_utc',
                            'payment_method',
                            'duration',
                            'subtotal',
                            'ip_address',
                            'source_id',
                            'source_url',
                            'price',
                            'payment_portion',
                            'buffer_before',
                            'buffer_after',
                            'coupon_code',
                            'coupon_discount',
                            'total_attendies',
                            'customer_comment',
                            'status',
	    'payment_status');
    return $params_to_save;
  }


  protected function properties_to_validate(){
    $validations = array(
      'service_id' => array('presence'),
      'agent_id' => array('presence'),
      'location_id' => array('presence'),
      'customer_id' => array('presence'),
      'start_date' => array('presence'),
      'end_date' => array('presence'),
      'status' => array('presence'),
    );
    return $validations;
  }
}