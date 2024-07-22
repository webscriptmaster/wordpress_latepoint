<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}


if (!class_exists('OsNotificationsController')) :


	class OsNotificationsController extends OsController {

		function __construct() {
			parent::__construct();

			$this->views_folder = LATEPOINT_VIEWS_ABSPATH . 'notifications/';
			$this->vars['page_header'] = OsMenuHelper::get_menu_items_by_id('notifications');
			$this->vars['breadcrumbs'][] = array('label' => __('Notifications', 'latepoint'), 'link' => OsRouterHelper::build_link(OsRouterHelper::build_route_name('notifications', 'settings')));
		}

		public function templates_index() {
			$action_id = $this->params['action_id'];
			$action_type = $this->params['action_type'];

			$templates = OsNotificationsHelper::load_templates_for_action_type($action_type);

			switch ($action_type) {
				case 'send_email':
					$this->vars['heading'] = __('Email Notification Templates', 'latepoint');
					break;
				case 'send_sms':
					$this->vars['heading'] = __('SMS Notification Templates', 'latepoint');
					break;
			}

			$this->vars['action_type'] = $action_type;
			$this->vars['action_id'] = $action_id;

			$this->vars['selected_template_id'] = empty($templates) ? '' : $templates[0]['id'];
			$this->vars['templates'] = $templates;
			$this->format_render(__FUNCTION__);

		}


	}
endif;