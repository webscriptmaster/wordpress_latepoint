<?php 

class OsDatabaseHelper {

	public static function run_setup(){
		self::run_version_specific_updates();
		self::install_database();
	}

  public static function check_db_version(){
    $current_db_version = OsSettingsHelper::get_db_version();
    if(!$current_db_version || version_compare(LATEPOINT_DB_VERSION, $current_db_version)){
      self::install_database();
    }
  }

  // [name => 'addon_name', 'db_version' => '1.0.0', 'version' => '1.0.0']
  public static function get_installed_addons_list(){
    $installed_addons = [];
    $installed_addons = apply_filters('latepoint_installed_addons', $installed_addons);
    return $installed_addons;
  }


  // Check if addons databases are up to date
  public static function check_db_version_for_addons(){
    $is_new_addon_db_version_available = false;
    $installed_addons = self::get_installed_addons_list();
    if(empty($installed_addons)) return;
    foreach($installed_addons as $installed_addon){
      $current_addon_db_version = get_option($installed_addon['name'] . '_addon_db_version');
      if(!$current_addon_db_version || version_compare($current_addon_db_version, $installed_addon['db_version'])){
        OsAddonsHelper::save_addon_info($installed_addon['name'], $installed_addon['db_version']);
        $is_new_addon_db_version_available = true;
      }
    }
    if($is_new_addon_db_version_available) self::install_database_for_addons();
  }


  // Install queries for addons
	public static function install_database_for_addons(){
		$sqls = self::get_table_queries_for_addons();
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    foreach($sqls as $sql){
      error_log(print_r(dbDelta( $sql ), true));
    }
	}



  public static function install_database(){
    $sqls = self::get_initial_table_queries();
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    foreach($sqls as $sql){
      error_log(print_r(dbDelta( $sql ), true));
    }
    self::run_version_specific_updates();
    self::seed_initial_data();
    update_option( 'latepoint_db_version', LATEPOINT_DB_VERSION );
  }

	public static function seed_initial_data(){
		// if DB version is set (means that it's probably an update) skip seeding
		if(OsSettingsHelper::get_db_version()) return false;
		// if database was already seeded before - skip it
		if(OsSettingsHelper::get_settings_value('is_database_seeded', false)) return false;

		// set default booking status rules
		OsSettingsHelper::save_setting_by_name('default_booking_status', LATEPOINT_BOOKING_STATUS_APPROVED);
		OsSettingsHelper::save_setting_by_name('timeslot_blocking_statuses', LATEPOINT_BOOKING_STATUS_APPROVED);
		OsSettingsHelper::save_setting_by_name('calendar_hidden_statuses', LATEPOINT_BOOKING_STATUS_CANCELLED);
		OsSettingsHelper::save_setting_by_name('need_action_statuses', implode(',',[LATEPOINT_BOOKING_STATUS_PENDING, LATEPOINT_BOOKING_STATUS_PAYMENT_PENDING]));
		// create default processes
		$process = new OsProcessModel();
		$process->event_type = 'booking_created';
		$process->name = 'New Booking Notification';
		$actions = [];


		foreach (['agent', 'customer'] as $user_type) {
			$action = [];
			$action['type'] = 'send_email';
			$action['settings']['to_email'] = '{{'.$user_type.'_full_name}} <{{'.$user_type.'_email}}>';
			$action['settings']['subject'] = ($user_type == 'agent') ? "New Appointment Received" : "Appointment Confirmation";
			$action['settings']['content'] = OsEmailHelper::get_email_layout(file_get_contents(LATEPOINT_VIEWS_ABSPATH . 'mailers/'.$user_type.'/booking_created.php'));
			$actions[\LatePoint\Misc\ProcessAction::generate_id()] = $action;
		}
		$process_actions = OsProcessesHelper::iterate_trigger_conditions([], $actions);
		$process_actions[0]['time_offset'] = [];
		$process->actions_json = json_encode($process_actions);
		if(!OsProcessesHelper::check_if_process_exists($process)) $process->save();

	  /**
	   * Hook your initial data seed actions here
	   *
	   * @since 4.7.0
	   * @hook latepoint_seed_initial_data
	   *
	   */
		do_action('latepoint_seed_initial_data');
		OsSettingsHelper::save_setting_by_name('is_database_seeded', true);

	}

	/**
	 *
	 * Used to target a specific version during an update
	 *
	 * @return bool
	 */
	public static function run_version_specific_updates(){
		$current_db_version = OsSettingsHelper::get_db_version();
		if(!$current_db_version) return false;
		$sqls = [];
		if(version_compare('1.0.2', $current_db_version) > 0){
			// lower than 1.0.2
			$sqls = self::get_queries_for_nullable_columns();
			self::run_queries($sqls);
		}
    if(version_compare('1.1.0', $current_db_version) > 0){
      // lower than 1.1.0
      $sqls = self::set_end_date_for_bookings();
      self::run_queries($sqls);
    }
    if(version_compare('1.3.0', $current_db_version) > 0){
      // lower than 1.3.0
      $sqls = [];
      $sqls[] = "UPDATE ".LATEPOINT_TABLE_BOOKINGS." SET total_attendies = 1 WHERE total_attendies IS NULL;";
      $sqls[] = "UPDATE ".LATEPOINT_TABLE_SERVICES." SET visibility = '".LATEPOINT_SERVICE_VISIBILITY_VISIBLE."' WHERE visibility IS NULL OR visibility = '';";
      $sqls[] = "UPDATE ".LATEPOINT_TABLE_SERVICES." SET capacity_min = 1 WHERE capacity_min IS NULL;";
      $sqls[] = "UPDATE ".LATEPOINT_TABLE_SERVICES." SET capacity_max = 1 WHERE capacity_max IS NULL;";
      self::run_queries($sqls);
    }
    if(version_compare('1.3.1', $current_db_version) > 0){
      $sqls = [];
      $sqls[] = "ALTER TABLE ".LATEPOINT_TABLE_CUSTOMERS." MODIFY COLUMN first_name varchar(255)";
      self::run_queries($sqls);
    }
    if(version_compare('1.3.7', $current_db_version) > 0){
      $sqls = [];
      $sqls[] = "ALTER TABLE ".LATEPOINT_TABLE_AGENTS." MODIFY COLUMN wp_user_id int(11)";
      self::run_queries($sqls);
    }
    if(version_compare('1.4.8', $current_db_version) > 0){
	    update_option( 'latepoint_db_seeded', true );
			OsSettingsHelper::save_setting_by_name('timeslot_blocking_statuses', LATEPOINT_BOOKING_STATUS_APPROVED);
			OsSettingsHelper::save_setting_by_name('calendar_hidden_statuses', LATEPOINT_BOOKING_STATUS_CANCELLED);
			OsSettingsHelper::save_setting_by_name('need_action_statuses', implode(',', [LATEPOINT_BOOKING_STATUS_PENDING, LATEPOINT_BOOKING_STATUS_PAYMENT_PENDING]));
			$tile_info = OsSettingsHelper::get_booking_template_for_calendar();
			$tile_info = OsUtilHelper::replace_single_curly_with_double($tile_info);
			OsSettingsHelper::save_setting_by_name('booking_template_for_calendar', $tile_info);

			// -------
			// Update {var} to {{var}}
	    // -------

	    // password reset message
	    $content_to_replace = OsUtilHelper::replace_single_curly_with_double(OsSettingsHelper::get_settings_value('email_customer_password_reset_request_content', ''));
			OsSettingsHelper::save_setting_by_name('email_customer_password_reset_request_content', $content_to_replace);

			// new message (chat)
	    $content_to_replace = OsUtilHelper::replace_single_curly_with_double(OsSettingsHelper::get_settings_value('email_notification_customer_has_new_message_content', ''));
			OsSettingsHelper::save_setting_by_name('email_notification_customer_has_new_message_content', $content_to_replace);

			// js tracking code
	    $content_to_replace = OsUtilHelper::replace_single_curly_with_double(OsSettingsHelper::get_settings_value('confirmation_step_tracking_code', ''));
			OsSettingsHelper::save_setting_by_name('confirmation_step_tracking_code', $content_to_replace);

			// Google calendar
	    $content_to_replace = OsUtilHelper::replace_single_curly_with_double(OsSettingsHelper::get_settings_value('google_calendar_event_summary_template', ''));
			if(!empty($content_to_replace)) OsSettingsHelper::save_setting_by_name('google_calendar_event_summary_template', $content_to_replace);
	    $content_to_replace = OsUtilHelper::replace_single_curly_with_double(OsSettingsHelper::get_settings_value('google_calendar_event_description_template', ''));
			if(!empty($content_to_replace)) OsSettingsHelper::save_setting_by_name('google_calendar_event_description_template', $content_to_replace);



	    // -------
			// migrate old notification system to processes
	    // -------

			$process_actions = [];
			// STATUS CHANGE NOTIFICATION

			if(OsSettingsHelper::is_on('notifications_email')) {
		    // email

				foreach (['agent', 'customer'] as $user_type) {
					$action = [];
					if (OsSettingsHelper::is_on('notification_' . $user_type . '_booking_status_changed')) {
						$action['type'] = 'send_email';
						$action['settings']['to_email'] = '{{'.$user_type.'_full_name}} <{{'.$user_type.'_email}}>';
						$action['settings']['subject'] = OsUtilHelper::replace_single_curly_with_double(OsSettingsHelper::get_settings_value('notification_' . $user_type . '_booking_status_changed_notification_subject', ''));
						$action['settings']['content'] = OsUtilHelper::replace_single_curly_with_double(OsSettingsHelper::get_settings_value('notification_' . $user_type . '_booking_status_changed_notification_content', ''));
						$process_actions[\LatePoint\Misc\ProcessAction::generate_id()] = $action;
					}
				}
				if ($process_actions) {
					// put all under single process with multiple actions
					$process = new OsProcessModel();
					$process->event_type = 'booking_updated';
					$process->name = 'Booking status change notification';

					$trigger_conditions[] = ['object' => 'old_booking', 'property' => 'old_booking__status', 'operator' => 'changed', 'value' => ''];
					$process_actions = OsProcessesHelper::iterate_trigger_conditions($trigger_conditions, $process_actions);
					$process_actions[0]['time_offset'] = [];
					$process->actions_json = json_encode($process_actions);
					if(!OsProcessesHelper::check_if_process_exists($process)) $process->save();
				}

			}



			$process_actions = [];
	    // NEW BOOKING NOTIFICATION
			if(OsSettingsHelper::is_on('notifications_email')){
				OsSettingsHelper::save_setting_by_name('notifications_email_processor', 'wp_mail');
				// email
				foreach(['agent', 'customer'] as $user_type){
					$action = [];
					if(OsSettingsHelper::is_on('notification_'.$user_type.'_confirmation')){
						$action['type'] = 'send_email';
						$action['settings']['to_email'] = '{{'.$user_type.'_email}}';
						$action['settings']['subject'] = OsUtilHelper::replace_single_curly_with_double(OsSettingsHelper::get_settings_value((($user_type == 'agent') ? 'notification_agent_new_booking_notification_subject' : 'notification_customer_booking_confirmation_subject'), ''));
						$action['settings']['content'] = OsUtilHelper::replace_single_curly_with_double(OsSettingsHelper::get_settings_value((($user_type == 'agent') ? 'notification_agent_new_booking_notification_content' : 'notification_customer_booking_confirmation_content'), ''));
						$process_actions[\LatePoint\Misc\ProcessAction::generate_id()] = $action;
					}
				}
			}
			if(OsSettingsHelper::is_on('notifications_sms')){
				OsSettingsHelper::save_setting_by_name('notifications_sms_processor', 'twilio');
				// sms
				foreach(['agent', 'customer'] as $user_type){
					$action = [];
					if(OsSettingsHelper::is_on('notification_sms_'.$user_type.'_confirmation')){
						$action['type'] = 'send_sms';
						$action['settings']['to_phone'] = '{{'.$user_type.'_phone}}';
						$action['settings']['content'] = OsUtilHelper::replace_single_curly_with_double(OsSettingsHelper::get_settings_value((($user_type == 'agent') ? 'notification_sms_agent_new_booking_notification_message' : 'notification_sms_customer_booking_confirmation_message'), ''));
						$process_actions[\LatePoint\Misc\ProcessAction::generate_id()] = $action;
					}
				}
			}



			// webhooks for new booking

			// migrate webhooks for new booking into processes
	    $webhooks = json_decode(OsSettingsHelper::get_settings_value('webhooks', ''), true);
			if($webhooks){
				foreach($webhooks as $webhook){
					// only process new booking
					if($webhook['status'] != 'active' || $webhook['trigger'] != 'new_booking') continue;
					$action = [];
					$action['type'] = 'trigger_webhook';
					$action['settings']['url'] = $webhook['url'];
					$process_actions[$webhook['id']] = $action;
				}
			}

			// CREATE NEW BOOKING PROCESSES IF THERE ARE ANY ACTIONS
			if($process_actions){
		    // put all under single process with multiple actions
				$process = new OsProcessModel();
				$process->event_type = 'booking_created';
				$process->name = 'Booking created notification';

				$process_actions = OsProcessesHelper::iterate_trigger_conditions([], $process_actions);
				$process_actions[0]['time_offset'] = [];
				$process->actions_json = json_encode($process_actions);
				if(!OsProcessesHelper::check_if_process_exists($process)) $process->save();
			}

			// migrate other webhooks (not new booking) into processes
			if($webhooks){
				$process_actions_for_triggers = ['updated_booking' => [], 'new_customer' => [], 'new_transaction' => []];
				foreach($webhooks as $webhook){
					if($webhook['status'] != 'active' || !in_array($webhook['trigger'], ['updated_booking', 'new_customer', 'new_transaction'])) continue;
					$process_actions_for_triggers[$webhook['trigger']][$webhook['id']] = ['type' => 'trigger_webhook', 'settings' => ['url' => $webhook['url']]];
				}
				foreach($process_actions_for_triggers as $webhook_trigger => $actions){
					if($actions){
						$process = new OsProcessModel();
						switch($webhook_trigger){
							case 'updated_booking':
								$process->name = 'Booking updated notification';
								$process->event_type = 'booking_updated';
								break;
							case 'new_customer':
								$process->name = 'New customer notification';
								$process->event_type = 'customer_created';
								break;
							case 'new_transaction':
								$process->name = 'New transaction notification';
								$process->event_type = 'transaction_created';
								break;
						}
						$process_actions = OsProcessesHelper::iterate_trigger_conditions([], $actions);
						$process_actions[0]['time_offset'] = [];
						$process->actions_json = json_encode($process_actions);
						if(!OsProcessesHelper::check_if_process_exists($process)) $process->save();
					}
				}
			}

			// migrate reminders into processes
	    // old example: {"rem_0zMZzZVY":{"name":"Reminder to customer","medium":"email","receiver":"customer","value":"7","unit":"day","when":"before","subject":"Reminder","content":"<p>Testing<\/p>","id":"rem_0zMZzZVY"}}
	    // multiple: {"rem_POtZuDDd":{"name":"Sms Reminder before","medium":"sms","receiver":"customer","value":"7","unit":"day","when":"before","subject":"","content":"<p>Testing<\/p>","id":"rem_POtZuDDd"},"rem_q4kA6JwC":{"name":"Sms Reminder after","medium":"sms","receiver":"agent","value":"7","unit":"day","when":"after","subject":"test","content":"Testing","id":"rem_q4kA6JwC"},"rem_hR6YOF3w":{"name":"Email Reminder after","medium":"email","receiver":"agent","value":"7","unit":"day","when":"after","subject":"test","content":"Testing","id":"rem_hR6YOF3w"}}
	    $reminders = json_decode(OsSettingsHelper::get_settings_value('reminders', ''), true);
			if($reminders){
				$processes = [];
				$actions = [];
				foreach($reminders as $reminder){

					// create action
					$action = [];
					$action_id = \LatePoint\Misc\ProcessAction::generate_id();
					$action['settings']['content'] = OsUtilHelper::replace_single_curly_with_double($reminder['content'] ?? '');
					switch($reminder['medium']){
						case 'sms':
							$action['type'] = 'send_sms';
							$action['settings']['to_phone'] = ($reminder['receiver'] == 'customer') ? '{{customer_phone}}' : '{{agent_phone}}';
						break;
						case 'email':
							$action['type'] = 'send_email';
							$action['settings']['to_email'] = ($reminder['receiver'] == 'customer') ? '{{customer_email}}' : '{{agent_email}}';
							$action['settings']['subject'] = OsUtilHelper::replace_single_curly_with_double($reminder['subject']);
						break;
					}

					// generate time offset
					$time_offset = ['value' => $reminder['value'], 'unit' => $reminder['unit'], 'before_after' => $reminder['when']];

					// attach to process
					if($processes){
						$existing = false;
						// try to find process that matches parameters
						for($i=0; $i<count($processes);$i++){
							if($processes[$i]['time_offset'] == $time_offset){
								$processes[$i]['actions'][$action_id] = $action;
								$existing = true;
								break;
							}
						}
						// didn't find process with same time offset, create new
						if(!$existing){
							$process = ['name' => $reminder['name'], 'event_type' => 'booking_start', 'time_offset' => $time_offset, 'actions' => []];
							$process['actions'][$action_id] = $action;
							$processes[] = $process;
						}
					}else{
						$process = ['name' => $reminder['name'], 'event_type' => 'booking_start', 'time_offset' => $time_offset, 'actions' => []];
						$process['actions'][$action_id] = $action;
						$processes[] = $process;
					}


				}
				if($processes){
					foreach($processes as $process_data){
						$process = new OsProcessModel();
						$process->event_type = $process_data['event_type'];
						$process->name = $process_data['name'];

						$process_actions = OsProcessesHelper::iterate_trigger_conditions([], $process_data['actions']);
						$process_actions[0]['time_offset'] = $process_data['time_offset'];
						$process->actions_json = json_encode($process_actions);
						if(!OsProcessesHelper::check_if_process_exists($process)) $process->save();
					}
				}
			}

			// Update customer phone numbers to new E.164 format based on the country that was selected in settings
	    $customers = new OsCustomerModel();
			$customers = $customers->get_results_as_models();
	    foreach($customers as $customer){
				if(empty($customer->phone)) continue;
				$formatted_phone = OsUtilHelper::sanitize_phone_number($customer->phone, OsSettingsHelper::get_settings_value('country_phone_code', ''));
				if(!empty($formatted_phone)) $customer->update_attributes(['phone' => $formatted_phone]);
	    }
			// update agent phone numbers
			$agents = new OsAgentModel();
			$agents = $agents->get_results_as_models();
	    foreach($agents as $agent){
				if(empty($agent->phone)) continue;
				$formatted_phone = OsUtilHelper::sanitize_phone_number($agent->phone, OsSettingsHelper::get_settings_value('country_phone_code', ''));
				if(!empty($formatted_phone)) $agent->update_attributes(['phone' => $formatted_phone]);
	    }
    }

    if(version_compare('1.4.91', $current_db_version) > 0){
      $sqls = [];
      $sqls[] = "ALTER TABLE ".LATEPOINT_TABLE_BOOKINGS." DROP COLUMN start_datetime_gmt";
      $sqls[] = "ALTER TABLE ".LATEPOINT_TABLE_BOOKINGS." DROP COLUMN end_datetime_gmt";
      self::run_queries($sqls);
    }

	  /**
	   * Hook your updates to database that need to be run for specific version of database
	   *
	   * @since 1.0.0
	   * @hook latepoint_run_version_specific_updates
	   *
	   * @param {string} version of database before the update
	   */
		do_action('latepoint_run_version_specific_updates', $current_db_version);
		return true;
	}

	public static function run_queries($sqls){
    global $wpdb;
		if($sqls && is_array($sqls)){
			foreach($sqls as $sql){
				$wpdb->query($sql);
        OsDebugHelper::log_query($sql);
			}
		}
	}

  public static function set_end_date_for_bookings(){
    $sqls = [];

    $sqls[] = "UPDATE ".LATEPOINT_TABLE_BOOKINGS." SET end_date = start_date WHERE end_date IS NULL;";
    return $sqls;
  }



  // Get queries registered by addons
  public static function get_table_queries_for_addons(){
    $sqls = [];
    $sqls = apply_filters('latepoint_addons_sqls', $sqls);
    return $sqls;
  }



  public static function get_initial_table_queries(){

    global $wpdb;

    $charset_collate = $wpdb->get_charset_collate();

    $sqls = [];

    $sqls[] = "CREATE TABLE ".LATEPOINT_TABLE_PROCESS_JOBS." (
      id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
      process_id int(11) NOT NULL,
      object_id int(11) NOT NULL,
      object_model_type varchar(55),
      settings text,
      to_run_after_utc datetime,
      status varchar(30) DEFAULT '".LATEPOINT_JOB_STATUS_SCHEDULED."',
      run_result text,
      process_info text,
      created_at datetime,
      updated_at datetime,
      PRIMARY KEY  (id)
    ) $charset_collate;";


    $sqls[] = "CREATE TABLE ".LATEPOINT_TABLE_BOOKING_INTENTS." (
      id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
      intent_key varchar(55) NOT NULL,
      customer_id int(11) NOT NULL,
      booking_data text,
      restrictions_data text,
      payment_data text,
      booking_id int(11),
      booking_form_page_url text,
      created_at datetime,
      updated_at datetime,
      PRIMARY KEY  (id),
      UNIQUE KEY intent_key_index (intent_key)
    ) $charset_collate;";

    $sqls[] = "CREATE TABLE ".LATEPOINT_TABLE_SESSIONS." (
      id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
      session_key varchar(55) NOT NULL,
      session_value longtext NOT NULL,
      expiration BIGINT UNSIGNED NOT NULL,
      hash varchar(50) NOT NULL,
      PRIMARY KEY  (id),
      UNIQUE KEY session_key (session_key)
    ) $charset_collate;";

    $sqls[] = "CREATE TABLE ".LATEPOINT_TABLE_BOOKINGS." (
      id int(11) NOT NULL AUTO_INCREMENT,
      booking_code varchar(10),
      start_date date,
      end_date date,
      start_time mediumint(9),
      end_time mediumint(9),
      start_datetime_utc datetime,
      end_datetime_utc datetime,
      buffer_before mediumint(9) NOT NULL,
      buffer_after mediumint(9) NOT NULL,
      duration mediumint(9),
      subtotal decimal(20,4),
      price decimal(20,4),
      status varchar(30) DEFAULT '".LATEPOINT_BOOKING_STATUS_PENDING."' NOT NULL,
      payment_status varchar(30) DEFAULT '".LATEPOINT_PAYMENT_STATUS_NOT_PAID."' NOT NULL,
      customer_id mediumint(9) NOT NULL,
      service_id mediumint(9) NOT NULL,
      agent_id mediumint(9) NOT NULL,
      location_id mediumint(9),
      total_attendies mediumint(4),
      payment_method varchar(55),
      payment_portion varchar(55),
      ip_address varchar(55),
      source_id varchar(100),
      source_url text,
      coupon_code varchar(100),
      coupon_discount decimal(20,4),
      customer_comment text,
      created_at datetime,
      updated_at datetime,
      KEY start_date_index (start_date),
      KEY end_date_index (end_date),
      KEY status_index (status),
      KEY customer_id_index (customer_id),
      KEY service_id_index (service_id),
      KEY agent_id_index (agent_id),
      KEY location_id_index (location_id),
      PRIMARY KEY  (id)
    ) $charset_collate;";


    $sqls[] = "CREATE TABLE ".LATEPOINT_TABLE_BOOKING_META." (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      object_id mediumint(9) NOT NULL,
      meta_key varchar(110) NOT NULL,
      meta_value text,
      created_at datetime,
      updated_at datetime,
      KEY meta_key_index (meta_key),
      KEY object_id_index (object_id),
      PRIMARY KEY  (id)
    ) $charset_collate;";


    $sqls[] = "CREATE TABLE ".LATEPOINT_TABLE_PROCESSES." (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      name varchar(110) NOT NULL,
      event_type varchar(110) NOT NULL,
      actions_json text,
      status varchar(30) DEFAULT '".LATEPOINT_STATUS_ACTIVE."',
      created_at datetime,
      updated_at datetime,
      PRIMARY KEY  (id)
    ) $charset_collate;";

    $sqls[] = "CREATE TABLE ".LATEPOINT_TABLE_SERVICE_META." (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      object_id mediumint(9) NOT NULL,
      meta_key varchar(110) NOT NULL,
      meta_value text,
      created_at datetime,
      updated_at datetime,
      KEY meta_key_index (meta_key),
      KEY object_id_index (object_id),
      PRIMARY KEY  (id)
    ) $charset_collate;";

    $sqls[] = "CREATE TABLE ".LATEPOINT_TABLE_CUSTOMER_META." (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      object_id mediumint(9) NOT NULL,
      meta_key varchar(110) NOT NULL,
      meta_value text,
      created_at datetime,
      updated_at datetime,
      KEY meta_key_index (meta_key),
      KEY object_id_index (object_id),
      PRIMARY KEY  (id)
    ) $charset_collate;";

    $sqls[] = "CREATE TABLE ".LATEPOINT_TABLE_AGENT_META." (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      object_id mediumint(9) NOT NULL,
      meta_key varchar(110) NOT NULL,
      meta_value text,
      created_at datetime,
      updated_at datetime,
      KEY meta_key_index (meta_key),
      KEY object_id_index (object_id),
      PRIMARY KEY  (id)
    ) $charset_collate;";


    $sqls[] = "CREATE TABLE ".LATEPOINT_TABLE_SETTINGS." (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      name varchar(110) NOT NULL,
      value longtext,
      created_at datetime,
      updated_at datetime,
      KEY name_index (name),
      PRIMARY KEY  (id)
    ) $charset_collate;";


    $sqls[] = "CREATE TABLE ".LATEPOINT_TABLE_LOCATIONS." (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      name varchar(255) NOT NULL,
      full_address text,
      status varchar(20) NOT NULL,
      category_id int(11),
      order_number int(11),
      selection_image_id int(11),
      created_at datetime,
      updated_at datetime,
      KEY status_index (status),
      PRIMARY KEY  (id)
    ) $charset_collate;";


    $sqls[] = "CREATE TABLE ".LATEPOINT_TABLE_LOCATION_CATEGORIES." (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      name varchar(100) NOT NULL,
      short_description text,
      parent_id mediumint(9),
      selection_image_id int(11),
      order_number int(11),
      created_at datetime,
      updated_at datetime,
      KEY order_number_index (order_number),
      KEY parent_id_index (parent_id),
      PRIMARY KEY  (id)
    ) $charset_collate;";


    $sqls[] = "CREATE TABLE ".LATEPOINT_TABLE_SERVICES." (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      name varchar(255) NOT NULL,
      short_description text,
      is_price_variable boolean,
      price_min decimal(20,4),
      price_max decimal(20,4),
      charge_amount decimal(20,4),
      deposit_amount decimal(20,4),
      is_deposit_required boolean,
      duration_name varchar(255),
      override_default_booking_status varchar(255),
      duration int(11) NOT NULL,
      buffer_before int(11),
      buffer_after int(11),
      category_id int(11),
      order_number int(11),
      selection_image_id int(11),
      description_image_id int(11),
      bg_color varchar(20),
      timeblock_interval int(11),
      capacity_min int(4),
      capacity_max int(4),
      status varchar(20) NOT NULL,
      visibility varchar(20) NOT NULL,
      created_at datetime,
      updated_at datetime,
      KEY category_id_index (category_id),
      KEY order_number_index (order_number),
      KEY status_index (status),
      PRIMARY KEY  (id)
    ) $charset_collate;";

    $sqls[] = "CREATE TABLE ".LATEPOINT_TABLE_AGENTS." (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      avatar_image_id int(11),
      bio_image_id int(11),
      first_name varchar(255) NOT NULL,
      last_name varchar(255),
      display_name varchar(255),
      title varchar(255),
      bio text,
      features text,
      email varchar(110) NOT NULL,
      phone varchar(255),
      password varchar(255),
      custom_hours boolean,
      wp_user_id int(11),
      status varchar(20) NOT NULL,
      extra_emails text,
      extra_phones text,
      created_at datetime,
      updated_at datetime,
      KEY email_index (email),
      PRIMARY KEY  (id)
    ) $charset_collate;";

    $sqls[] = "CREATE TABLE ".LATEPOINT_TABLE_STEP_SETTINGS." (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      label varchar(50) NOT NULL,
      value text,
      step varchar(50),
      created_at datetime,
      updated_at datetime,
      KEY step_index (step),
      KEY label_index (label),
      PRIMARY KEY  (id)
    ) $charset_collate;";
    
    
    $sqls[] = "CREATE TABLE ".LATEPOINT_TABLE_CUSTOMERS." (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      first_name varchar(255),
      last_name varchar(255),
      email varchar(110) NOT NULL,
      phone varchar(255),
      avatar_image_id int(11),
      status varchar(50) NOT NULL,
      password varchar(255),
      activation_key varchar(255),
      account_nonse varchar(255),
      google_user_id varchar(255),
      facebook_user_id varchar(255),
      wordpress_user_id int(11),
      is_guest boolean,
      notes text,
      admin_notes text,
      created_at datetime,
      updated_at datetime,
      KEY email_index (email),
      KEY status_index (status),
      KEY wordpress_user_id_index (wordpress_user_id),
      PRIMARY KEY  (id)
    ) $charset_collate;";

    $sqls[] = "CREATE TABLE ".LATEPOINT_TABLE_SERVICE_CATEGORIES." (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      name varchar(100) NOT NULL,
      short_description text,
      parent_id mediumint(9),
      selection_image_id int(11),
      order_number int(11),
      created_at datetime,
      updated_at datetime,
      KEY order_number_index (order_number),
      KEY parent_id_index (parent_id),
      PRIMARY KEY  (id)
    ) $charset_collate;";

    $sqls[] = "CREATE TABLE ".LATEPOINT_TABLE_CUSTOM_PRICES." (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      agent_id int(11) NOT NULL,
      service_id int(11) NOT NULL,
      location_id int(11) NOT NULL,
      is_price_variable boolean,
      price_min decimal(20,4),
      price_max decimal(20,4),
      charge_amount decimal(20,4),
      is_deposit_required boolean,
      deposit_amount decimal(20,4),
      created_at datetime,
      updated_at datetime,
      KEY agent_id_index (agent_id),
      KEY service_id_index (service_id),
      KEY location_id_index (location_id),
      PRIMARY KEY  (id)
    ) $charset_collate;";

    $sqls[] = "CREATE TABLE ".LATEPOINT_TABLE_WORK_PERIODS." (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      agent_id int(11) NOT NULL,
      service_id int(11) NOT NULL,
      location_id int(11) NOT NULL,
      start_time smallint(6) NOT NULL,
      end_time smallint(6) NOT NULL,
      week_day tinyint(3) NOT NULL,
      custom_date date,
      chain_id varchar(20),
      created_at datetime,
      updated_at datetime,
      KEY agent_id_index (agent_id),
      KEY service_id_index (service_id),
      KEY location_id_index (location_id),
      KEY week_day_index (week_day),
      KEY custom_date_index (custom_date),
      PRIMARY KEY  (id)
    ) $charset_collate;";

    $sqls[] = "CREATE TABLE ".LATEPOINT_TABLE_AGENTS_SERVICES." (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      agent_id int(11) NOT NULL,
      service_id int(11) NOT NULL,
      location_id int(11),
      is_custom_hours BOOLEAN,
      is_custom_price BOOLEAN,
      is_custom_duration BOOLEAN,
      created_at datetime,
      updated_at datetime,
      KEY agent_id_index (agent_id),
      KEY service_id_index (service_id),
      KEY location_id_index (location_id),
      PRIMARY KEY  (id)
    ) $charset_collate;";

    $sqls[] = "CREATE TABLE ".LATEPOINT_TABLE_ACTIVITIES." (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      agent_id int(11),
      booking_id int(11),
      service_id int(11),
      customer_id int(11),
      location_id int(11),
      code varchar(255) NOT NULL,
      description text,
      initiated_by varchar(100),
      initiated_by_id int(11),
      created_at datetime,
      updated_at datetime,
      PRIMARY KEY  (id)
    ) $charset_collate;";

    $sqls[] = "CREATE TABLE ".LATEPOINT_TABLE_TRANSACTIONS." (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      token text NOT NULL,
      booking_id int(11) NOT NULL,
      customer_id int(11) NOT NULL,
      processor varchar(100) NOT NULL,
      payment_method varchar(55),
      payment_portion varchar(55),
      funds_status varchar(40),
      status varchar(100) NOT NULL,
      amount decimal(20,4),
      notes text,
      created_at datetime,
      updated_at datetime,
      PRIMARY KEY  (id)
    ) $charset_collate;";

    return $sqls;
  }

























  public static function get_queries_for_nullable_columns(){
  	$sqls = [];

    $sqls[] = "ALTER TABLE ".LATEPOINT_TABLE_BOOKINGS."
					      MODIFY COLUMN ip_address varchar(55),
					      MODIFY COLUMN created_at datetime,
					      MODIFY COLUMN updated_at datetime;";


    $sqls[] = "ALTER TABLE ".LATEPOINT_TABLE_CUSTOMER_META."
					      MODIFY COLUMN meta_value text,
					      MODIFY COLUMN created_at datetime,
					      MODIFY COLUMN updated_at datetime;";

    $sqls[] = "ALTER TABLE ".LATEPOINT_TABLE_SETTINGS."
					      MODIFY COLUMN value text,
					      MODIFY COLUMN created_at datetime,
					      MODIFY COLUMN updated_at datetime;";

    $sqls[] = "ALTER TABLE ".LATEPOINT_TABLE_SERVICES."
					      MODIFY COLUMN short_description text,
					      MODIFY COLUMN is_price_variable boolean,
					      MODIFY COLUMN price_min decimal(20,4),
					      MODIFY COLUMN price_max decimal(20,4),
					      MODIFY COLUMN charge_amount decimal(20,4),
					      MODIFY COLUMN is_deposit_required boolean,
					      MODIFY COLUMN buffer_before int(11),
					      MODIFY COLUMN buffer_after int(11),
					      MODIFY COLUMN category_id int(11),
					      MODIFY COLUMN order_number int(11),
					      MODIFY COLUMN selection_image_id int(11),
					      MODIFY COLUMN description_image_id int(11),
					      MODIFY COLUMN bg_color varchar(20),
					      MODIFY COLUMN created_at datetime,
					      MODIFY COLUMN updated_at datetime;";

    $sqls[] = "ALTER TABLE ".LATEPOINT_TABLE_AGENTS."
					      MODIFY COLUMN avatar_image_id int(11),
					      MODIFY COLUMN last_name varchar(255),
					      MODIFY COLUMN phone varchar(255),
					      MODIFY COLUMN password varchar(255),
					      MODIFY COLUMN custom_hours boolean,
					      MODIFY COLUMN created_at datetime,
					      MODIFY COLUMN updated_at datetime;";

    $sqls[] = "ALTER TABLE ".LATEPOINT_TABLE_STEP_SETTINGS."
					      MODIFY COLUMN value text,
					      MODIFY COLUMN step varchar(50),
					      MODIFY COLUMN created_at datetime,
					      MODIFY COLUMN updated_at datetime;";

  	$sqls[] = "ALTER TABLE ".LATEPOINT_TABLE_CUSTOMERS." 
						    MODIFY COLUMN last_name varchar(255),
						    MODIFY COLUMN phone varchar(255),
						    MODIFY COLUMN avatar_image_id int(11),
						    MODIFY COLUMN password varchar(255),
						    MODIFY COLUMN activation_key varchar(255),
						    MODIFY COLUMN account_nonse varchar(255),
						    MODIFY COLUMN google_user_id varchar(255),
						    MODIFY COLUMN facebook_user_id varchar(255),
						    MODIFY COLUMN is_guest boolean,
						    MODIFY COLUMN notes text,
						    MODIFY COLUMN created_at datetime,
						    MODIFY COLUMN updated_at datetime;";

    $sqls[] = "ALTER TABLE ".LATEPOINT_TABLE_SERVICE_CATEGORIES." 
					      MODIFY COLUMN short_description text,
					      MODIFY COLUMN parent_id mediumint(9),
					      MODIFY COLUMN selection_image_id int(11),
					      MODIFY COLUMN order_number int(11),
					      MODIFY COLUMN created_at datetime,
					      MODIFY COLUMN updated_at datetime";

    $sqls[] = "ALTER TABLE ".LATEPOINT_TABLE_CUSTOM_PRICES." 
					      MODIFY COLUMN is_price_variable boolean,
					      MODIFY COLUMN price_min decimal(20,4),
					      MODIFY COLUMN price_max decimal(20,4),
					      MODIFY COLUMN charge_amount decimal(20,4),
					      MODIFY COLUMN is_deposit_required boolean,
					      MODIFY COLUMN created_at datetime,
					      MODIFY COLUMN updated_at datetime";

    $sqls[] = "ALTER TABLE ".LATEPOINT_TABLE_WORK_PERIODS." 
					      MODIFY COLUMN custom_date date,
					      MODIFY COLUMN created_at datetime,
					      MODIFY COLUMN updated_at datetime";

    $sqls[] = "ALTER TABLE ".LATEPOINT_TABLE_AGENTS_SERVICES." 
					      MODIFY COLUMN is_custom_hours BOOLEAN,
					      MODIFY COLUMN is_custom_price BOOLEAN,
					      MODIFY COLUMN is_custom_duration BOOLEAN,
					      MODIFY COLUMN created_at datetime,
					      MODIFY COLUMN updated_at datetime";

    $sqls[] = "ALTER TABLE ".LATEPOINT_TABLE_ACTIVITIES." 
					      MODIFY COLUMN agent_id int(11),
					      MODIFY COLUMN booking_id int(11),
					      MODIFY COLUMN service_id int(11),
					      MODIFY COLUMN customer_id int(11),
					      MODIFY COLUMN description text,
					      MODIFY COLUMN initiated_by varchar(100),
					      MODIFY COLUMN initiated_by_id int(11),
					      MODIFY COLUMN created_at datetime,
					      MODIFY COLUMN updated_at datetime";

    $sqls[] = "ALTER TABLE ".LATEPOINT_TABLE_TRANSACTIONS." 
					      MODIFY COLUMN notes text,
					      MODIFY COLUMN created_at datetime,
					      MODIFY COLUMN updated_at datetime";
		return $sqls;
  }


}