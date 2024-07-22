<?php 

class OsMenuHelper {

	public static array $side_menu_items;

  public static function get_menu_items_by_id($query){
    $menus = self::get_side_menu_items();
    foreach($menus as $menu_item){
      if(isset($menu_item['id']) && $menu_item['id'] == $query){
				if(isset($menu_item['children'])){
					// has sub items
					return $menu_item['children'];
				}else{
					// no sub items
					return $menu_item['label'];
				}
      }
    }
    return false;
  }

  public static function get_side_menu_items() {
		if(isset(self::$side_menu_items)) return self::$side_menu_items;
    $is_update_available = OsUpdatesHelper::is_update_available();
		$menus = [];
		switch(OsAuthHelper::get_current_user()->backend_user_type){
			case LATEPOINT_USER_TYPE_ADMIN:
			case LATEPOINT_USER_TYPE_CUSTOM:
	      // ---------------
	      // ADMINISTRATOR MENU
	      // ---------------
	      $menus = array(
	        array( 'id' => 'dashboard', 'label' => __( 'Dashboard', 'latepoint' ), 'icon' => 'latepoint-icon latepoint-icon-box', 'link' => OsRouterHelper::build_link(['dashboard', 'index'])),
	        array( 'id' => 'calendar', 'label' => __( 'Calendar', 'latepoint' ), 'icon' => 'latepoint-icon latepoint-icon-calendar', 'link' => OsRouterHelper::build_link(['calendars', 'view'])),
	        array( 'id' => 'appointments', 'label' => __( 'Appointments', 'latepoint' ), 'icon' => 'latepoint-icon latepoint-icon-inbox', 'link' => OsRouterHelper::build_link(['bookings', 'index'])),
	        array( 'id' => 'payments', 'label' => __( 'Payments', 'latepoint' ), 'icon' => 'latepoint-icon latepoint-icon-credit-card', 'link' => OsRouterHelper::build_link(['transactions', 'index'])),
	        array( 'id' => 'customers', 'label' => __( 'Customers', 'latepoint' ), 'icon' => 'latepoint-icon latepoint-icon-users', 'link' => OsRouterHelper::build_link(['customers', 'index']),
	          'children' => array(
	                          array('label' => __('List of Customers', 'latepoint'), 'icon' => '', 'link' => OsRouterHelper::build_link(['customers', 'index'])),
	                          array('label' => __('Add Customer', 'latepoint'), 'icon' => '', 'link' => OsRouterHelper::build_link(['customers', 'new_form'])),
	                        )
	        ),
	        array('label' => '', 'small_label' => __('Resources', 'latepoint'), 'menu_section' => 'records'),
	        array( 'id' => 'services', 'label' => __( 'Services', 'latepoint' ), 'icon' => 'latepoint-icon latepoint-icon-package', 'link' => OsRouterHelper::build_link(['services', 'index']),
	          'children' => array(
	                          array('label' => __( 'Services', 'latepoint' ), 'icon' => '', 'link' => OsRouterHelper::build_link(['services', 'index'])),
	                          array('label' => __( 'Categories', 'latepoint' ), 'icon' => '', 'link' => OsRouterHelper::build_link(['service_categories', 'index'])),
	          )
	        ),
	        array( 'id' => 'agents', 'label' => __( 'Agents', 'latepoint' ), 'icon' => 'latepoint-icon latepoint-icon-briefcase', 'link' => OsRouterHelper::build_link(['agents', 'index'])),
	        array( 'id' => 'locations', 'label' => __( 'Locations', 'latepoint' ), 'icon' => 'latepoint-icon latepoint-icon-map-pin', 'link' => OsRouterHelper::build_link(['addons', 'missing_locations'])),
	        array('label' => '', 'small_label' => __('Settings', 'latepoint'), 'menu_section' => 'settings'),
	        array( 'id' => 'addons', 'show_notice' => OsUpdatesHelper::is_update_available_for_addons(), 'label' => __( 'Add-ons', 'latepoint' ), 'icon' => 'latepoint-icon latepoint-icon-plus-circle2', 'link' => OsRouterHelper::build_link(['addons', 'index'])),
	        array( 'id' => 'settings', 'show_notice' => $is_update_available, 'label' => __( 'Settings', 'latepoint' ), 'icon' => 'latepoint-icon latepoint-icon-settings', 'link' => OsRouterHelper::build_link(['settings', 'general']),
	          'children' => array(
	                          array('id' => 'general', 'label' => __( 'General', 'latepoint' ), 'icon' => '', 'link' => OsRouterHelper::build_link(['settings', 'general'])),
	                          array('id' => 'schedule', 'label' => __( 'Schedule', 'latepoint' ), 'icon' => '', 'link' => OsRouterHelper::build_link(['settings', 'work_periods'])),
	                          array('id' => 'tax', 'label' => __( 'Tax', 'latepoint' ), 'icon' => '', 'link' => OsRouterHelper::build_link(['addons', 'missing_taxes'])),
	                          array('id' => 'steps', 'label' => __( 'Steps', 'latepoint' ), 'icon' => '', 'link' => OsRouterHelper::build_link(['settings', 'steps'])),
	                          array('id' => 'payments', 'label' => __( 'Payments', 'latepoint' ), 'icon' => '', 'link' => OsRouterHelper::build_link(['settings', 'payments'])),
	                          array('id' => 'notifications', 'label' => __( 'Notifications', 'latepoint' ), 'icon' => '', 'link' => OsRouterHelper::build_link(['settings', 'notifications'])),
	                          array('id' => 'roles', 'label' => __( 'Roles', 'latepoint' ), 'icon' => '', 'link' => OsRouterHelper::build_link(['settings', 'roles'])),
	                          array('id' => 'updates', 'label' => __( 'System', 'latepoint' ), 'show_notice' => $is_update_available, 'icon' => '', 'link' => OsRouterHelper::build_link(['updates', 'status'])),
	          )
	        ),
	        array( 'id' => 'processes', 'label' => __( 'Processes', 'latepoint' ), 'icon' => 'latepoint-icon latepoint-icon-server', 'link' => OsRouterHelper::build_link(['processes', 'index']),
	          'children' => array(
	                          array('label' => __('Processes', 'latepoint'), 'icon' => '', 'link' => OsRouterHelper::build_link(['processes', 'index'])),
	                          array('label' => __('Scheduled Jobs', 'latepoint'), 'icon' => '', 'link' => OsRouterHelper::build_link(['process_jobs', 'index'])),
	                          array('label' => __('Activity Log', 'latepoint'), 'icon' => '', 'link' => OsRouterHelper::build_link(['activities', 'index'])),
	                        )
	        ),
	        array( 'id' => 'integrations', 'label' => __( 'Integrations', 'latepoint' ), 'icon' => 'latepoint-icon latepoint-icon-link-2', 'link' => OsRouterHelper::build_link(['integrations', 'external_calendars']),
	          'children' => array(
	                          array('id' => 'calendars', 'label' => __( 'Calendars', 'latepoint' ), 'icon' => '', 'link' => OsRouterHelper::build_link(['integrations', 'external_calendars'])),
	                          array('id' => 'meetings', 'label' => __( 'Meetings', 'latepoint' ), 'icon' => '', 'link' => OsRouterHelper::build_link(['integrations', 'external_meeting_systems'])),
	                          array('id' => 'meetings', 'label' => __( 'Marketing', 'latepoint' ), 'icon' => '', 'link' => OsRouterHelper::build_link(['integrations', 'external_marketing_systems'])),
	          )
	        ),
		      array( 'id' => 'form_fields', 'label' => __( 'Form Fields', 'latepoint' ), 'icon' => 'latepoint-icon latepoint-icon-layers', 'link' => OsRouterHelper::build_link(['settings', 'default_form_fields'])),
	      );
				break;
			case LATEPOINT_USER_TYPE_AGENT:
	      // ---------------
	      // AGENT MENU
	      // ---------------
	      $menus = array(
	        array( 'id' => 'dashboard',  'label' => __( 'Dashboard', 'latepoint' ), 'icon' => 'latepoint-icon latepoint-icon-box', 'link' => OsRouterHelper::build_link(['dashboard', 'index'])),
	        array( 'id' => 'calendar',  'label' => __( 'Calendar', 'latepoint' ), 'icon' => 'latepoint-icon latepoint-icon-calendar', 'link' => OsRouterHelper::build_link(['calendars', 'view'])),
	        array( 'id' => 'appointments',  'label' => __( 'Appointments', 'latepoint' ), 'icon' => 'latepoint-icon latepoint-icon-inbox', 'link' => OsRouterHelper::build_link(['bookings', 'index'])),
	        array( 'id' => 'payments', 'label' => __( 'Payments', 'latepoint' ), 'icon' => 'latepoint-icon latepoint-icon-credit-card', 'link' => OsRouterHelper::build_link(['transactions', 'index'])),
	        array( 'id' => 'customers',  'label' => __( 'Customers', 'latepoint' ), 'icon' => 'latepoint-icon latepoint-icon-users', 'link' => OsRouterHelper::build_link(['customers', 'index']),
	          'children' => array(
	                          array('label' => __('Customers', 'latepoint'), 'icon' => '', 'link' => OsRouterHelper::build_link(['customers', 'index'])),
	                          array('label' => __('New Customer', 'latepoint'), 'icon' => '', 'link' => OsRouterHelper::build_link(['customers', 'new_form'])),
	                        )
	        ),
					array( 'id' => 'services', 'label' => __( 'Services', 'latepoint' ), 'icon' => 'latepoint-icon latepoint-icon-package', 'link' => OsRouterHelper::build_link(['services', 'index']),
	          'children' => array(
	                          array('label' => __( 'Services', 'latepoint' ), 'icon' => '', 'link' => OsRouterHelper::build_link(['services', 'index'])),
	                          array('label' => __( 'Categories', 'latepoint' ), 'icon' => '', 'link' => OsRouterHelper::build_link(['service_categories', 'index'])),
	          )
	        ),
	        array( 'id' => 'locations', 'label' => __( 'Locations', 'latepoint' ), 'icon' => 'latepoint-icon latepoint-icon-map-pin', 'link' => OsRouterHelper::build_link(['addons', 'missing_locations'])),
	        array( 'id' => 'settings',  'label' => __( 'My Settings', 'latepoint' ), 'icon' => 'latepoint-icon latepoint-icon-settings', 'link' => OsRouterHelper::build_link(['agents', 'edit_form'], array('id' => OsAuthHelper::get_logged_in_agent_id()) ))
	      );
				break;
		}
		/**
		 * Filters side menu items
		 *
		 * @since 4.7.0
		 * @hook latepoint_side_menu
		 *
		 * @param {array} $menus Array of side menu items in a format ['id' => '', 'label' => '', 'icon' => '', 'link' => '', 'children' => [ ['label' => '', 'icon' => '', 'link' => ''] ]
		 * @returns {array} Filtered array of side menu items
		 */
    $menus = apply_filters('latepoint_side_menu', $menus);
		self::$side_menu_items = self::filter_by_user_capabilities($menus);
		return self::$side_menu_items;
  }

	public static function filter_by_user_capabilities(array $menus): array{
		$total_menus = count($menus);
		for($i = 0; $i < $total_menus; $i++){
			if(!empty($menus[$i]['children'])){
				$menus[$i]['children'] = self::filter_by_user_capabilities($menus[$i]['children']);
			}
			if(!empty($menus[$i]['link'])){
				parse_str(parse_url($menus[$i]['link'])['query'] ?? '',$params);
				if(empty($params['route_name'])) continue; // not a controller__action route, could be custom URL

				$split_route_name = explode('__', $params['route_name']);
				if(empty($split_route_name) || count($split_route_name) != 2) continue; // not a controller__action route, could be custom URL

				$controller_name = $split_route_name[0];
				$action = $split_route_name[1];

				if(empty($controller_name) || empty($action)) continue;  // not a controller__action route, could be custom URL
		    $controller_name = str_replace('_', '', ucwords($controller_name, '_'));
		    $controller_class_name = 'Os'.$controller_name.'Controller';
				$capabilities = OsRolesHelper::get_capabilities_required_for_controller_action($controller_class_name, $action);
				if(!OsAuthHelper::get_current_user()->has_capability($capabilities)) unset($menus[$i]);
			}
		}
		return $menus;
	}

}