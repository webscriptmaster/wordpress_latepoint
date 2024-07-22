<?php 

class OsUpdatesHelper {

  public static function is_update_available(){
    $latest_version = get_option('latepoint_latest_available_version', LATEPOINT_VERSION);
    return (version_compare($latest_version, LATEPOINT_VERSION) > 0);
  }

  public static function is_update_available_for_addons(){
    return get_option('latepoint_addons_update_available', false);
  }

  public static function get_list_of_addons(){
    // connect
    $vars = array(
      '_nonce'            => wp_create_nonce('activate_licence'),
      'version'           => LATEPOINT_VERSION, 
      'domain'            => OsUtilHelper::get_site_url(),
      'marketplace'       => LATEPOINT_MARKETPLACE,
      'license_key'       => OsLicenseHelper::get_license_key(),
      'user_ip'           => OsUtilHelper::get_user_ip(),
    );
    add_filter('https_ssl_verify', '__return_false');
    $url = OsSettingsHelper::get_remote_url("/wp/addons/load_addons_list");
    
   

    $request = wp_remote_post( $url,array('body' => $vars, 'sslverify' => false));
    $addons = false;
    if( !is_wp_error($request) || wp_remote_retrieve_response_code($request) === 200){ 
      $addons = json_decode($request['body']);
    }else{
			if (is_wp_error($request)) OsDebugHelper::log('Connection error', 'list_of_addons_error', ['error' => $request->get_error_messages()]);
    }
    return $addons;
  }
  public static function get_addons_info(){
    // connect
    $vars = array(
      '_nonce'            => wp_create_nonce('activate_licence'),
      'version'           => LATEPOINT_VERSION,
      'domain'            => OsUtilHelper::get_site_url(),
      'marketplace'       => LATEPOINT_MARKETPLACE,
      'license_key'       => OsLicenseHelper::get_license_key(),
      'user_ip'           => OsUtilHelper::get_user_ip(),
    );
    add_filter('https_ssl_verify', '__return_false');
    $url = OsSettingsHelper::get_remote_url("/wp/addons/get_addons_info");



    $request = wp_remote_post( $url,array('body' => $vars, 'sslverify' => false));
    $addons_info = false;
    if( !is_wp_error($request) || wp_remote_retrieve_response_code($request) === 200){
      $addons_info = json_decode($request['body']);
    }else{
			if (is_wp_error($request)) OsDebugHelper::log('Connection error', 'load_addons_info_error', ['error' => $request->get_error_messages()]);
    }
    return $addons_info;
  }

  public static function check_addons_latest_version($addons = false){

    if(!$addons){
      $addons = self::get_list_of_addons();
    }
    $addons_to_update = [];
    if($addons){
      foreach($addons as $addon){
        $is_installed = OsAddonsHelper::is_addon_installed($addon->wp_plugin_path);
        if($is_installed){
					if( !function_exists('get_plugin_data') ) require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
          $addon_data = get_plugin_data(OsAddonsHelper::get_addon_plugin_path($addon->wp_plugin_path));
          $installed_version = (isset($addon_data['Version'])) ? $addon_data['Version'] : '1.0.0';
          if(version_compare($addon->version, $installed_version) > 0){
            $addons_to_update[] = $addon->wp_plugin_name;
          }
        }
      }
    }
    if($addons_to_update){
      update_option('latepoint_addons_update_available', true);
    }else{
      update_option('latepoint_addons_update_available', false);
    }
  }


  public static function check_plugin_latest_version(){
    // connect
    $vars = array(
      '_nonce'            => wp_create_nonce('check_version_number'),
      'version'           => LATEPOINT_VERSION, 
      'domain'            => OsUtilHelper::get_site_url(),
      'license_key'       => OsSettingsHelper::get_settings_value('license')
    );

    $url = OsSettingsHelper::get_remote_url("/wp/latest-version-number.json");

    $args = array(
      'timeout' => 15,
      'headers' => array(),
      'body' => $vars,
      'sslverify' => false
    );
   
    $request = wp_remote_post( $url, $args);
    
    if( !is_wp_error($request) || wp_remote_retrieve_response_code($request) === 200){
      $version_info = json_decode($request['body'], true);
      if(!isset($version_info['version'])) return;
      update_option('latepoint_latest_available_version', $version_info['version']);
    }
  }



  /*
   * $res empty at this step
   * $action 'plugin_information'
   * $args stdClass Object ( [slug] => woocommerce [is_ssl] => [fields] => Array ( [banners] => 1 [reviews] => 1 [downloaded] => [active_installs] => 1 ) [per_page] => 24 [locale] => en_US )
   */
  public static function modify_plugin_info($res, $action, $args){
    $plugin_slug = 'latepoint';
    // do nothing if this is not about getting plugin information
    if( 'plugin_information' !== $action || $plugin_slug !== $args->slug) {
      return false;
    }

    // trying to get from cache first
    $remote = get_transient( 'latepoint_update_' . $plugin_slug );
    if( false == $remote ) {

      // info.json is the file with the actual plugin information on your server
      $remote = wp_remote_get( OsSettingsHelper::get_remote_url("/wp/plugin-info/latepoint.json"), array(
        'timeout' => 10,
        'sslverify' => false,
        'headers' => array(
          'Accept' => 'application/json'
        ) )
      );

      if ( ! is_wp_error( $remote ) && isset( $remote['response']['code'] ) && $remote['response']['code'] == 200 && ! empty( $remote['body'] ) ) {
        set_transient( 'latepoint_update_' . $plugin_slug, $remote, 43200 ); // 12 hours cache
      }
    
    }

    if( ! is_wp_error( $remote ) && isset( $remote['response']['code'] ) && $remote['response']['code'] == 200 && ! empty( $remote['body'] ) ) {

      $remote = json_decode( $remote['body'] );
      $res = new stdClass();

      $res->name = $remote->name;
      $res->slug = $remote->slug;
      $res->version = $remote->version;
      $res->tested = $remote->tested;
      $res->requires = $remote->requires;
      $res->author = $remote->author;
      $res->author_profile = $remote->author_homepage;
      $res->download_link = '';
      $res->trunk = '';
      $res->requires_php = $remote->requires_php;
      $res->last_updated = $remote->last_updated;
      $res->sections = array(
        // 'description' => $remote->sections->description,
        // 'installation' => $remote->sections->installation,
        'changelog' => $remote->sections->changelog
        // you can add your custom sections (tabs) here
      );

      // in case you want the screenshots tab, use the following HTML format for its content:
      // <ol><li><a href="IMG_URL" target="_blank"><img src="IMG_URL" alt="CAPTION" /></a><p>CAPTION</p></li></ol>
      // if( !empty( $remote->sections->screenshots ) ) {
      //   $res->sections['screenshots'] = $remote->sections->screenshots;
      // }

      $res->banners = array(
        'low' => 'https://latepoint.s3.amazonaws.com/codecanyon/wp-plugin-head.jpg',
        'high' => 'https://latepoint.s3.amazonaws.com/codecanyon/wp-plugin-head-2x.jpg'
      );
      return $res;

    }

    return false;
  }

  public static function modify_plugin_update_message( $plugin_data, $new_data ) {
    if ( isset( $plugin_data['update'] ) && $plugin_data['update'] ) {
      echo '<br /><br/>' . sprintf(
        __('You can install this update from your <a href="%s">LatePoint Settings</a> page', 'latepoint'),
        OsRouterHelper::build_link(['updates', 'status'])
      );
    }
  }

  public static function modify_addon_update_message( $plugin_data, $new_data ) {
    if ( isset( $plugin_data['update'] ) && $plugin_data['update'] ) {
      echo '<br /><br/>' . sprintf(
        __('You can install this update from your <a href="%s">LatePoint Addons</a> page', 'latepoint'),
        OsRouterHelper::build_link(['addons', 'index'])
      );
    }
  }

  public static function wp_native_check_if_update_available($transient){
    $remote = wp_remote_get( OsSettingsHelper::get_remote_url("/wp/plugin-info/latepoint.json"), array(
      'timeout' => 10,
      'sslverify' => false,
      'headers' => array(
        'Accept' => 'application/json'
      ) )
    );
    if( ! is_wp_error( $remote ) && isset( $remote['response']['code'] ) && $remote['response']['code'] == 200 && ! empty( $remote['body'] ) ) {
      $remote = json_decode( $remote['body'] );
      $plugin_data = get_plugin_data(OsAddonsHelper::get_addon_plugin_path('latepoint/latepoint.php'));
      if(version_compare($remote->version, $plugin_data['Version']) > 0){
        $obj = new stdClass();
        $obj->slug = 'latepoint';
        $obj->new_version = $remote->version;
        $obj->url = '';
        $obj->package = '';
        $transient->response['latepoint/latepoint.php'] = $obj;
      }
    }

    $addons = OsUpdatesHelper::get_list_of_addons();
    $installed_plugins = get_plugins();
    $installed_latepoint_addons = $installed_plugins ? array_keys($installed_plugins) : [];
    if($addons){
      foreach($addons as $addon){
        if(in_array($addon->wp_plugin_path, $installed_latepoint_addons)){
          $addon_data = get_plugin_data(OsAddonsHelper::get_addon_plugin_path($addon->wp_plugin_path));
          $installed_version = (isset($addon_data['Version'])) ? $addon_data['Version'] : '1.0.0';
          if(version_compare($addon->version, $installed_version) > 0){
            $obj = new stdClass();
            $obj->slug = $addon->wp_plugin_name;
            $obj->new_version = $addon->version;
            $obj->url = '';
            $obj->package = '';
            $transient->response[$addon->wp_plugin_path] = $obj;
          }
        }
      }
    }

    return $transient;
  }

}