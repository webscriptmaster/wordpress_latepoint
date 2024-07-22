<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}


if ( ! class_exists( 'OsAddonsController' ) ) :


  class OsAddonsController extends OsController {



    function __construct(){
      parent::__construct();

      $this->views_folder = LATEPOINT_VIEWS_ABSPATH . 'addons/';
      $this->vars['page_header'] = __('Add-ons', 'latepoint');
    }

    function delete_addon(){
      if(!isset($this->params['addon_name']) || empty($this->params['addon_name'])) return;
      delete_plugins($this->params['addon_name']);
      if($this->get_return_format() == 'json'){
        $this->send_json(array('status' => LATEPOINT_STATUS_SUCCESS, 'message' => __('Addon deleted', 'latepoint')));
      }
    }

    function missing_locations(){
			$this->vars['missing_label'] = __('Install Locations Add-on', 'latepoint');
      $this->vars['page_header'] = __('Locations', 'latepoint');
      $this->format_render('missing');
    }

    function missing_taxes(){
      $this->vars['page_header'] = OsMenuHelper::get_menu_items_by_id('settings');
			$this->vars['missing_label'] = __('Install Taxes Add-on', 'latepoint');
      $this->format_render('missing');
    }


    function deactivate_addon(){
      if(!isset($this->params['addon_name']) || empty($this->params['addon_name'])) return;

      $result = OsAddonsHelper::deactivate_addon( $this->params['addon_path'] );
      $status = is_wp_error( $result ) ? LATEPOINT_STATUS_ERROR : LATEPOINT_STATUS_SUCCESS;
      $response_html = is_wp_error($result) ? $result->get_error_message() : __('Addon deactivated', 'latepoint');

      if($this->get_return_format() == 'json'){
        $this->send_json(array('status' => $status, 'message' => $response_html));
      }
    }

    function activate_addon(){
      if(!isset($this->params['addon_path']) || empty($this->params['addon_path'])) return;

      $result = OsAddonsHelper::activate_addon( $this->params['addon_path'] );
      $status = is_wp_error( $result ) ? LATEPOINT_STATUS_ERROR : LATEPOINT_STATUS_SUCCESS;
      $response_html = is_wp_error($result) ? $result->get_error_message() : __('Addon activated', 'latepoint');
      if($this->get_return_format() == 'json'){
        $this->send_json(['status' => $status, 'message' => $response_html]);
      }
    }

    function install_addon(){
      if(!isset($this->params['addon_name']) || empty($this->params['addon_name'])) return;

      $addon_name = $this->params['addon_name'];

      $license = OsLicenseHelper::get_license_info();

      if(OsLicenseHelper::is_license_active()){
        $addon_info = OsAddonsHelper::get_addon_download_info($addon_name);
        $result = OsAddonsHelper::install_addon($addon_info);
        if(is_wp_error( $result )){
          $status = LATEPOINT_STATUS_ERROR;
          $response_html = $result->get_error_message();
          $code = '500';
        }else{
          $status = LATEPOINT_STATUS_SUCCESS;
          $code = '200';
          $response_html = __('Addon installed successfully.', 'latepoint');
        }
      }else{
        $this->vars['license'] = $license;
        $status = LATEPOINT_STATUS_ERROR;
        $response_html = $this->render(LATEPOINT_VIEWS_ABSPATH.'updates/_license_form', 'none');
        $code = '404';
      }

      if($this->get_return_format() == 'json'){
        $this->send_json(array('status' => $status, 'code' => $code, 'message' => $response_html));
      }

    }

		function dismiss_message(){
			$message_id = $this->params['message_id'];
			$dismissed_messages = get_user_meta(get_current_user_id(), 'latepoint_dismissed_messages', true);
			if(empty($dismissed_messages)){
				$dismissed_messages = [$message_id];
			}else{
				$dismissed_messages[] = $message_id;
				$dismissed_messages = array_unique($dismissed_messages);
			}
			update_user_meta(get_current_user_id(), 'latepoint_dismissed_messages', $dismissed_messages);
      $this->send_json(array('status' => LATEPOINT_STATUS_SUCCESS, 'message' => 'Message dismissed'));
		}


    function index(){

      $this->format_render(__FUNCTION__);
    }

    function load_addons_list(){
      $addons_info = OsUpdatesHelper::get_addons_info();
      $this->vars['addons'] = $addons_info->addons;
      $this->vars['categories'] = $addons_info->categories;

			$messages = [];
			if(!empty($addons_info->messages)){
				$dismissed_messages = get_user_meta(get_current_user_id(), 'latepoint_dismissed_messages', true);
				foreach($addons_info->messages as $message){
					if(empty($dismissed_messages) || !in_array($message->id, $dismissed_messages)) $messages[] = $message;
				}
			}
      $this->vars['messages'] = $messages;



      OsUpdatesHelper::check_addons_latest_version($addons_info);
      $this->format_render(__FUNCTION__);
    }
	}



endif;