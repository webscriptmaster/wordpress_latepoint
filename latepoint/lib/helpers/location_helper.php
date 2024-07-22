<?php 

class OsLocationHelper {

  static $locations;
  static $selected_location = false;
  static $total_locations;
  static $filtered_total_locations;

  public static function locations_selector_html(){
    return false;
  }

  public static function get_location_ids_for_service_and_agent($service_id = false, $agent_id = false): array{
    $all_location_ids = OsConnectorHelper::get_connected_object_ids('location_id', ['service_id' => $service_id, 'agent_id' => $agent_id]);
    $locations = new OsLocationModel();
    $active_location_ids = $locations->select('id')->should_be_active()->get_results(ARRAY_A);
    if($active_location_ids){
      $active_location_ids = array_column($active_location_ids, 'id');
      $all_location_ids = array_intersect($active_location_ids, $all_location_ids);
    }else{
      $all_location_ids = [];
    }
    return $all_location_ids;
  }


  public static function generate_locations_list($locations = false, $preselected_location = false){
    if($locations && is_array($locations) && !empty($locations)){ ?>
      <div class="os-locations os-animated-parent os-items os-selectable-items os-as-rows">
        <?php foreach($locations as $location){ ?>
          <?php if($preselected_location && $location->id != $preselected_location->id) continue; ?>
          <div class="os-animated-child os-item os-selectable-item <?php if(!empty($location->full_address)) echo 'with-description'; ?>  <?php echo ($preselected_location && $location->id == $preselected_location->id) ? 'selected is-preselected' : ''; ?>"
              data-summary-field-name="location" 
              data-summary-value="<?php echo esc_attr($location->name); ?>" 
              data-id-holder=".latepoint_location_id"
              data-item-id="<?php echo $location->id; ?>">
            <div class="os-animated-self os-item-i">
              <div class="os-item-img-w" style="background-image: url(<?php echo $location->selection_image_url; ?>);"></div>
              <div class="os-item-name-w">
                <div class="os-item-name"><?php echo $location->name; ?></div>
                <?php if($location->full_address){ ?>
                  <div class="os-item-desc"><?php echo $location->full_address; ?></div>
                <?php } ?>
              </div>
            </div>
          </div>
        <?php } ?>
      </div>
    <?php } 
  }

  public static function generate_locations_and_categories_list($parent_id = false, $show_selected_locations = false){
    $location_categories = new OsLocationCategoryModel();
    $args = array();
    $args['parent_id'] = $parent_id ? $parent_id : 'IS NULL';
    $location_categories = $location_categories->where($args)->order_by('order_number asc')->get_results_as_models();


    $main_parent_class = ($parent_id) ? 'os-animated-parent': 'os-item-categories-main-parent os-animated-parent';
    echo '<div class="os-item-categories-holder '.$main_parent_class.'">';

    // generate locations that have no category
    if($parent_id == false){
          $locations_without_category = new OsLocationModel();
          if($show_selected_locations) $locations_without_category->where_in('id', $show_selected_locations);
          $locations_without_category = $locations_without_category->where(['category_id' => 0])->should_be_active()->get_results_as_models();
          if($locations_without_category) OsLocationHelper::generate_locations_list($locations_without_category);
    }

    if(is_array($location_categories)){
      foreach($location_categories as $location_category){
        $locations = [];
        $category_locations = $location_category->get_active_locations();
        if(is_array($category_locations)){
          // if show selected locations restriction is set - filter
          if($show_selected_locations){
            foreach($category_locations as $category_location){
              if(in_array($category_location->id, $show_selected_locations)) $locations[] = $category_location;
            }
          }else{
            $locations = $category_locations;
          }  
        }
        $child_categories = new OsLocationCategoryModel();
        $count_child_categories = $child_categories->where(['parent_id' => $location_category->id])->count();
        // show only if it has either at least one child category or location
        if($count_child_categories || count($locations)){ ?>
          <div class="os-item-category-w os-items os-as-rows os-animated-child" data-id="<?php echo $location_category->id; ?>">
            <div class="os-item-category-info-w os-item os-animated-self with-plus">
              <div class="os-item-category-info os-item-i">
                <div class="os-item-img-w" style="background-image: url(<?php echo $location_category->selection_image_url; ?>);"></div>
                <div class="os-item-name-w">
                  <div class="os-item-name"><?php echo $location_category->name; ?></div>
                </div>
                <?php if(count($locations)){ ?>
                  <div class="os-item-child-count"><span><?php echo count($locations); ?></span> <?php _e('Locations', 'latepoint-locations'); ?></div>
                <?php } ?>
              </div>
            </div>
            <?php OsLocationHelper::generate_locations_list($locations); ?>
            <?php OsLocationHelper::generate_locations_and_categories_list($location_category->id, $show_selected_locations); ?>
          </div><?php
        }
      }
    }
    echo '</div>';
  }

  public static function get_locations_for_service_and_agent($service_id = false, $agent_id = false, $active_only = true){
    $all_location_ids = OsConnectorHelper::get_connected_object_ids('location_id', ['service_id' => $service_id, 'agent_id' => $agent_id]);
    if($active_only){
      $locations = new OsLocationModel();
      $active_location_ids = $locations->select('id')->should_be_active()->get_results(ARRAY_A);
      if($active_location_ids){
        $active_location_ids = array_column($active_location_ids, 'id');
        $all_location_ids = array_intersect($active_location_ids, $all_location_ids);
      }else{
        $all_location_ids = [];
      }
    }
    return $all_location_ids;
  }

	/**
	 * @param bool $filter_allowed_records
	 * @return array
	 */
  public static function get_locations(bool $filter_allowed_records = false): array{
    $locations = new OsLocationModel();
    if($filter_allowed_records) $locations->filter_allowed_records();
    $locations = $locations->get_results_as_models();
    return $locations;
  }

	/**
	 * @param bool $filter_allowed_records
	 * @return array
	 */
  public static function get_locations_list(bool $filter_allowed_records = false): array{
    $locations = new OsLocationModel();
    if($filter_allowed_records) $locations->filter_allowed_records();
    $locations = $locations->get_results_as_models();
    $locations_list = [];
    if($locations){
      foreach($locations as $location){
        $locations_list[] = ['value' => $location->id, 'label' => $location->name];
      }
    }
    return $locations_list;
  }

	/**
	 * @param bool $filter_allowed_records
	 * @return int
	 */
  public static function count_locations(bool $filter_allowed_records = false): int{
		if($filter_allowed_records){
	    if(self::$filtered_total_locations) return self::$filtered_total_locations;
		}else{
	    if(self::$total_locations) return self::$total_locations;
		}
    $locations = new OsLocationModel();
    if($filter_allowed_records) $locations->filter_allowed_records();
    $locations = $locations->get_results_as_models();
		if($filter_allowed_records) {
			self::$filtered_total_locations = $locations ? count($locations) : 0;
			return self::$filtered_total_locations;
		}else{
			self::$total_locations = $locations ? count($locations) : 0;
			return self::$total_locations;
		}
  }

  public static function get_default_location(bool $filter_allowed_records = false): OsLocationModel{
    $location_model = new OsLocationModel();
		if($filter_allowed_records) $location_model->filter_allowed_records();
    $location = $location_model->set_limit(1)->get_results_as_models();
		if($location && $location->id){
			return $location;
		}else{
			// create location only if we trully haven't found anything unfiltered
			if(!$filter_allowed_records || OsRolesHelper::are_all_records_allowed('location')){
				return self::create_default_location();
			}else{
				return new OsLocationModel();
			}
		}
  }

  public static function get_default_location_id(bool $filter_allowed_records = false){
    $location = self::get_default_location($filter_allowed_records);
    return $location->is_new_record() ? 0 : $location->id;
  }

  public static function create_default_location(){
    $location_model = new OsLocationModel();
    $location_model->name = __('Main Location', 'latepoint-locations');
    if($location_model->save()){
      $connector = new OsConnectorModel();
      $incomplete_connections = $connector->where(['location_id' => 'IS NULL'])->get_results_as_models();
      if($incomplete_connections){
        foreach($incomplete_connections as $incomplete_connection){
          $incomplete_connection->update_attributes(['location_id' => $location_model->id]);
        }
      }
      $bookings = new OsBookingModel();
      $incomplete_bookings = $bookings->where(['location_id' => 'IS NULL'])->get_results_as_models();
      if($incomplete_bookings){
        foreach($incomplete_bookings as $incomplete_booking){
          $incomplete_booking->update_attributes(['location_id' => $location_model->id]);
        }
      }
    }
    return $location_model;
  }


  public static function generate_location_categories_list($parent_id = false){
    $location_categories = new OsLocationCategoryModel();
    $args = array();
    $args['parent_id'] = $parent_id ? $parent_id : 'IS NULL';
    $location_categories = $location_categories->where($args)->order_by('order_number asc')->get_results_as_models();
    if(!is_array($location_categories)) return;
    if($location_categories){
      foreach($location_categories as $location_category){ ?>
        <div class="os-category-parent-w" data-id="<?php echo $location_category->id; ?>">
          <div class="os-category-w">
            <div class="os-category-head">
              <div class="os-category-drag"></div>
              <div class="os-category-name"><?php echo $location_category->name; ?></div>
              <div class="os-category-items-meta"><?php _e('ID: ', 'latepoint-locations'); ?><span><?php echo $location_category->id; ?></span></div>
              <div class="os-category-items-count"><span><?php echo $location_category->count_locations(); ?></span> <?php _e('Locations Linked', 'latepoint-locations'); ?></div>
              <button class="os-category-edit-btn"><i class="latepoint-icon latepoint-icon-edit-3"></i></button>
            </div>
            <div class="os-category-body">
              <?php include(LATEPOINT_ADDON_LOCATIONS_VIEWS_ABSPATH. 'location_categories/_form.php'); ?>
            </div>
          </div>
          <div class="os-category-children">
            <?php 
            if(is_array($location_category->locations)){
              foreach($location_category->locations as $location){
                echo '<div class="item-in-category-w status-'.$location->status.'" data-id="'.$location->id.'">
												<div class="os-category-item-drag"></div>
												<div class="os-category-item-name">'.$location->name.'</div>
												<div class="os-category-item-meta">ID: '.$location->id.'</div>
											</div>';
              }
            } ?>
            <?php OsLocationHelper::generate_location_categories_list($location_category->id); ?>
          </div>
        </div>
        <?php
      }
    }
  }
}