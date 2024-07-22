<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}


if ( ! class_exists( 'OsStepsController' ) ) :


  class OsStepsController extends OsController {

    private $booking;

    function __construct(){
      parent::__construct();
      $this->action_access['customer'] = array_merge($this->action_access['customer'], ['start_from_booking_intent']);
      $this->action_access['public'] = array_merge($this->action_access['public'], ['start', 'get_step', 'reload_booking_summary']);

      $this->views_folder = LATEPOINT_VIEWS_ABSPATH . 'steps/';
      $this->vars['page_header'] = __('Appointments', 'latepoint');
      $this->vars['breadcrumbs'][] = array('label' => __('Appointments', 'latepoint'), 'link' => OsRouterHelper::build_link(['bookings', 'pending_approval'] ) );
    }



		function reload_booking_summary(){
      OsStepsHelper::set_booking_object($this->params['booking']);

			$this->vars['booking'] = OsStepsHelper::$booking_object;
			$this->vars['price_breakdown_rows'] = OsBookingHelper::generate_price_breakdown_rows(OsStepsHelper::$booking_object, true, ['balance', 'payments']);

      if($this->get_return_format() == 'json'){
        $response_html = $this->render($this->views_folder.'partials/_booking_summary', 'none');
        echo wp_send_json(['status' => 'success', 'message' => $response_html]);
        exit();
      }else{
        echo $this->render($this->views_folder . 'partials/_booking_summary', $this->get_layout());
      }
		}

    public function start_from_booking_intent(){
      $booking_intent_key = $this->params['booking_intent_key'];
      $booking_intent = new OsBookingIntentModel();
      $booking_intent = $booking_intent->where(['intent_key' => $booking_intent_key])->set_limit(1)->get_results_as_models();

      $steps_to_preload = [];
      
      if($booking_intent){
        if($booking_intent->booking_id){
          // if booking is created - load it
          OsStepsHelper::load_booking_object($booking_intent->booking_id);
          $active_step_name = 'confirmation';
        }else{
          OsStepsHelper::set_booking_object(json_decode($booking_intent->booking_data, true));
          OsStepsHelper::set_restrictions(json_decode($booking_intent->restrictions_data, true));
          $active_step_name = 'payment';
        }
        OsStepsHelper::$booking_object->intent_key = $booking_intent->intent_key;

        OsStepsHelper::get_step_names_in_order();
        OsStepsHelper::remove_already_selected_steps();

        // we want to send user back to a step prior to payment, because sometimes payment step is a redirect (stripe checkout, mollie etc...)
        if($active_step_name == 'payment'){
          $steps = OsStepsHelper::get_step_names_in_order();
          // if payment step is not first - get the step before, if its first or not found - use payment step
          $active_step_name = array_search('payment', $steps) ? $steps[array_search('payment', $steps) - 1] : $active_step_name;
        }

        $steps_models = OsStepsHelper::load_steps_as_models(OsStepsHelper::get_step_names_in_order());
        $active_step_model = $steps_models[0];
        foreach($steps_models as $step_model){
          if($step_model->name == $active_step_name){
            $active_step_model = $step_model;
            break;
          }else{
            $steps_to_preload[] = $step_model->name;
          }
        }


        // booking exists - only load confirmation step
        $this->vars['steps_to_preload'] = ($active_step_name == 'confirmation') ? [] : $steps_to_preload;

        $this->vars['show_next_btn'] = OsStepsHelper::can_step_show_next_btn($active_step_model->name, OsStepsHelper::$booking_object, OsStepsHelper::$restrictions);
        $this->vars['show_prev_btn'] = OsStepsHelper::can_step_show_prev_btn($active_step_model->name, OsStepsHelper::$booking_object, OsStepsHelper::$restrictions);
        $this->vars['steps_models'] = $steps_models;
        $this->vars['active_step_model'] = $active_step_model;

        $this->vars['current_step'] = $active_step_model->name;
        $this->vars['booking'] = OsStepsHelper::$booking_object;
				$this->vars['price_breakdown_rows'] = OsBookingHelper::generate_price_breakdown_rows(OsStepsHelper::$booking_object, true);
        $this->vars['restrictions'] = OsStepsHelper::$restrictions;
        $this->set_layout('none');

        $lightbox_class = '';
        // if($this.data('hide-summary') != 'yes') lightbox_class+= ' latepoint-with-summary';
        // if($this.data('hide-side-panel') == 'yes') lightbox_class+= ' latepoint-hide-side-panel';
        $this->format_render('start', array(), array('step' => $active_step_model->name, 'lightbox_class' => ''));
      }else{
        $this->send_json(array('status' => LATEPOINT_STATUS_ERROR, 'message' => __('Invalid booking intent key', 'latepoint')));
      }

    }

    public function start($restrictions = false, $output = true){
      OsStepsHelper::set_booking_object();
      if((!$restrictions || empty($restrictions)) && isset($this->params['restrictions'])) $restrictions = $this->params['restrictions'];
      OsStepsHelper::set_restrictions($restrictions);
      OsStepsHelper::get_step_names_in_order();
      OsStepsHelper::remove_already_selected_steps();

      $steps_models = OsStepsHelper::load_steps_as_models(OsStepsHelper::get_step_names_in_order());

      $active_step_model = $steps_models[0];

      // if is payment step - check if total is not $0 and if it is skip payment step
      if(OsStepsHelper::should_step_be_skipped($active_step_model->name)){
        $active_step_name = OsStepsHelper::get_next_step_name($active_step_model->name);
        $active_step_model = new OsStepModel($active_step_name);
      }

      $this->vars['show_next_btn'] = OsStepsHelper::can_step_show_next_btn($active_step_model->name, OsStepsHelper::$booking_object, OsStepsHelper::$restrictions);
      $this->vars['show_prev_btn'] = OsStepsHelper::can_step_show_prev_btn($active_step_model->name, OsStepsHelper::$booking_object, OsStepsHelper::$restrictions);
      $this->vars['steps_models'] = $steps_models;
      $this->vars['active_step_model'] = $active_step_model;

      $this->vars['current_step'] = $active_step_model->name;
      $this->vars['booking'] = OsStepsHelper::$booking_object;
			$this->vars['price_breakdown_rows'] = OsBookingHelper::generate_price_breakdown_rows(OsStepsHelper::$booking_object, true, ['balance', 'payments']);
      $this->vars['restrictions'] = OsStepsHelper::$restrictions;
      $this->set_layout('none');

      LatePoint\Cerber\Router::smell();

      if($output){
        $this->format_render(__FUNCTION__, array(), array('step' => $active_step_model->name));
      }else{
        return $this->format_render_return(__FUNCTION__, array(), array('step' => $active_step_model->name));
      }
    }


    public function get_step(){
    	if(!OsStepsHelper::is_valid_step($this->params['current_step'])) return false;
      OsStepsHelper::set_booking_object($this->params['booking']);
      OsStepsHelper::set_restrictions($this->params['restrictions']);
      OsStepsHelper::get_step_names_in_order();
      OsStepsHelper::remove_already_selected_steps();
      // Check if a valid step name
      $current_step = $this->params['current_step'];
      if(!in_array($current_step, OsStepsHelper::get_step_names_in_order())) return false;
      $step_direction = isset($this->params['step_direction']) ? $this->params['step_direction'] : 'next';
      $step_name_to_load = false;
      switch ($step_direction) {
        case 'next':
		      do_action('latepoint_process_step', $current_step, OsStepsHelper::$booking_object);
		      $step_name_to_load = OsStepsHelper::get_next_step_name($current_step);
          break;
        case 'prev':
		      $step_name_to_load = OsStepsHelper::get_prev_step_name($current_step);
          break;
        case 'specific':
	        $step_name_to_load = OsStepsHelper::should_step_be_skipped($current_step) ? OsStepsHelper::get_next_step_name($current_step) : $current_step;
          break;
      }
      if($step_name_to_load){
  	    do_action('latepoint_load_step', $step_name_to_load, OsStepsHelper::$booking_object, 'json', OsStepsHelper::$restrictions);
      }
    }





  }


endif;
