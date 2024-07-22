<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}


if ( ! class_exists( 'OsDebugController' ) ) :


  class OsDebugController extends OsController {



    function __construct(){
      parent::__construct();

      $this->views_folder = LATEPOINT_VIEWS_ABSPATH . 'debug/';
      $this->vars['page_header'] = __('LatePoint Status', 'latepoint');
    }

		function reset_plugin_db_version(){
			delete_option('latepoint_db_version');
			if($this->get_return_format() == 'json'){
        $this->send_json(array('status' => LATEPOINT_STATUS_SUCCESS, 'message' => __('DB Version was reset', 'latepoint')));
      }
		}

		function reset_addon_db_version(){
			delete_option($this->params['plugin_name'] . '_addon_db_version');
			if($this->get_return_format() == 'json'){
        $this->send_json(array('status' => LATEPOINT_STATUS_SUCCESS, 'message' => __('DB Version was reset', 'latepoint')));
      }
		}

    function status(){

      $this->vars['page_header'] = OsMenuHelper::get_menu_items_by_id('settings');

			$addons = OsUpdatesHelper::get_list_of_addons();
      $this->vars['addons'] = $addons;

			$this->set_layout('admin');
      $this->format_render(__FUNCTION__);
    }

	}



endif;