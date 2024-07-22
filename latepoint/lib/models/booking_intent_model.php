<?php

class OsBookingIntentModel extends OsModel{
  var $id,
      $intent_key,
      $customer_id,
      $booking_form_page_url,
      $booking_data,
      $restrictions_data,
      $payment_data,
      $booking_id,
      $updated_at,
      $created_at;

  function __construct($id = false){
    parent::__construct();
    $this->table_name = LATEPOINT_TABLE_BOOKING_INTENTS;

    if($id){
      $this->load_by_id($id);
    }
  }

  public function get_by_intent_key($intent_key){
    return $this->where(['intent_key' => $intent_key])->set_limit(1)->get_results_as_models();
  }

	public function converted_to_booking(OsBookingModel $booking){
		if(empty($booking->id)) return false;

		$this->update_attributes(['booking_id' => $booking->id]);
		/**
		 * Booking intent is converted to booking
		 *
		 * @since 4.7.0
		 * @hook latepoint_booking_intent_converted
		 *
		 * @param {OsBookingIntentModel} $booking_intent Instance of booking intent model that has been converted to booking
		 * @param {OsBookingModel} $booking Instance of booking model that booking intent was converted to
		 */
		do_action('latepoint_booking_intent_converted', $this, $booking);
	}

	// Determines if booking intent has been converted into a booking already
	public function is_converted(){
		if(empty($booking_intent->booking_id)){
			return false;
		}else{
			return true;
		}
	}

	public function generate_data_vars(): array {
		$vars = [
			'id' => $this->id,
			'intent_key' => $this->intent_key,
			'customer_id' => $this->customer_id,
			'booking_form_page_url' => $this->booking_form_page_url,
			'booking_data' => json_decode($this->booking_data, true),
			'restrictions_data' => json_decode($this->restrictions_data, true),
			'payment_data' => json_decode($this->payment_data, true),
			'booking_id' => $this->booking_id,
			'updated_at' => $this->updated_at,
			'created_at' => $this->created_at,
		];
		return $vars;
	}

  public function get_page_url_with_intent(){
    $booking_page_url = $this->booking_form_page_url;
    $existing_var_position = strpos($booking_page_url, 'latepoint_booking_intent_key=');
    if($existing_var_position === false){
      // no intent variable in url
      $question_position = strpos($booking_page_url, '?');
      if($question_position === false){
        // no ?query params
        $hash_position = strpos($booking_page_url, '#');
        if($hash_position === false){
          // no hashtag in url
          $booking_page_url = $booking_page_url.'?latepoint_booking_intent_key='.$this->intent_key;
        }else{
          // hashtag in url and no ?query, prepend the hashtag with query
          $booking_page_url = substr_replace($booking_page_url, '?latepoint_booking_intent_key='.$this->intent_key.'#', $hash_position, 1);
        }
      }else{
        // ?query string exists, add intent key to it
        $booking_page_url = substr_replace($booking_page_url, '?latepoint_booking_intent_key='.$this->intent_key.'&', $question_position, 1);
      }
    }else{
      // intent key variable exist in url
      preg_match('/latepoint_booking_intent_key=([\d,\w]*)/', $booking_page_url, $matches);
      if(isset($matches[1])){
        $booking_page_url = str_replace('latepoint_booking_intent_key='.$matches[1], 'latepoint_booking_intent_key='.$this->intent_key, $booking_page_url);
      }
    }
    return $booking_page_url;
  }


  protected function before_create(){
    if(empty($this->intent_key)) $this->intent_key = bin2hex(openssl_random_pseudo_bytes(10));
  }

  protected function allowed_params($role = 'admin'){
    $allowed_params = array('customer_id',
                            'booking_data',
                            'restrictions_data',
                            'payment_data',
                            'booking_form_page_url',
                            'intent_key',
                            'booking_id');
    return $allowed_params;
  }


  protected function params_to_save($role = 'admin'){
    $params_to_save = array('customer_id',
                            'booking_data',
                            'restrictions_data',
                            'payment_data',
                            'booking_form_page_url',
                            'intent_key',
                            'booking_id');
    return $params_to_save;
  }


  protected function properties_to_validate(){
    $validations = array(
      'customer_id' => array('presence'),
    );
    return $validations;
  }
}