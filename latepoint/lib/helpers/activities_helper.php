<?php

class OsActivitiesHelper {
	public static function create_activity($atts = array()) {
		$activity = new OsActivityModel();
		if (isset($atts['booking'])) {
			$atts['booking_id'] = $atts['booking']->id;
			$atts['agent_id'] = $atts['booking']->agent_id;
			$atts['service_id'] = $atts['booking']->service_id;
			$atts['customer_id'] = $atts['booking']->customer_id;
		}
		$atts['initiated_by'] = OsAuthHelper::get_highest_current_user_type();
		$atts['initiated_by_id'] = OsAuthHelper::get_highest_current_user_id();

		if ($atts['code'] == 'booking_change_status') {
			$atts['description'] = sprintf(__('Appointment status changed from %s to %s', 'latepoint'), $atts['old_value'], $atts['booking']->status);
		}

		$activity = $activity->set_data($atts);
		$activity->save();
		return $activity;
	}

	public static function get_codes() {
		$codes = [
			'customer_created' => __('New Customer Registration', 'latepoint'),
			'customer_updated' => __('Customer Profile Update', 'latepoint'),
			'booking_created' => __('New Appointment', 'latepoint'),
			'booking_change_status' => __('Appointment Status Changed', 'latepoint'),
			'booking_updated' => __('Appointment Edited', 'latepoint'),
			'agent_created' => __('New Agent', 'latepoint'),
			'agent_updated' => __('Agent Profile Update', 'latepoint'),
			'coupon_created' => __('New Coupon', 'latepoint'),
			'coupon_updated' => __('Coupon Update', 'latepoint'),
			'service_updated' => __('Service Updated', 'latepoint'),
			'service_created' => __('Service Created', 'latepoint'),
			'location_updated' => __('Location Updated', 'latepoint'),
			'location_created' => __('Location Created', 'latepoint'),
			'sms_sent' => __('SMS Sent', 'latepoint'),
			'email_sent' => __('Email Sent', 'latepoint'),
			'process_job_run' => __('Process Job Run', 'latepoint'),
			'booking_intent_converted' => __('Booking Intent Converted', 'latepoint'),
			'booking_intent_created' => __('Booking Intent Created', 'latepoint'),
			'booking_intent_updated' => __('Booking Intent Updated', 'latepoint'),
			'error' => __('Error', 'latepoint'),
		];
		return apply_filters('latepoint_activity_codes', $codes);
	}

	public static function init_hooks() {

		add_action('latepoint_booking_created', 'OsActivitiesHelper::log_booking_created');
		add_action('latepoint_booking_updated', 'OsActivitiesHelper::log_booking_updated', 10, 2);
		add_action('latepoint_customer_created', 'OsActivitiesHelper::log_customer_created');
		add_action('latepoint_customer_updated', 'OsActivitiesHelper::log_customer_updated', 10, 2);
		add_action('latepoint_agent_created', 'OsActivitiesHelper::log_agent_created');
		add_action('latepoint_agent_updated', 'OsActivitiesHelper::log_agent_updated', 10, 2);
		add_action('latepoint_service_created', 'OsActivitiesHelper::log_service_created');
		add_action('latepoint_service_updated', 'OsActivitiesHelper::log_service_updated', 10, 2);
		add_action('latepoint_booking_intent_converted', 'OsActivitiesHelper::log_booking_intent_converted', 10, 2);
		add_action('latepoint_booking_intent_created', 'OsActivitiesHelper::log_booking_intent_created');
		add_action('latepoint_booking_intent_updated', 'OsActivitiesHelper::log_booking_intent_updated');

	}

	public static function log_booking_intent_updated(OsBookingIntentModel $booking_intent) {
		$data = [];
		$data['booking_id'] = $booking_intent->booking_id;
		$data['customer_id'] = $booking_intent->customer_id;
		$data['code'] = 'booking_intent_updated';
		$data['description'] = json_encode(['booking_data_vars' => $booking_intent->get_data_vars()]);
		OsActivitiesHelper::create_activity($data);
	}

	public static function log_booking_intent_created(OsBookingIntentModel $booking_intent) {
		$data = [];
		$data['booking_id'] = $booking_intent->booking_id;
		$data['customer_id'] = $booking_intent->customer_id;
		$data['code'] = 'booking_intent_created';
		$data['description'] = json_encode(['booking_data_vars' => $booking_intent->get_data_vars()]);
		OsActivitiesHelper::create_activity($data);
	}

	public static function log_booking_intent_converted(OsBookingIntentModel $booking_intent, OsBookingModel $booking) {
		$data = [];
		$data['booking_id'] = $booking_intent->booking_id;
		$data['customer_id'] = $booking_intent->customer_id;
		$data['code'] = 'booking_intent_converted';
		$data['description'] = json_encode(['booking_data_vars' => $booking_intent->get_data_vars()]);
		OsActivitiesHelper::create_activity($data);
	}

	public static function log_booking_created(OsBookingModel $booking) {
		$data = [];
		$data['booking_id'] = $booking->id;
		$data['code'] = 'booking_created';
		$data['description'] = json_encode(['booking_data_vars' => $booking->get_data_vars()]);
		OsActivitiesHelper::create_activity($data);
	}

	public static function log_booking_updated(OsBookingModel $booking, OsBookingModel $old_booking) {
		$data = [];
		$data['booking_id'] = $booking->id;
		$data['code'] = 'booking_updated';
		$data['description'] = json_encode(['booking_data_vars' => ['new' => $booking->get_data_vars(), 'old' => $old_booking->get_data_vars()]]);
		OsActivitiesHelper::create_activity($data);
	}

	public static function log_customer_created(OsCustomerModel $customer) {
		$data = [];
		$data['customer_id'] = $customer->id;
		$data['code'] = 'customer_created';
		$data['description'] = json_encode(['customer_data_vars' => $customer->get_data_vars()]);
		OsActivitiesHelper::create_activity($data);

	}

	public static function log_customer_updated(OsCustomerModel $customer, array $old_customer_data) {
		$data = [];
		$data['customer_id'] = $customer->id;
		$data['code'] = 'customer_updated';
		$data['description'] = json_encode(['customer_data_vars' => ['new' => $customer->get_data_vars(), 'old' => $old_customer_data]]);
		OsActivitiesHelper::create_activity($data);

	}

	public static function log_agent_created(OsAgentModel $agent) {

	}

	public static function log_agent_updated(OsAgentModel $agent, array $old_agent) {

	}

	public static function log_service_created(OsServiceModel $service) {

	}

	public static function log_service_updated(OsServiceModel $service, array $old_service) {

	}
}