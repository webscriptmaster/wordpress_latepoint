<?php

class OsTransactionModel extends OsModel {
	public $id,
		$token,
		$booking_id,
		$customer_id,
		$processor,
		$payment_method,
		$payment_portion,
		$funds_status,
		$amount,
		$status,
		$notes,
		$updated_at,
		$created_at;

	function __construct($id = false) {
		parent::__construct();
		$this->table_name = LATEPOINT_TABLE_TRANSACTIONS;
		$this->nice_names = ['token' => __('Confirmation Number', 'latepoint')];

		if ($id) {
			$this->load_by_id($id);
		}
	}

	public function properties_to_query(): array{
		return [
			'payment_method' => __('Payment Method', 'latepoint'),
			'payment_portion' => __('Payment Portion', 'latepoint'),
			'funds_status' => __('Funds Status', 'latepoint'),
		];
	}

	public function generate_data_vars(): array {
		return [
			'id' => $this->id,
			'booking_id' => $this->booking_id,
			'token' => $this->token,
			'processor' => $this->processor,
			'payment_method' => $this->payment_method,
			'payment_portion' => $this->payment_portion_nice_name,
			'funds_status' => $this->funds_status,
			'status' => $this->status,
			'amount' => $this->amount,
			'notes' => $this->notes,
		];
	}


	public function filter_allowed_records(): OsModel{
		if(!OsRolesHelper::are_all_records_allowed()){
			// join bookings table to filter allowed transactions
			$this->join(LATEPOINT_TABLE_BOOKINGS, ['id' => $this->table_name.'.booking_id']);
			$this->select(LATEPOINT_TABLE_TRANSACTIONS.'.*');
			if(!OsRolesHelper::are_all_records_allowed('agent')){
				$this->select(LATEPOINT_TABLE_BOOKINGS.'.agent_id');
				$this->filter_where_conditions([LATEPOINT_TABLE_BOOKINGS.'.agent_id' => OsRolesHelper::get_allowed_records('agent')]);
			}
			if(!OsRolesHelper::are_all_records_allowed('location')){
				$this->select(LATEPOINT_TABLE_BOOKINGS.'.location_id');
				$this->filter_where_conditions([LATEPOINT_TABLE_BOOKINGS.'.location_id' => OsRolesHelper::get_allowed_records('location')]);
			}
			if(!OsRolesHelper::are_all_records_allowed('service')){
				$this->select(LATEPOINT_TABLE_BOOKINGS.'.service_id');
				$this->filter_where_conditions([LATEPOINT_TABLE_BOOKINGS.'.service_id' => OsRolesHelper::get_allowed_records('service')]);
			}
		}
		return $this;
	}

	protected function params_to_sanitize() {
		return ['amount' => 'money'];
	}

	protected function get_customer() {
		if ($this->customer_id) {
			if (!isset($this->customer) || (isset($this->customer) && ($this->customer->id != $this->customer_id))) {
				$this->customer = new OsCustomerModel($this->customer_id);
			}
		} else {
			$this->customer = new OsCustomerModel();
		}
		return $this->customer;
	}


	public function get_payment_portion_nice_name($default = '') {
		$payment_portions = OsPaymentsHelper::get_payment_portions_list();
		$nice_name = (!empty($this->payment_portion) && isset($payment_portions[$this->payment_portion])) ? $payment_portions[$this->payment_portion] : $default;
		return $nice_name;
	}


	protected function get_booking() {
		if ($this->booking_id) {
			if (!isset($this->booking) || (isset($this->booking) && ($this->booking->id != $this->booking_id))) {
				$this->booking = new OsBookingModel($this->booking_id);
			}
		} else {
			$this->booking = new OsBookingModel();
		}
		return $this->booking;
	}

	protected function params_to_save($role = 'admin') {
		$params_to_save = array('id',
			'token',
			'booking_id',
			'customer_id',
			'processor',
			'payment_method',
			'payment_portion',
			'funds_status',
			'amount',
			'status',
			'notes');
		return $params_to_save;
	}


	protected function allowed_params($role = 'admin') {
		$allowed_params = array('id',
			'token',
			'booking_id',
			'customer_id',
			'processor',
			'payment_method',
			'payment_portion',
			'funds_status',
			'amount',
			'status',
			'notes');
		return $allowed_params;
	}


	protected function properties_to_validate() {
		$validations = array(
			'booking_id' => array('presence'),
			'customer_id' => array('presence'),
		);
		return $validations;
	}
}