<?php 

class OsReplacerHelper {

	/**
	 * @param array $data_objects
	 * @param array $other_vars
	 * @return array
	 */
	public static function generate_replacement_vars_from_data_objects(array $data_objects, array $other_vars = []): array{
		$vars = [];
		foreach($data_objects as $data_object){
			switch($data_object['model']){
				case 'old_booking':
					$old_booking = $data_object['model_ready'] ?? new OsBookingModel($data_object['id']);
					$temp_vars = self::generate_replacement_vars_from_booking($old_booking);
					foreach($temp_vars as $key => $data){
						$vars['old_'.$key] = $data;
					}
					break;
				case 'booking':
					$booking = $data_object['model_ready'] ?? new OsBookingModel($data_object['id']);
					$vars = array_merge($vars, self::generate_replacement_vars_from_booking($booking));
					break;
				case 'agent':
					$agent = $data_object['model_ready'] ?? new OsAgentModel($data_object['id']);
					$vars = array_merge($vars, self::generate_replacement_vars_from_agent($agent));
					break;
				case 'customer':
					$customer = $data_object['model_ready'] ?? new OsCustomerModel($data_object['id']);
					$vars = array_merge($vars, self::generate_replacement_vars_from_customer($customer));
					break;
				case 'transaction':
					$transaction = $data_object['model_ready'] ?? new OsTransactionModel($data_object['id']);
					$vars = array_merge($vars, self::generate_replacement_vars_from_transaction($transaction));
					break;
			}
		}
		if(!empty($other_vars)) $vars = array_merge($vars, $other_vars);

		/**
		 * Returns an array of replacement variables, based on supplied data objects
		 *
		 * @since 4.7.0
		 * @hook latepoint_prepare_replacement_vars_from_data_objects
		 *
		 * @param {array} $vars Current array of replacement variables
		 * @param {array} $data_objects Array of data objects to generate replacement variables for
		 * @param {array} $other_vars Array of additional (pre-prepared) replacement variables
		 *
		 * @returns {array} Filtered array of replacement variables
		 */
		return apply_filters('latepoint_prepare_replacement_vars_from_data_objects', $vars, $data_objects, $other_vars);
	}

	/**
	 *
	 * Prepares an array of variables to be used in replacer method from a customer object
	 *
	 * @param OsCustomerModel $customer
	 * @param array $other_vars
	 *
	 * @return array
	 */
	public static function generate_replacement_vars_from_customer(OsCustomerModel $customer, array $other_vars = []): array{
		$vars = [];
		$vars['customer'] = $customer;
		if(!empty($other_vars)) $vars = array_merge($vars, $other_vars);

		/**
		 * Returns an array of replacement variables, based on supplied <code>OsCustomerModel</code> instance
		 *
		 * @since 4.7.0
		 * @hook latepoint_prepare_replacement_vars_from_customer
		 *
		 * @param {array} $vars Current array of replacement variables
		 * @param {OsCustomerModel} $customer Instance of <code>OsCustomerModel</code> to generate replacement variables for
		 * @param {array} $other_vars Array of additional (pre-prepared) replacement variables
		 *
		 * @returns {array} Filtered array of replacement variables
		 */
		return apply_filters('latepoint_prepare_replacement_vars_from_customer', $vars, $customer, $other_vars);
	}


	/**
	 *
	 * Prepares an array of variables to be used in replacer method from a transaction object
	 *
	 * @param OsTransactionModel $transaction
	 * @param array $other_vars
	 *
	 * @return array
	 */
	public static function generate_replacement_vars_from_transaction(OsTransactionModel $transaction, array $other_vars = []): array{
		$vars = [];
		$vars['transaction'] = $transaction;
		$vars['booking'] = $transaction->booking;
		$vars['customer'] = $transaction->booking->customer;
		$vars['agent'] = $transaction->booking->agent;
		if(!empty($other_vars)) $vars = array_merge($vars, $other_vars);
		return apply_filters('latepoint_prepare_replacement_vars_from_transaction', $vars, $transaction, $other_vars);
	}


	/**
	 *
	 * Prepares an array of variables to be used in replacer method from an agent object
	 *
	 * @param OsAgentModel $agent
	 * @param array $other_vars
	 *
	 * @return array
	 */
	public static function generate_replacement_vars_from_agent(OsAgentModel $agent, array $other_vars = []): array{
		$vars = [];
		$vars['agent'] = $agent;
		if(!empty($other_vars)) $vars = array_merge($vars, $other_vars);

		/**
		 * Returns an array of replacement variables, based on supplied <code>OsAgentModel</code> instance
		 *
		 * @since 4.7.0
		 * @hook latepoint_prepare_replacement_vars_from_agent
		 *
		 * @param {array} $vars Current array of replacement variables
		 * @param {OsAgentModel} $agent Instance of <code>OsAgentModel</code> to generate replacement variables for
		 * @param {array} $other_vars Array of additional (pre-prepared) replacement variables
		 *
		 * @returns {array} Filtered array of replacement variables
		 */
		return apply_filters('latepoint_prepare_replacement_vars_from_agent', $vars, $agent, $other_vars);
	}

	/**
	 *
	 * Prepares an array of variables to be used in replacer method from a booking object
	 *
	 * @param OsBookingModel $booking
	 * @param array $other_vars
	 *
	 * @return array
	 */
	public static function generate_replacement_vars_from_booking(OsBookingModel $booking, array $other_vars = []): array{
		$vars = [];
		$vars['booking'] = $booking;
		$vars['customer'] = $booking->customer;
		$vars['agent'] = $booking->agent;
		if(!empty($other_vars)) $vars = array_merge($vars, $other_vars);

		/**
		 * Returns an array of replacement variables, based on supplied <code>OsBookingModel</code> instance
		 *
		 * @since 4.7.0
		 * @hook latepoint_prepare_replacement_vars_from_booking
		 *
		 * @param {array} $vars Current array of replacement variables
		 * @param {OsBookingModel} $customer Instance of <code>OsBookingModel</code> to generate replacement variables for
		 * @param {array} $other_vars Array of additional (pre-prepared) replacement variables
		 *
		 * @returns {array} Filtered array of replacement variables
		 */
		return apply_filters('latepoint_prepare_replacement_vars_from_booking', $vars, $booking, $other_vars);
	}

	public static function stylize_vars($html){
		$html = self::replace_business_vars($html);
		$html = str_replace('{{', '<span class="os-template-var">{{', $html);
		$html = str_replace('}}', '}}</span>', $html);
		// fix issue when the template variable is inside of an href="{{var}}", we don't want to replace those
		$html = str_replace('"<span class="os-template-var">{{', '"{{', $html);
		$html = str_replace('}}</span>"', '}}"', $html);
		return $html;
	}

  public static function replace_customer_vars($text, $customer){
  	$needles = array('{{customer_full_name}}','{{customer_first_name}}', '{{customer_last_name}}', '{{customer_email}}','{{customer_phone}}', '{{customer_notes}}');
  	$replacements = array($customer->full_name, $customer->first_name, $customer->last_name, $customer->email, $customer->phone, $customer->notes);
    $original_text = $text;
  	$text = str_replace($needles, $replacements, $text);

    /**
    * Returns a string with variables replaced, based on supplied <code>OsCustomerModel</code> instance
    *
    * @since 4.3.2
    * @hook latepoint_replace_customer_vars
    *
    * @param {string} $text Current string with variables replaced
    * @param {OsCustomerModel} $customer Instance of <code>OsCustomerModel</code> to replace variables for
    * @param {string} $original_text Original string before any customer-related replacements were performed
    * @param {array} $needles Array of default needles to be replaced
    * @param {array} $replacements Array of default replacement values to supplant each needle
    *
    * @returns {string} Filtered string with variables replaced
	*/
	return apply_filters('latepoint_replace_customer_vars', $text, $customer, $original_text, $needles, $replacements);
  }

  public static function replace_transaction_vars($text, $transaction){
  	$needles = [
			'{{transaction_token}}',
		  '{{transaction_amount}}',
		  '{{transaction_processor}}',
		  '{{transaction_payment_method}}',
		  '{{transaction_funds_status}}',
		  '{{transaction_status}}',
		  '{{transaction_notes}}',
		  '{{transaction_payment_portion}}'
	  ];
  	$replacements = [
			$transaction->token,
		  $transaction->amount,
		  $transaction->processor,
		  $transaction->payment_method,
		  $transaction->funds_status,
		  $transaction->status,
		  $transaction->notes,
		  $transaction->payment_portion
	  ];
  	$text = str_replace($needles, $replacements, $text);
    $text = apply_filters('latepoint_replace_transaction_vars', $text, $transaction);
  	return $text;
  }

  public static function replace_agent_vars($text, $agent){
  	$needles = array('{{agent_first_name}}', '{{agent_last_name}}', '{{agent_full_name}}', '{{agent_display_name}}', '{{agent_email}}','{{agent_phone}}', '{{agent_additional_emails}}', '{{agent_additional_phones}}');
  	$replacements = array($agent->first_name, $agent->last_name, $agent->full_name, $agent->display_name, $agent->email, $agent->phone, $agent->extra_emails, $agent->extra_phones);
		$original_text = $text;
  	$text = str_replace($needles, $replacements, $text);

    /**
    * Returns a string with variables replaced, based on supplied <code>OsAgentModel</code> instance
    *
    * @since 4.7.0
    * @hook latepoint_replace_agent_vars
    *
    * @param {string} $text Current string with variables replaced
    * @param {OsAgentModel} $agent Instance of <code>OsAgentModel</code> to replace variables for
    * @param {string} $original_text Original string before any agent-related replacements were performed
    * @param {array} $needles Array of default needles to be replaced
    * @param {array} $replacements Array of default replacement values to supplant each needle
    *
    * @returns {string} Filtered string with variables replaced
	*/
	return apply_filters('latepoint_replace_agent_vars', $text, $agent, $original_text, $needles, $replacements);
  }

  public static function replace_business_vars($text){
    $needles      = [
			'{{business_logo_image}}',
      '{{business_logo_url}}',
      '{{business_address}}',
      '{{business_phone}}',
      '{{business_name}}',
      '{{customer_dashboard_url}}',
    ];
    $replacements = [
			OsSettingsHelper::get_business_logo_image(),
      OsSettingsHelper::get_business_logo_url(),
      OsSettingsHelper::get_settings_value('business_address', ''),
      OsSettingsHelper::get_settings_value('business_phone', ''),
      OsSettingsHelper::get_settings_value('business_name', ''),
      OsSettingsHelper::get_customer_dashboard_url()
    ];
	$original_text = $text;
    $text         = str_replace($needles, $replacements, $text);

    /**
    * Returns a string with business-related variables replaced
    *
    * @since 4.7.0
    * @hook latepoint_replace_business_vars
    *
    * @param {string} $text Current string with variables replaced
    * @param {string} $original_text Original string before any business-related replacements were performed
    * @param {array} $needles Array of default needles to be replaced
    * @param {array} $replacements Array of default replacement values to supplant each needle
    *
    * @returns {string} Filtered string with variables replaced
	*/
	return apply_filters('latepoint_replace_business_vars', $text, $original_text, $needles, $replacements);
  }

  public static function replace_tracking_vars($text, $booking){
    $needles = ['{{booking_id}}',
                '{{agent_id}}',
                '{{customer_id}}',
                '{{total_price}}',
                '{{service_id}}'];
    $replacements = [$booking->id,
                    $booking->agent_id,
                    $booking->customer_id,
                    $booking->price,
                    $booking->service_id];
    $original_text = $text;
  	$text = str_replace($needles, $replacements, $text);

    /**
    * Returns a string with tracking-related variables replaced, based on supplied <code>OsBookingModel</code> instance
    *
    * @since 3.0.4
    * @hook latepoint_replace_tracking_vars
    *
    * @param {string} $text Current string with variables replaced
    * @param {OsBookingModel} $booking Instance of <code>OsBookingModel</code> to replace variables for
    * @param {string} $original_text Original string before any tracking-related replacements were performed
    * @param {array} $needles Array of default needles to be replaced
    * @param {array} $replacements Array of default replacement values to supplant each needle
    *
    * @returns {string} Filtered string with variables replaced
	*/
	return apply_filters('latepoint_replace_tracking_vars', $text, $booking, $original_text, $needles, $replacements);
  }

  public static function replace_booking_vars($text, OsBookingModel $booking){
  	$needles = ['{{booking_id}}',
                '{{booking_code}}',
                '{{service_name}}',
                '{{service_category}}',
                '{{start_date}}',
                '{{start_time}}',
                '{{end_time}}',
                '{{booking_status}}',
                '{{location_name}}',
                '{{location_full_address}}',
                '{{booking_duration}}',
                '{{booking_price}}',
                '{{booking_payment_status}}',
                '{{booking_payment_portion}}',
                '{{booking_payment_method}}',
                '{{booking_payment_amount}}',
							  '{{manage_booking_url_agent}}',
							  '{{manage_booking_url_customer}}'];
    $total_duration = ($booking->get_total_duration() > 0) ? $booking->get_total_duration().' '.__('minutes', 'latepoint') : __('n/a', 'latepoint');
  	$replacements = [$booking->id,
                      $booking->booking_code,
                      $booking->service->name,
                      $booking->service->category_name,
                      $booking->format_start_date_and_time(OsSettingsHelper::get_readable_date_format(), false), 
                      $booking->nice_start_time, 
                      $booking->nice_end_time, 
                      $booking->nice_status,
                      $booking->location->name,
                      $booking->location->full_address, 
                      $total_duration, 
                      OsMoneyHelper::format_price($booking->price), 
                      $booking->get_payment_status_nice_name(),
                      $booking->get_payment_portion_nice_name(),
                      $booking->get_payment_method_nice_name(),
                      OsMoneyHelper::format_price($booking->get_total_amount_paid_from_transactions()),
		                  OsBookingHelper::generate_direct_manage_booking_url($booking, 'agent'),
		                  OsBookingHelper::generate_direct_manage_booking_url($booking, 'customer'),
		  ];
    $original_text = $text;
  	$text = str_replace($needles, $replacements, $text);

    /**
    * Returns a string with variables replaced, based on supplied <code>OsBookingModel</code> instance
    *
    * @since 3.0.4
    * @hook latepoint_replace_booking_vars
    *
    * @param {string} $text Current string with variables replaced
    * @param {OsBookingModel} $booking Instance of <code>OsBookingModel</code> to replace variables for
    * @param {string} $original_text Original string before any booking-related replacements were performed
    * @param {array} $needles Array of default needles to be replaced
    * @param {array} $replacements Array of default replacement values to supplant each needle
    *
    * @returns {string} Filtered string with variables replaced
	*/
	return apply_filters('latepoint_replace_booking_vars', $text, $booking, $original_text, $needles, $replacements);
  }

  public static function replace_other_vars($text, $other_vars){
    if(isset($other_vars['old_status'])){
      $text = str_replace('{{booking_old_status}}', $other_vars['old_status'], $text);
    }
    if(isset($other_vars['token'])){
      $text = str_replace('{{token}}', $other_vars['token'], $text);
    }
    return $text;
  }

  public static function replace_all_vars($text, $vars){
    $original_text = $text;
  	if(isset($vars['booking'])) $text = self::replace_booking_vars($text, $vars['booking']);
  	if(isset($vars['customer'])) $text = self::replace_customer_vars($text, $vars['customer']);
    if(isset($vars['agent'])) $text = self::replace_agent_vars($text, $vars['agent']);
    if(isset($vars['transaction'])) $text = self::replace_transaction_vars($text, $vars['transaction']);
  	if(isset($vars['other_vars'])) $text = self::replace_other_vars($text, $vars['other_vars']);
	$text = self::replace_business_vars($text);

	/**
    * Returns a string with needles replaced, based on supplied variables
    *
    * @since 4.3.2
    * @hook latepoint_replace_all_vars_in_template
    *
    * @param {string} $text Current string with variables replaced
    * @param {array} $vars Array of variables to perform replacements for
    * @param {string} $original_text Original string before any replacements were performed
    *
    * @returns {string} Filtered string with variables replaced
	*/
    return apply_filters('latepoint_replace_all_vars_in_template', $text, $vars, $original_text);
  }
}