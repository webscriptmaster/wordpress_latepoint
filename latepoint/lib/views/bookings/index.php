<?php
/*
 * Copyright (c) 2022 LatePoint LLC. All rights reserved.
 */
?>
<?php
/* @var $bookings OsBookingModel[] */
/* @var $showing_from int */
/* @var $showing_to int */
/* @var $total_records int */
/* @var $per_page int */
/* @var $total_pages int */
/* @var $current_page_number int */
/* @var $records_ordered_by_key string */
/* @var $records_ordered_by_direction string */
/* @var $agents_list array */
/* @var $services_list array */
/* @var $locations_list array */
/* @var $selected_columns array */
?>
<?php if($bookings){ ?>
  <div class="table-with-pagination-w">
    <div class="os-pagination-w with-actions">
	    <div class="table-heading-w">
			  <h2 class="table-heading"><?php _e('Appointments', 'latepoint'); ?></h2>
	      <div class="pagination-info"><?php echo __('Showing', 'latepoint'). ' <span class="os-pagination-from">'. $showing_from . '</span>-<span class="os-pagination-to">'. $showing_to .'</span> '.__('of', 'latepoint').' <span class="os-pagination-total">'. $total_records. '</span>'; ?></div>
	    </div>
	    <div class="mobile-table-actions-trigger"><i class="latepoint-icon latepoint-icon-more-horizontal"></i></div>
      <div class="table-actions">
        <a data-os-lightbox-classes="width-700" data-os-action="<?php echo OsRouterHelper::build_route_name('bookings', 'customize_table'); ?>" href="#" data-os-output-target="lightbox" class="latepoint-btn latepoint-btn-grey latepoint-btn-outline download-csv-with-filters"><i class="latepoint-icon latepoint-icon-sliders"></i><span><?php _e('Table Settings', 'latepoint'); ?></span></a>
        <a href="<?php echo OsRouterHelper::build_admin_post_link(['bookings', 'index']); ?>" target="_blank" class="latepoint-btn latepoint-btn-grey latepoint-btn-outline download-csv-with-filters"><i class="latepoint-icon latepoint-icon-download"></i><span><?php _e('Download .csv', 'latepoint'); ?></span></a>
      </div>
    </div>
    <div class="os-bookings-list">
      <div class="os-scrollable-table-w">
        <div class="os-table-w os-table-compact">
          <table class="os-table os-reload-on-booking-update os-scrollable-table" data-route="<?php echo OsRouterHelper::build_route_name('bookings', 'index'); ?>">
	          <?php echo OsFormHelper::hidden_field('filter[records_ordered_by_key]', $records_ordered_by_key, ['class' => 'records-ordered-by-key os-table-filter']); ?>
	          <?php echo OsFormHelper::hidden_field('filter[records_ordered_by_direction]', $records_ordered_by_direction, ['class' => 'records-ordered-by-direction os-table-filter']); ?>
            <thead>
              <tr>
                <th class="os-sortable-column <?php if($records_ordered_by_key == 'booking_id') echo 'ordered-'.$records_ordered_by_direction; ?>" data-order-key="booking_id"><?php _e('ID', 'latepoint'); ?></th>
                <?php if(count($services_list) > 1) echo '<th>'.__('Service', 'latepoint').'</th>'; ?>
                <th class="os-sortable-column <?php if($records_ordered_by_key == 'booking_start_datetime') echo 'ordered-'.$records_ordered_by_direction; ?>" data-order-key="booking_start_datetime"><?php _e('Date/Time', 'latepoint'); ?></th>
                <th class="os-sortable-column <?php if($records_ordered_by_key == 'booking_time_left') echo 'ordered-'.$records_ordered_by_direction; ?>" data-order-key="booking_time_left"><?php _e('Time Left', 'latepoint'); ?></th>
                <?php if(count($agents_list) > 1) echo '<th>'.__('Agent', 'latepoint').'</th>'; ?>
                <?php if(count($locations_list) > 1) echo '<th>'.__('Location', 'latepoint').'</th>'; ?>
                <th><?php _e('Customer', 'latepoint'); ?></th>
                <th><?php _e('Status', 'latepoint'); ?></th>
                <th><?php _e('Payment Status', 'latepoint'); ?></th>
                <th class="os-sortable-column <?php if($records_ordered_by_key == 'booking_created_on') echo 'ordered-'.$records_ordered_by_direction; ?>" data-order-key="booking_created_on"><?php _e('Created On', 'latepoint'); ?></th>
                <?php
                foreach($selected_columns as $column_type => $columns){ 
                  foreach($columns as $column_key){ 
                    if(isset($available_columns[$column_type][$column_key])) echo '<th>'.$available_columns[$column_type][$column_key].'</th>';
                  }
                } ?>
              </tr>
              <tr>
                <th><?php echo OsFormHelper::text_field('filter[id]', false, '', ['placeholder' => __('ID', 'latepoint'), 'theme' => 'bordered', 'style' => 'width: 60px;', 'class' => 'os-table-filter']); ?></th>
                <?php if(count($services_list) > 1) echo '<th>'.OsFormHelper::select_field('filter[service_id]', false, $services_list, '', ['placeholder' => __('All Services', 'latepoint'), 'class' => 'os-table-filter']).'</th>'; ?>
                <th>
                  <div class="os-form-group">
                    <div class="os-date-range-picker os-table-filter-datepicker" data-can-be-cleared="yes" data-no-value-label="<?php _e('Search by Appointment Date', 'latepoint'); ?>" data-clear-btn-label="<?php _e('Reset Date Search', 'latepoint'); ?>">
                      <span class="range-picker-value"><?php _e('Filter Date', 'latepoint'); ?></span>
                      <i class="latepoint-icon latepoint-icon-chevron-down"></i>
                      <input type="hidden" class="os-table-filter os-datepicker-date-from" name="filter[booking_date_from]" value=""/>
                      <input type="hidden" class="os-table-filter os-datepicker-date-to" name="filter[booking_date_to]" value=""/>
                    </div>
                  </div>
                </th>
                <th><?php echo OsFormHelper::select_field('filter[time_status]', false, ['upcoming' => __('Upcoming', 'latepoint'), 'past' => __('Past', 'latepoint'), 'now' => __('Happening Now', 'latepoint')], '', ['placeholder' => __('Show All', 'latepoint'), 'class' => 'os-table-filter']); ?></th>
                <?php if(count($agents_list) > 1) echo '<th>'.OsFormHelper::select_field('filter[agent_id]', false, $agents_list, '', ['placeholder' => __('All Agents', 'latepoint'), 'class' => 'os-table-filter']).'</th>'; ?>
                <?php if(count($locations_list) > 1) echo '<th>'.OsFormHelper::select_field('filter[location_id]', false, $locations_list, '', ['placeholder' => __('All Locations', 'latepoint'), 'class' => 'os-table-filter']).'</th>'; ?>
                <th><?php echo OsFormHelper::text_field('filter[customer][full_name]', false, '', ['class' => 'os-table-filter', 'theme' => 'bordered', 'placeholder' => __('Search by Customer', 'latepoint')]); ?></th>
                <th><?php echo OsFormHelper::select_field('filter[status]', false, OsBookingHelper::get_statuses_list(), '', ['placeholder' => __('Show All', 'latepoint'), 'class' => 'os-table-filter']); ?></th>
                <th><?php echo OsFormHelper::select_field('filter[payment_status]', false, OsBookingHelper::get_payment_statuses_list(), '', ['placeholder' => __('Show All', 'latepoint'), 'class' => 'os-table-filter']); ?></th>
                <th>
                  <div class="os-form-group">
                    <div class="os-date-range-picker os-table-filter-datepicker" data-can-be-cleared="yes" data-no-value-label="<?php _e('Filter Date', 'latepoint'); ?>" data-clear-btn-label="<?php _e('Reset Date Search', 'latepoint'); ?>">
                      <span class="range-picker-value"><?php _e('Filter Date', 'latepoint'); ?></span>
                      <i class="latepoint-icon latepoint-icon-chevron-down"></i>
                      <input type="hidden" class="os-table-filter os-datepicker-date-from" name="filter[created_date_from]" value=""/>
                      <input type="hidden" class="os-table-filter os-datepicker-date-to" name="filter[created_date_to]" value=""/>
                    </div>
                  </div>
                </th>
                <?php 
                foreach($selected_columns as $column_type => $columns){ 
                  foreach($columns as $column_key){
										if(!isset($available_columns[$column_type][$column_key])) continue;
										// if column belongs to non booking object (customer, transaction, agent etc... build appropriate name for filter var)
										$field_name = ($column_type != 'booking') ? 'filter['.$column_type.']['.$column_key.']' : 'filter['.$column_key.']';
										// skip the search box if the property is "magic" (accessed via __get), because we can't query DB with that
										if($column_type == 'booking' && !property_exists('OsBookingModel', $column_key)){
											$filter_input = '';
										}else{
											$filter_input = OsFormHelper::text_field($field_name, false, '', ['class' => 'os-table-filter', 'theme' => 'bordered', 'placeholder' => $available_columns[$column_type][$column_key]]);
										}
                    echo '<th>'.$filter_input.'</th>';
                  }
                } ?>
              </tr>
            </thead>
            <tbody>
              <?php include('_table_body.php'); ?>
            </tbody>
            <tfoot>
              <tr>
                <th><?php _e('ID', 'latepoint'); ?></th>
                <?php if(count($services_list) > 1) echo '<th>'.__('Service', 'latepoint').'</th>'; ?>
                <th><?php _e('Date/Time', 'latepoint'); ?></th>
                <th><?php _e('Time Left', 'latepoint'); ?></th>
                <?php if(count($agents_list) > 1) echo '<th>'.__('Agent', 'latepoint').'</th>'; ?>
                <?php if(count($locations_list) > 1) echo '<th>'.__('Location', 'latepoint').'</th>'; ?>
                <th><?php _e('Customer', 'latepoint'); ?></th>
                <th><?php _e('Status', 'latepoint'); ?></th>
                <th><?php _e('Payment Status', 'latepoint'); ?></th>
                <th><?php _e('Created On', 'latepoint'); ?></th>
                <?php
                foreach($selected_columns as $column_type => $columns){ 
                  foreach($columns as $column_key){ 
                    if(isset($available_columns[$column_type][$column_key])) echo '<th>'.$available_columns[$column_type][$column_key].'</th>';
                  }
                } ?>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
    <div class="os-pagination-w">
      <div class="pagination-info"><?php echo __('Showing', 'latepoint'). ' <span class="os-pagination-from">'. $showing_from . '</span>-<span class="os-pagination-to">'. $showing_to .'</span> '.__('of', 'latepoint').' <span class="os-pagination-total">'. $total_records. '</span>'; ?></div>
      <div class="pagination-page-select-w">
        <label for=""><?php _e('Page:', 'latepoint'); ?></label>
        <select name="page" class="pagination-page-select">
          <?php 
          for($i = 1; $i <= $total_pages; $i++){
            $selected = ($current_page_number == $i) ? 'selected' : '';
            echo '<option '.$selected.'>'.$i.'</option>';
          } ?>
        </select>
      </div>
    </div>
  </div>

<?php }else{ ?>
  <div class="no-results-w">
    <div class="icon-w"><i class="latepoint-icon latepoint-icon-book"></i></div>
    <h2><?php _e('No Existing Appointments Found', 'latepoint'); ?></h2>
    <a href="#" <?php echo OsBookingHelper::quick_booking_btn_html(); ?> class="latepoint-btn"><i class="latepoint-icon latepoint-icon-plus"></i><span><?php _e('Add First Appointment', 'latepoint'); ?></span></a>
  </div>
<?php } ?>