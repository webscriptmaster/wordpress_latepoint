<?php

class OsBookingIntentHelper {

	public static function generate_continue_intent_url($booking_intent_key) {
		return OsRouterHelper::build_admin_post_link(['bookings', 'continue_booking_intent'], ['booking_intent_key' => $booking_intent_key]);
	}

	public static function get_booking_id_from_intent_key($intent_key){
		if(empty($intent_key)) return false;
		$booking_intent = new OsBookingIntentModel();
		$booking_intent = $booking_intent->where(['intent_key' => $intent_key])->set_limit(1)->get_results_as_models();

		if($booking_intent && $booking_intent->booking_id) {
			return $booking_intent->booking_id;
		}else {
			return null;
		}
	}

	public static function create_or_update_booking_intent($booking_data = [], $restrictions_data = [], $payment_data = [], $booking_form_page_url = '') {
		$booking_intent = new OsBookingIntentModel();
		if (isset($booking_data['intent_key']) && !empty($booking_data['intent_key'])) {
			$booking_intent = $booking_intent->get_by_intent_key($booking_data['intent_key']);
			if (!$booking_intent) $booking_intent = new OsBookingIntentModel();
		}
		$is_new = $booking_intent->is_new_record();

		if(!$is_new) $old_booking_intent = clone $booking_intent;

		// set customer id from session, do not trust submitted data
		$booking_data['customer_id'] = OsAuthHelper::get_logged_in_customer_id();

		$booking_data = apply_filters('latepoint_booking_data_for_booking_intent', $booking_data);

		$booking_intent->booking_data = json_encode($booking_data);
		$booking_intent->restrictions_data = json_encode($restrictions_data);
		$booking_intent->payment_data = json_encode($payment_data);
		$booking_intent->booking_form_page_url = urldecode($booking_form_page_url);
		$booking_intent->customer_id = OsAuthHelper::get_logged_in_customer_id();

		if($booking_intent->save()){
			if($is_new){
				/**
				 * Booking intent is created
				 *
				 * @since 4.7.0
				 * @hook latepoint_booking_intent_created
				 *
				 * @param {OsBookingIntentModel} $booking_intent Instance of booking intent model that was created
				 */
				do_action('latepoint_booking_intent_created', $booking_intent);
			}else{
				/**
				 * Booking intent is updated
				 *
				 * @since 4.7.0
				 * @hook latepoint_booking_intent_updated
				 *
				 * @param {OsBookingIntentModel} $booking_intent Updated instance of booking intent model
				 * @param {OsBookingIntentModel} $old_booking_intent Instance of booking intent model before it was updated
				 */
				do_action('latepoint_booking_intent_updated', $booking_intent, $old_booking_intent);
			}
		}else{
			$action_type = $is_new ? 'creating' : 'updating';
			OsDebugHelper::log('Error '.$action_type.' booking intent', 'error_saving_booking_intent', $booking_intent->get_error_messages());
		}

		return $booking_intent;
	}

	public static function get_booking_intent_by_intent_key($intent_key){
		if(empty($intent_key)) return false;
		$booking_intent = new OsBookingIntentModel();
		$booking_intent = $booking_intent->where(['intent_key' => $intent_key])->set_limit(1)->get_results_as_models();
		return $booking_intent;
	}

	public static function convert_intent_to_booking($intent_key) {
		$booking_intent = self::get_booking_intent_by_intent_key($intent_key);
		if ($booking_intent) {
			if (!$booking_intent->is_converted()) {
				$booking_data = json_decode($booking_intent->booking_data, true);
				OsStepsHelper::set_booking_object($booking_data);
				// by default a customer ID is set from session, we need to set it from booking intent data instead
				OsStepsHelper::$booking_object->customer_id = $booking_data['customer_id'];
				OsStepsHelper::$booking_object->intent_key = $booking_intent->intent_key;
				OsStepsHelper::set_restrictions(json_decode($booking_intent->restrictions_data, true));
				if (!OsStepsHelper::$booking_object->create_from_booking_form(false)) {
					// ERROR SAVING BOOKING
					OsDebugHelper::log('Error converting booking intent to booking', 'booking_intent_error', ['intent_key' => $intent_key, 'booking_errors' => OsStepsHelper::$booking_object->get_error_messages()]);
					return false;
				} else {
					$booking_intent->converted_to_booking(OsStepsHelper::$booking_object);
					return $booking_intent->booking_id;
				}
			} else {
				// has already converted to a booking
				return $booking_intent;
			}
		} else {
			return false;
		}
	}

}