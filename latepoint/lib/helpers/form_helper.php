<?php 

class OsFormHelper {

  public static function atts_string_from_array($atts = array(), $join_atts = array()){
    $atts_str = '';
    if(!empty($atts)){
      if(isset($atts['add_string_to_id'])) unset($atts['add_string_to_id']);
      if(isset($atts['skip_id'])) unset($atts['skip_id']);
      foreach($atts as $key => $value){
        if(isset($join_atts[$key])){
          $value.= ' '.$join_atts[$key];
          unset($join_atts[$key]);
        }
        if(!is_array($value)) $atts_str.= $key.'="'.$value.'" ';
      }
    }
    if(!empty($join_atts)){
      foreach($join_atts as $key => $value){
        $atts_str.= $key.'="'.$value.'" ';
      }
    }
    return $atts_str;
  }


  public static function toggler_group_field($name, $values, $selected_values = [], $atts = [], $size = 'normal'){
    if(!$selected_values) $selected_values = [];
    $html = '';
		// hidden field needed to clear the value when no checkboxes are selected
    $html.= OsFormHelper::hidden_field($name, '');
    foreach($values as $value => $label){
      $html.= '<div class="os-form-group os-form-toggler-group">';
        $checked_attr = in_array($value, $selected_values) ? 'checked' : '';
        $id = self::name_to_id($name.'_'.$value);
        $status = in_array($value, $selected_values) ? 'on' : 'off';
        $html.= '<input type="checkbox" name="'.$name.'[]" value="'.$value.'" '.$checked_attr.' '.self::atts_string_from_array($atts, ['id' => $id, 'class' => 'os-form-checkbox']).' style="display: none;"/>';
        $html.= '<div class="os-toggler '.$status.' size-'.$size.'" data-is-string-value="true" data-for="'.$id.'"><div class="toggler-rail"><div class="toggler-pill"></div></div></div>';
				$html.= '<div class="os-toggler-label-w">';
	        $html.= '<label>'.$label.'</label>';
				$html.= '</div>';
      $html.= '</div>';
    }
    return $html;
  }



	/**
	 * @param string $name
	 * @param string $label
	 * @param bool $is_active
	 * @param $controlledToggleId
	 * @param $size
	 * @return string
	 */
  public static function toggler_radio_field(string $name, string $label, string $value, bool $is_active, $controlledToggleId = false, $size = false, $atts = []): string{
		if(!$size) $size = 'normal';
    $status = $is_active ? 'on' : 'off';
    $controlledToggleHtml = $controlledToggleId ? 'data-controlled-toggle-id="'.$controlledToggleId.'"' : '';
    $html = '';
    $id = self::name_to_id($name.'_'.$value);
		$extra = (empty($atts['sub_label'])) ? '' : ' with-sub-label';
    if($label) $html.= '<div class="os-form-group os-form-toggler-group '.$extra.' size-'.$size.'">';
      $html.= OsFormHelper::radio_field($name, $value, $is_active, ['id' => $id]);
      $html.= '<div '.$controlledToggleHtml.' class="os-toggler os-toggler-radio '.$status.' size-'.$size.'" data-is-string-value="true" data-for="'.$id.'"><div class="toggler-rail"><div class="toggler-pill"></div></div></div>';
			if($label){
				$html.= '<div class="os-toggler-label-w">';
		      $html.= '<label>'.$label.'</label>';
					if(!empty($atts['sub_label'])) $html.= '<span>'.$atts['sub_label'].'</span>';
				$html.= '</div>';
			}
    if($label) $html.= '</div>';
    return $html;
  }

	/**
	 * @param string $name
	 * @param string $label
	 * @param bool $is_active
	 * @param $controlledToggleId
	 * @param $size
	 * @return string
	 */
  public static function toggler_field(string $name, string $label, bool $is_active, $controlledToggleId = false, $size = false, $atts = []): string{
		if(!$size) $size = 'normal';
    $status = $is_active ? 'on' : 'off';
    $value = $is_active ? 'on' : 'off';
    $controlledToggleHtml = $controlledToggleId ? 'data-controlled-toggle-id="'.$controlledToggleId.'"' : '';
    $html = '';
    $id = self::name_to_id($name);
		$extra = (empty($atts['sub_label'])) ? '' : ' with-sub-label';
    if($label) $html.= '<div class="os-form-group os-form-toggler-group '.$extra.' size-'.$size.'">';
      $html.= OsFormHelper::hidden_field($name, $value, ['id' => $id]);
      $html.= '<div '.$controlledToggleHtml.' class="os-toggler '.$status.' size-'.$size.'" data-is-string-value="true" data-for="'.$id.'"><div class="toggler-rail"><div class="toggler-pill"></div></div></div>';
			if($label){
				$html.= '<div class="os-toggler-label-w">';
		      $html.= '<label>'.$label.'</label>';
					if(!empty($atts['sub_label'])) $html.= '<span>'.$atts['sub_label'].'</span>';
				$html.= '</div>';
			}
    if($label) $html.= '</div>';
    return $html;
  }


  public static function media_uploader_field($name, $post_id = 0, $label_set_str = '', $label_remove_str = '', $value_image_id = false, $atts = array(), $wrapper_atts = array(), $is_avatar = false){
    $upload_link = esc_url( get_upload_iframe_src( 'image', $post_id ) );
    $img_html = '';
    $has_image_class = '';
    $label_str = $label_set_str;

    // Image is set
    if($value_image_id){
      $image_url = OsImageHelper::get_image_url_by_id($value_image_id);
      $img_html = '<img src="'.$image_url.'"/>';
      $has_image_class = 'has-image';
      $label_str = $label_remove_str;
    }

    $is_avatar_class = $is_avatar ? ' is-avatar ' : '';
    $html = '';
    $html.= '<div class="os-image-selector-w '.$is_avatar_class.'">';
      $html.= '<a href="'.$upload_link.'" data-label-remove-str="'.$label_remove_str.'" data-label-set-str="'.$label_set_str.'"'.self::atts_string_from_array($wrapper_atts, ['class' => "os-image-selector-trigger"]).'>';
        $html.= '<div class="os-image-container '.$has_image_class.'">'.$img_html.'</div>';
        $html.= '<div class="os-image-selector-text"><span class="os-text-holder">'.$label_str.'</span></div>';
      $html.= '</a>';
      $html.= '<input type="hidden" name="'.$name.'" value="'.esc_attr($value_image_id).'" '.self::atts_string_from_array($atts, ['class' => 'os-image-id-holder']).'/>';
    $html.= '</div>';
    return $html;
  }

  public static function file_upload_field($name, $label, $value = false, $atts = [], $wrapper_atts = []){
		$accepted_formats = '.jpg,.jpeg,.png,.gif,.ico,.pdf,.doc,.docx,.ppt,.pptx,.pps,.ppsx,.odt,.xls,.xlsx,.PSD,.mp3,.m4a,.ogg,.wav,.mp4,.m4v,.mov,.wmv,.avi,.mpg,.ogv,.3gp,.3g2';
    // generate id if not set
    if(!isset($atts['id']) && !isset($atts['skip_id'])) $atts['id'] = self::name_to_id($name, $atts);
		$html = '';
    if(!empty($wrapper_atts)) $html.= '<div '.self::atts_string_from_array($wrapper_atts).'>';
	    $html.= '<div class="os-form-group os-form-file-upload-group">';
			if($label) $html.= '<label for="'.$name.'">'.$label.'</label>';
			$html.= '<a target="_blank" href="'.$value.'" class="os-uploaded-file-info '.(!empty($value) ? 'is-uploaded' : '').'" style="'.(empty($value) ? 'display: none' : '').'">
								<div class="uf-icon"><i class="latepoint-icon latepoint-icon-file-text"></i></div>
								<div class="uf-data">
									<div class="uf-name">'.(!empty($value) ? basename($value) : '').'</div>
								</div>
								<div class="uf-remove"><i class="latepoint-icon latepoint-icon-cross"></i></div>
							</a>';
				$html.= '<div class="os-upload-file-input-w" style="'.($value ? 'display: none' : '').'">';
		      $html.= '<input type="file" accept="'.$accepted_formats.'" name="'.$name.'"  multiple="false" '.self::atts_string_from_array($atts).'/>';
					if($value) $html.= self::hidden_field($name, $value);
				$html.= '</div>';
	    $html.= '</div>';
		if(!empty($wrapper_atts)) $html.= '</div>';
    return $html;
  }

  public static function generate_select_options_from_custom_field($options){
    if(!empty($options)){
      return preg_split('/\r\n|\r|\n/', $options);
    }else{
      return [];
    }
  }

	/**
	 * @param $name
	 * @param $id
	 * @param $label
	 * @param $content
	 * @param $atts
	 * @return string
	 *
	 * Same as wp_editor_field but returns the html instead of echoing it
	 */
	public static function wp_editor_field_return($name, $id, $label, $content, $atts = array()){
			ob_start();

			self::wp_editor_field( $name, $id, $label, $content, $atts );

			$html = ob_get_clean();

			return $html;
	}

  public static function wp_editor_field($name, $id, $label, $content, $atts = array()){
    $editor_height = isset($atts['editor_height']) ? $atts['editor_height'] : 300;
    echo '<div class="os-form-group os-form-control-wp-editor-group">';
      echo '<label for="'.$name.'">'.$label.'</label>';
      wp_editor($content, $id, ['textarea_name' => $name, 'media_buttons' => false, 'editor_height' => $editor_height]);
    echo '</div>';
  }


  public static function textarea_field($name, $label, $value = '', $atts = array(), $wrapper_atts = array()){
    $extra_class = ' os-form-group-transparent';
		if(isset($atts['theme'])){
			switch($atts['theme']){
				case 'bordered':
					$extra_class = ' os-form-group-bordered';
					break;
				case 'right-aligned':
					$extra_class = ' os-form-group-right-aligned';
					break;
				case 'simple':
					$extra_class = ' os-form-group-simple';
					unset($atts['theme']);
					break;
			}
		}
    // generate id if not set
    if(!isset($atts['id']) && !isset($atts['skip_id'])) $atts['id'] = self::name_to_id($name, $atts);
    if($value) $extra_class.= ' has-value';
    if(empty($label)) $extra_class.= ' no-label';

		// validations
	  if(!empty($atts['validate'])){
			$validate_html = 'data-os-validate="'.esc_attr(implode(' ', $atts['validate'])).'"';
	  }else{
			$validate_html = '';
	  }
		unset($atts['validate']);
    $html = '<div '.self::atts_string_from_array($wrapper_atts).'>';
      $html.= '<div '.self::atts_string_from_array(array('class' => 'os-form-group os-form-textfield-group os-form-textarea-group'.$extra_class)).'>';
        if($label) $html.= '<label for="'.$atts['id'].'">'.$label.'</label>';
        $placeholder = (isset($atts['placeholder'])) ? $atts['placeholder'] : $label;
        $html.= '<textarea '.$validate_html.' type="text" placeholder="'.esc_attr($placeholder).'" name="'.$name.'" '.self::atts_string_from_array($atts, ['class' => 'os-form-control']).'>'.$value.'</textarea>';
      $html.= '</div>';
    $html.= '</div>';
    return $html;
  }

  public static function service_selector_adder_field($name, $label, $add_label, $options = array(), $value = '', $atts = array(), $wrapper_atts = array()){
    $html = '<div '.self::atts_string_from_array($wrapper_atts, ['class' => 'os-form-group os-form-select-group os-form-group-transparent service-selector-adder-field-w']).'>';
      if($label) $html.= '<label for="'.$name.'">'.$label.'</label>';
      $html.= '<div class="selector-adder-w">';
        $html.= '<select name="'.$name.'" '.self::atts_string_from_array($atts, ['class' => 'os-form-control']).' data-select-source="'.OsRouterHelper::build_route_name('service_categories', 'list_for_select').'">';
        foreach($options as $option){
          if(isset($option['value']) && isset($option['label'])){
            $selected = ($value == $option['value']) ? 'selected' : '';
            $html.= '<option value="'.$option['value'].'" '.$selected.'>'.$option['label'].'</option>';
          }
        }
        $html.='</select>';
        $html.='<button class="latepoint-btn latepoint-btn-primary" data-os-action="'.OsRouterHelper::build_route_name('service_categories', 'new_form').'" data-os-output-target="lightbox"><i class="latepoint-icon latepoint-icon-plus"></i> <span>'.$add_label.'</span></button>';
      $html.= '</div>';
    $html.= '</div>';
    return $html;
  }

  public static function location_selector_adder_field($name, $label, $add_label, $options = array(), $value = '', $atts = array(), $wrapper_atts = array()){
    $html = '<div '.self::atts_string_from_array($wrapper_atts, ['class' => 'os-form-group os-form-select-group os-form-group-transparent location-selector-adder-field-w']).'>';
      if($label) $html.= '<label for="'.$name.'">'.$label.'</label>';
      $html.= '<div class="selector-adder-w">';
        $html.= '<select name="'.$name.'" '.self::atts_string_from_array($atts, ['class' => 'os-form-control']).' data-select-source="'.OsRouterHelper::build_route_name('location_categories', 'list_for_select').'">';
        foreach($options as $option){
          if(isset($option['value']) && isset($option['label'])){
            $selected = ($value == $option['value']) ? 'selected' : '';
            $html.= '<option value="'.$option['value'].'" '.$selected.'>'.$option['label'].'</option>';
          }
        }
        $html.='</select>';
        $html.='<button class="latepoint-btn latepoint-btn-primary" data-os-action="'.OsRouterHelper::build_route_name('location_categories', 'new_form').'" data-os-output-target="lightbox"><i class="latepoint-icon latepoint-icon-plus"></i> <span>'.$add_label.'</span></button>';
      $html.= '</div>';
    $html.= '</div>';
    return $html;
  }

  public static function model_options_for_multi_select($model_name){
    $options = [];
    switch($model_name){
      case 'service':
      case 'OsServiceModel':
        $service = new OsServiceModel;
        $services = $service->get_results_as_models();
        if($services){
          foreach($services as $service){
            $options[] = ['value' => $service->id, 'label' => $service->name];
          }
        }
      break;
      case 'agent':
      case 'OsAgentModel':
        $agent = new OsAgentModel;
        $agents = $agent->get_results_as_models();
        if($agents){
          foreach($agents as $agent){
            $options[] = ['value' => $agent->id, 'label' => $agent->full_name];
          }
        }
      break;
    }

	  /**
	   * Returns an array of options based on model name/mnemonic, formatted for multi-select fields
	   *
	   * @since 4.4.0
	   * @hook latepoint_model_options_for_multi_select
	   *
	   * @param {array} $options Array of model options to filter
	   * @param {string} $model_name Class name or mnemonic used to determine resultant options
	   *
	   * @returns {array} Filtered array of model options
	   */
    return apply_filters('latepoint_model_options_for_multi_select', $options, $model_name);
  }

  public static function multi_select_field($name, $label = false, $options = [], $selected_values = [], $atts = [], $wrapper_atts = []){
    $html = '';
    if(!$selected_values) $selected_values = [];
    // generate id if not set
    if(!isset($atts['id']) && !isset($atts['skip_id'])) $atts['id'] = self::name_to_id($name, $atts);
    if(!empty($wrapper_atts)) $html = '<div '.self::atts_string_from_array($wrapper_atts).'>';
    $html.= '<div class="os-form-group os-form-select-group os-form-group-transparent">';
    if($label) $html.= '<label for="'.$atts['id'].'">'.$label.'</label>';
    $html.= '<select " '.self::atts_string_from_array($atts, ['class' => 'os-late-select']).' data-placeholder="'.__('Click to select...','latepoint').'" multiple>';
    foreach($options as $key => $option){
			if(isset($option['value']) && isset($option['label'])){
	      $selected = ($selected_values && is_array($selected_values) && in_array($option['value'], $selected_values)) ? 'selected="selected"' : '';
	      $html.= '<option value="'.$option['value'].'" '.$selected.'>'.$option['label'].'</option>';
      }else{
        $value = (is_string($key)) ? $key : $option;
        $selected = ($selected_values && in_array($value, $selected_values)) ? 'selected' : '';
        $html.= '<option value="'.$value.'" '.$selected.'>'.$option.'</option>';
			}
    }
    $html.= '</select>';
    $html.= OsFormHelper::hidden_field($name, is_array($selected_values) ? implode(',',$selected_values) : $selected_values, ['skip_id' => true]);
    $html.= '</div>';
    if(!empty($wrapper_atts)) $html.= '</div>';
    return $html;
  }

  public static function select_field($name, $label, $options = array(), $selected_value = '', $atts = array(), $wrapper_atts = array(), $add_value_if_not_present = false){
    $html = '';
    // generate id if not set
	  if(!$atts) $atts = [];
    if(!isset($atts['id']) && !isset($atts['skip_id'])) $atts['id'] = self::name_to_id($name, $atts);

		// validations
	  if(!empty($atts['validate'])){
			$validate_html = 'data-os-validate="'.esc_attr(implode(' ', $atts['validate'])).'"';
	  }else{
			$validate_html = '';
	  }

    if(!empty($wrapper_atts)) $html = '<div '.self::atts_string_from_array($wrapper_atts).'>';
      $html.= '<div class="os-form-group os-form-select-group os-form-group-transparent">';
        if($label) $html.= '<label for="'.$atts['id'].'">'.$label.'</label>';
        $html.= '<select '.$validate_html.' name="'.$name.'" '.self::atts_string_from_array($atts, ['class' => 'os-form-control']).'>';
        if(isset($atts['placeholder']) && !empty($atts['placeholder'])) $html.= '<option value="">'.$atts['placeholder'].'</option>';
        if(is_array($options)){
          foreach($options as $key => $option){
            if(isset($option['value']) && isset($option['label'])){
              $selected = ($selected_value == $option['value']) ? 'selected' : '';
              $html.= '<option value="'.$option['value'].'" '.$selected.'>'.$option['label'].'</option>';
            }else{
              $value = (is_string($key)) ? $key : $option;
              $selected = ($selected_value == $value) ? 'selected' : '';
              $html.= '<option value="'.$value.'" '.$selected.'>'.$option.'</option>';
            }
          }
        }else{
          $html.= $options;
        }
        if($add_value_if_not_present && !isset($options[$selected_value])) $html.= '<option value="'.$selected_value.'">'.$selected_value.'</option>';
        $html.='</select>';
      $html.= '</div>';
    if(!empty($wrapper_atts)) $html.= '</div>';
    return $html;
  }

  public static function time_field($name, $label, $value = '', $as_period = false){
    if(strpos($value, ':') === false){
      $formatted_value = OsTimeHelper::minutes_to_hours_and_minutes($value, false, false);
    }

    $extra_class = '';
    if($as_period) $extra_class = 'as-period';

    $html = '<div class="os-time-group os-time-input-w '.$extra_class.'">';
      if($label) $html.= '<label for="'.$name.'[formatted_value]">'.$label.'</label>';
      $html.= '<div class="os-time-input-fields">';
        $html.= '<input type="text" placeholder="HH:MM" name="'.$name.'[formatted_value]" value="'.$formatted_value.'" class="os-form-control os-mask-time"/>';

        // am-pm toggler switch
        if(!OsTimeHelper::is_army_clock()){
          $is_am = (OsTimeHelper::am_or_pm($value) == 'am');
          $am_active = ($is_am) ? 'active' : '';
          $pm_active = (!$is_am) ? 'active' : '';
          $html.= '<input type="hidden" name="'.$name.'[ampm]" value="'.OsTimeHelper::am_or_pm($value).'" class="ampm-value-hidden-holder"/>';
          $html.= '<div class="time-ampm-w"><div class="time-ampm-select time-am '.$am_active.'" data-ampm-value="am">'.__('am', 'latepoint').'</div><div class="time-ampm-select time-pm '.$pm_active.'" data-ampm-value="pm">'.__('pm', 'latepoint').'</div></div>';
        }

      $html.= '</div>';
    $html.= '</div>';
    return $html;
  }


  public static function color_picker($name, $label, $value = '', $atts = array(), $wrapper_atts = array()){
    $extra_class = '';
    if($value != '') $extra_class = ' has-value';
    $html = '';
    if(!empty($wrapper_atts)) $html = '<div '.self::atts_string_from_array($wrapper_atts).'>';
      $html.= '<div '.self::atts_string_from_array(array('class' => 'os-form-group os-form-group-transparent os-form-color-picker-group'.$extra_class)).'>';
        if($label) $html.= '<label for="'.$name.'">'.$label.'</label>';
        $html.= '<div class="latepoint-color-picker-w">';
          $html.= '<div class="latepoint-color-picker" data-color="'.$value.'"></div>';
          $html.= '<input type="text" name="'.$name.'" placeholder="'.__('Pick a color', 'latepoint').'" value="'.$value.'" '.self::atts_string_from_array($atts, ['class' => 'os-form-control']).'/>';
        $html.= '</div>';
      $html.= '</div>';
    if(!empty($wrapper_atts)) $html.= '</div>';
    return $html;
  }

  public static function name_to_id($name, $atts = []){
    $name = strtolower(preg_replace('/[^0-9a-zA-Z_]/', '_', $name));
    $name = preg_replace('/__+/', '_', $name);
    $name = rtrim($name, '_');
    if(isset($atts['add_unique_id']) && $atts['add_unique_id']) $name.= '_'.OsUtilHelper::random_text('hexdec', 8);
    if(isset($atts['add_string_to_id']) && $atts['add_string_to_id']) $name.= $atts['add_string_to_id'];
    return $name;
  }


  // Value: on
  public static function checkbox_field($name, $label, $value = '', $is_checked = false, $atts = array(), $wrapper_atts = array(), $off_value = 'off'){
    $html = '';
    // generate id if not set
    if(!isset($atts['id']) && !isset($atts['skip_id'])) $atts['id'] = self::name_to_id($name, $atts);
    if(!empty($wrapper_atts)) $html.= '<div '.self::atts_string_from_array($wrapper_atts).'>';
      $checked_class = $is_checked ? 'is-checked' : '';
      if(isset($atts['data-toggle-element'])) $checked_class.= ' has-toggle-element';
      if(isset($atts['data-inverse-toggle'])) $checked_class.= ' inverse-toggle';
      $checked_attr = $is_checked ? 'checked' : '';
      $html.= '<div '.self::atts_string_from_array(array('class' => 'os-form-group os-form-checkbox-group '.$checked_class)).'>';
        if($label) $html.= '<label for="'.$atts['id'].'">';
          if($off_value !== false) $html.= '<input type="hidden" name="'.$name.'" value="'.$off_value.'"/>';
          $html.= '<input type="checkbox" name="'.$name.'" value="'.$value.'" '.$checked_attr.' '.self::atts_string_from_array($atts, ['class' => 'os-form-checkbox']).'/>';
        if($label) $html.= $label.'</label>';
      $html.= '</div>';
    if(!empty($wrapper_atts)) $html.= '</div>';
    return $html;
  }

	/**
	 * @param string $name
	 * @param string $label
	 * @param string $value
	 * @param array $atts
	 * @param array $wrapper_atts
	 * @param array $form_group_atts
	 * @return string
	 */
	public static function money_field($name, $label, $value = '', $atts = [], $wrapper_atts = [], $form_group_atts = []){
		$input_class = 'os-mask-money';
		if(isset($atts['class'])) $input_class.= ' '.$atts['class'];
		$atts = array_merge($atts, ['class' => $input_class, 'inputmode' => 'decimal']);
		$value = OsMoneyHelper::to_money_field_format($value);
		return self::text_field($name, $label, $value, $atts, $wrapper_atts, $form_group_atts);
	}

	/**
	 * @param string $name
	 * @param string $label
	 * @param string $value
	 * @param array $atts
	 * @param array $wrapper_atts
	 * @return string
	 *
	 * Generates a text input field
	 */
  public static function text_field($name, $label, $value = '', $atts = [], $wrapper_atts = [], $form_group_atts = []){
		$extra_class = ' os-form-group-transparent';
		if(isset($atts['theme'])){
			switch($atts['theme']){
				case 'bordered':
					$extra_class = ' os-form-group-bordered';
					break;
				case 'right-aligned':
					$extra_class = ' os-form-group-right-aligned';
					break;
				case 'simple':
					$extra_class = ' os-form-group-simple';
					unset($atts['theme']);
					break;
			}
		}
		if(isset($form_group_atts['class'])){
			$extra_class.= ' '.$form_group_atts['class'].' ';
			unset($form_group_atts['class']);
		}
    // generate id if not set
    if(!isset($atts['id']) && !isset($atts['skip_id'])) $atts['id'] = self::name_to_id($name, $atts);
    if($value != '') $extra_class.= ' has-value';
    if(empty($label)) $extra_class.= ' no-label';

		// validations
	  if(!empty($atts['validate'])){
			$validate_html = 'data-os-validate="'.esc_attr(implode(' ', $atts['validate'])).'"';
	  }else{
			$validate_html = '';
	  }
		unset($atts['validate']);
    $html = '';
    if(!empty($wrapper_atts)) $html = '<div '.self::atts_string_from_array($wrapper_atts).'>';
      $html.= '<div class ="os-form-group os-form-textfield-group'.$extra_class.'" '.self::atts_string_from_array($form_group_atts).'>';
        $placeholder = (isset($atts['placeholder']) && !empty($atts['placeholder'])) ? $atts['placeholder'] : $label;
        if($label) $html.= '<label for="'.$atts['id'].'">'.$label.'</label>';
				$input_class = 'os-form-control';
        $html.= '<input '.$validate_html.' type="'.($atts['type'] ?? 'text').'" placeholder="'.esc_attr($placeholder).'" name="'.esc_attr($name).'" value="'.esc_attr($value).'" '.self::atts_string_from_array($atts, ['class' => $input_class]).'/>';
      $html.= '</div>';
    if(!empty($wrapper_atts)) $html.= '</div>';
    return $html;
  }

	/**
	 * @param string $name
	 * @param string $label
	 * @param string $value
	 * @param array $atts
	 * @param array $wrapper_atts
	 * @return string
	 *
	 * Generates a telephone text input field
	 */
	public static function phone_number_field($name, $label, $value = '', $atts = [], $wrapper_atts = [], $form_group_atts = []){
		// Remain standards compliant
		$atts['type'] = 'tel';
		$atts['format'] = '[0-9]*';

		$atts['class'] = $atts['class'] ?? '';
		if (strpos($atts['class'], 'os-mask-phone') === false) $atts['class'] .= ' os-mask-phone';
		$form_group_atts['class'] = ($form_group_atts['class'] ?? '') . ' os-form-phonefield-group';

		$atts['validate'] = empty($atts['validate']) ? [] : $atts['validate'];
		if(OsSettingsHelper::is_on('validate_phone_number') && in_array('presence', $atts['validate'])){
			// validate phone if the field is required
			$atts['validate'][] = 'phone';
		}

		return self::text_field($name, $label, $value, $atts, $wrapper_atts, $form_group_atts);
	}

	/**
	 * @param string $name
	 * @param string $label
	 * @param string $value
	 * @param int|float|null $min
	 * @param int|float|null $max
	 * @param array $atts
	 * @param array $wrapper_atts
	 * @return string
	 *
	 * Generates a numeric text input field
	 */
  public static function number_field($name, $label, $value = '', $min = null, $max = null, $atts = [], $wrapper_atts = [], $form_group_atts = []): string {
	  $atts['type'] = 'number';
	  if (is_numeric($min)) $atts['min'] = $min;
	  if (is_numeric($max) && (!is_numeric($min) || $max >= $min)) $atts['max'] = $max;

	  return self::text_field($name, $label, $value, $atts, $wrapper_atts, $form_group_atts);
  }

  public static function password_field($name, $label, $value = '', $atts = array(), $wrapper_atts = array()){
    $extra_class = ' os-form-group-transparent';
		if(isset($atts['theme'])){
			switch($atts['theme']){
				case 'bordered':
					$extra_class = ' os-form-group-bordered';
					break;
				case 'right-aligned':
					$extra_class = ' os-form-group-right-aligned';
					break;
				case 'simple':
					$extra_class = ' os-form-group-simple';
					unset($atts['theme']);
					break;
			}
		}
    // generate id if not set
    if(!isset($atts['id']) && !isset($atts['skip_id'])) $atts['id'] = self::name_to_id($name, $atts);
    if($value != '') $extra_class.= ' has-value';
    if(empty($label)) $extra_class.= ' no-label';
    $html = '';
    if(!empty($wrapper_atts)) $html = '<div '.self::atts_string_from_array($wrapper_atts).'>';
      $html.= '<div '.self::atts_string_from_array(array('class' => 'os-form-group os-form-textfield-group'.$extra_class)).'>';
        $placeholder = (isset($atts['placeholder']) && !empty($atts['placeholder'])) ? $atts['placeholder'] : $label;
        if($label) $html.= '<label for="'.$atts['id'].'">'.$label.'</label>';
        $html.= '<input type="password" placeholder="'.$placeholder.'" name="'.$name.'" value="'.$value.'" '.self::atts_string_from_array($atts, ['class' => 'os-form-control']).'/>';
      $html.= '</div>';
    if(!empty($wrapper_atts)) $html.= '</div>';
    return $html;
  }

  public static function hidden_field($name, $value, $atts = array()){
    // generate id if not set
    if(!isset($atts['id']) && !isset($atts['skip_id'])) $atts['id'] = self::name_to_id($name, $atts);
    $html = '<input type="hidden" name="'.$name.'" value="'.esc_attr($value).'" '.self::atts_string_from_array($atts).'/>';
    return $html;
  }



  public static function radio_field($name, $value, $checked = false, $atts = array()){
    // generate id if not set
    if(!isset($atts['id']) && !isset($atts['skip_id'])) $atts['id'] = self::name_to_id($name, $atts);
		$checked_html = $checked ? 'checked' : '';
    $html = '<input '.$checked_html.' type="radio" name="'.$name.'" value="'.esc_attr($value).'" '.self::atts_string_from_array($atts).'/>';
    return $html;
  }


  public static function button($name, $label, $type = 'button', $atts = array(), $icon = false){
    // generate id if not set
    if(!isset($atts['id']) && !isset($atts['skip_id'])) $atts['id'] = self::name_to_id($name, $atts);
    $html = '<div class="os-form-group">';
      if($icon) $label = '<i class="latepoint-icon '.esc_attr($icon).'"></i><span>'.$label.'</span>';
      $html.= '<button type="'.$type.'" name="'.$name.'" '.self::atts_string_from_array($atts).'>'.$label.'</button>';
    $html.= '</div>';
    return $html;
  }
}