<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}


if ( ! class_exists( 'OsUpdatesController' ) ) :


  class OsUpdatesController extends OsController {



    function __construct(){
      parent::__construct();

      $this->views_folder = LATEPOINT_VIEWS_ABSPATH . 'updates/';
      $this->vars['page_header'] = OsMenuHelper::get_menu_items_by_id('settings');
    }

    function status(){

      $this->vars['license'] = OsLicenseHelper::get_license_info();
      $this->vars['is_license_active'] = OsLicenseHelper::is_license_active();

      $this->format_render(__FUNCTION__);
    }


    public function remove_license(){
      LatePoint\Cerber\Router::release();
      if($this->get_return_format() == 'json'){
        $this->send_json(array('status' => LATEPOINT_STATUS_SUCCESS, 'message' => __('License Deactivated')));
      }
    }

    public function save_license_information(){
      $license_data = $this->params['license'];

      $verify_license_key_result = OsLicenseHelper::verify_license_key($license_data);
      
      if($this->get_return_format() == 'json'){
        $this->send_json(array('status' => $verify_license_key_result['status'], 'message' => $verify_license_key_result['message']));
      }
    }



    function update_plugin(){
      // connect
      $vars = array(
        '_nonce'            => wp_create_nonce('activate_licence'),
        'version'           => LATEPOINT_VERSION, 
        'domain'            => OsUtilHelper::get_site_url(),
        'license_key'       => OsLicenseHelper::get_license_key(),
        'user_ip'           => OsUtilHelper::get_user_ip(),
      );

      $url = OsSettingsHelper::get_remote_url("/wp/latest-version-info.json");

      $args = array(
        'timeout' => 15,
        'headers' => array(),
        'body' => $vars,
        'sslverify' => false
      );
     
      $request = wp_remote_post( $url, $args);
      
      if( !is_wp_error($request) || wp_remote_retrieve_response_code($request) === 200){
        $version_info = json_decode($request['body'], true);
        $plugin_info = ['url' => $version_info['update_url'], 'plugin_path' => 'latepoint/latepoint.php', 'version' => $version_info['version']];

        $result = OsAddonsHelper::install_addon($plugin_info);
        if(!is_wp_error($result)){
          $status = LATEPOINT_STATUS_SUCCESS;
          $response_html = 'Plugin Updated';
        }else{
          $response_html = $result->get_error_message();;
          $status = LATEPOINT_STATUS_ERROR;
        }
      }else{
				if (is_wp_error($request)) OsDebugHelper::log('Update plugin error', 'update_plugin_error', ['error' => $request->get_error_messages()]);
        $response_html = 'Error! OJF9399';
        $status = LATEPOINT_STATUS_ERROR;
      }

      if($this->get_return_format() == 'json'){
        $this->send_json(array('status' => $status, 'message' => $response_html));
      }
    }


    function check_version_status(){
      // connect
      $vars = array(
        '_nonce'            => wp_create_nonce('activate_licence'),
        'version'           => LATEPOINT_VERSION, 
        'domain'            => OsUtilHelper::get_site_url(),
        'license_key'       => OsLicenseHelper::get_license_key(),
        'user_ip'           => OsUtilHelper::get_user_ip(),
      );

      $url = OsSettingsHelper::get_remote_url("/wp/latest-version-info.json");

      $args = array(
        'timeout' => 15,
        'headers' => array(),
        'body' => $vars,
        'sslverify' => false
      );
     
      $request = wp_remote_post( $url, $args);
      if( !is_wp_error($request) || wp_remote_retrieve_response_code($request) === 200){
        $version_info = json_decode($request['body'], true);
        $this->vars['version_info'] = $version_info;
        if(version_compare($version_info['version'], LATEPOINT_VERSION) > 0){
          update_option('latepoint_latest_available_version', $version_info['version']);
        }
        $response_html = $this->render($this->get_view_uri('check_version_status'), 'none');
        $status = LATEPOINT_STATUS_SUCCESS;
      }else{
				if (is_wp_error($request)) OsDebugHelper::log('Check version status error', 'check_version_error', ['error' => $request->get_error_messages()]);
        $response_html = 'Error! 834LFIDF83';
        $status = LATEPOINT_STATUS_ERROR;
      }

      if($this->get_return_format() == 'json'){
        $this->send_json(array('status' => $status, 'message' => $response_html));
      }
    }

    function get_updates_log(){

      // connect
      $vars = array(
        '_nonce'            => wp_create_nonce('activate_licence'),
        'version'           => LATEPOINT_VERSION, 
        'domain'            => OsUtilHelper::get_site_url(),
        'user_ip'           => OsUtilHelper::get_user_ip(),
      );

      $url = OsSettingsHelper::get_remote_url("/wp/get-changelog");

      $args = array(
        'timeout' => 15,
        'headers' => array(),
        'body' => $vars,
        'sslverify' => false
      );
     
      $request = wp_remote_post( $url, $args );
      
      if( !is_wp_error($request) || wp_remote_retrieve_response_code($request) === 200){ 
        $response_html = $request['body'];
        $status = LATEPOINT_STATUS_SUCCESS;
      }else{
				if (is_wp_error($request)) OsDebugHelper::log('Error getting changelog', 'changelog_error', ['error' => $request->get_error_messages()]);
        $response_html = 'Error! 8346HS73';
        $status = LATEPOINT_STATUS_ERROR;
      }

      if($this->get_return_format() == 'json'){
        $this->send_json(array('status' => $status, 'message' => $response_html));
      }

    }





	}



endif;