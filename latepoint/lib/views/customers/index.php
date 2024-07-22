<?php if($customers){ ?>
  <div class="table-with-pagination-w">
    <div class="os-pagination-w with-actions">
	    <div class="table-heading-w">
			  <h2 class="table-heading"><?php _e('Customers', 'latepoint'); ?></h2>
	      <div class="pagination-info"><?php echo __('Showing', 'latepoint'). ' <span class="os-pagination-from">'. $showing_from . '</span>-<span class="os-pagination-to">'. $showing_to .'</span> '.__('of', 'latepoint').' <span class="os-pagination-total">'. $total_customers. '</span>'; ?></div>
	    </div>
	    <div class="mobile-table-actions-trigger"><i class="latepoint-icon latepoint-icon-more-horizontal"></i></div>
      <div class="table-actions">
        <a href="<?php echo OsRouterHelper::build_admin_post_link(OsRouterHelper::build_route_name('customers', 'index') ) ?>" target="_blank" class="latepoint-btn latepoint-btn-outline latepoint-btn-grey download-csv-with-filters"><i class="latepoint-icon latepoint-icon-download"></i><span><?php _e('Download .csv', 'latepoint'); ?></span></a>
        <?php if(OsAuthHelper::wp_users_as_customers()){ ?>
          <?php $not_connected_count = OsCustomerHelper::count_customers_not_connected_to_wp_users(); ?>
          <?php if($not_connected_count){ ?>
            <a href="#" data-os-success-action="reload" data-os-action="<?php echo OsRouterHelper::build_route_name('customers', 'connect_all_to_wp_users'); ?>" class="latepoint-btn latepoint-btn-outline latepoint-btn-grey"><i class="latepoint-icon latepoint-icon-wordpress"></i><span><?php _e('Connect to WP Users', 'latepoint'); ?><?php echo ' ['.$not_connected_count.']'; ?></span></a>
          <?php } ?>
        <?php } ?>
        <a href="<?php echo OsRouterHelper::build_link(['customers', 'new_form']); ?>" class="latepoint-btn latepoint-btn-outline latepoint-btn-grey"><i class="latepoint-icon latepoint-icon-plus"></i><span><?php _e('New Customer', 'latepoint'); ?></span></a>
      </div>
    </div>
    <div class="os-customers-list">
	    <div class="os-scrollable-table-w">
      <div class="os-table-w os-table-compact">
        <table class="os-table os-scrollable-table" data-route="<?php echo OsRouterHelper::build_route_name('customers', 'index'); ?>">
          <thead>
            <tr>
              <th><?php _e('ID', 'latepoint'); ?></th>
              <th class="text-left"><?php _e('Full Name', 'latepoint'); ?></th>
              <th><?php _e('Phone', 'latepoint'); ?></th>
              <th><?php _e('Email', 'latepoint'); ?></th>
              <?php if(OsSettingsHelper::is_using_social_login()) echo '<th>'.__('Social', 'latepoint').'</th>'; ?>
              <th><?php _e('Total Apps', 'latepoint'); ?></th>
              <th><?php _e('Next App', 'latepoint'); ?></th>
              <th><?php _e('Time to Next', 'latepoint'); ?></th>
              <?php if(OsAuthHelper::wp_users_as_customers()) echo '<th>'.__('WP User ID', 'latepoint').'</th>'; ?>
              <th><?php _e('Registered On', 'latepoint'); ?></th>
              <th></th>
            </tr>
            <tr>
              <th><?php echo OsFormHelper::text_field('filter[id]', false, '', ['style' => 'width: 40px;', 'class' => 'os-table-filter', 'placeholder' => __('ID', 'latepoint')]); ?></th>
              <th><?php echo OsFormHelper::text_field('filter[customer]', false, '', ['class' => 'os-table-filter', 'placeholder' => __('Search by Name', 'latepoint')]); ?></th>
              <th><?php echo OsFormHelper::text_field('filter[phone]', false, '', ['class' => 'os-table-filter', 'placeholder' => __('Phone...', 'latepoint')]); ?></th>
              <th><?php echo OsFormHelper::text_field('filter[email]', false, '', ['class' => 'os-table-filter', 'placeholder' => __('Search by Email', 'latepoint')]); ?></th>
              <th></th>
              <th></th>
              <th></th>
              <?php if(OsSettingsHelper::is_using_social_login()) echo '<th></th>'; ?>
              <?php if(OsAuthHelper::wp_users_as_customers()) echo '<th></th>'; ?>
              <th>
                <div class="os-form-group">
                  <div class="os-date-range-picker os-table-filter-datepicker" data-can-be-cleared="yes" data-no-value-label="<?php _e('Filter By Date', 'latepoint'); ?>" data-clear-btn-label="<?php _e('Reset Date Filtering', 'latepoint'); ?>">
                    <span class="range-picker-value"><?php _e('Filter By Date', 'latepoint'); ?></span>
                    <i class="latepoint-icon latepoint-icon-chevron-down"></i>
                    <input type="hidden" class="os-table-filter os-datepicker-date-from" name="filter[registration_date_from]" value=""/>
                    <input type="hidden" class="os-table-filter os-datepicker-date-to" name="filter[registration_date_to]" value=""/>
                  </div>
                </div>
              </th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php include('_table_body.php'); ?>
          </tbody>
          <tfoot>
            <tr>
              <th><?php _e('ID', 'latepoint'); ?></th>
              <th class="text-left"><?php _e('Full Name', 'latepoint'); ?></th>
              <th><?php _e('Phone', 'latepoint'); ?></th>
              <th><?php _e('Email', 'latepoint'); ?></th>
              <?php if(OsSettingsHelper::is_using_social_login()) echo '<th>'.__('Social', 'latepoint').'</th>'; ?>
              <th><?php _e('Total Apps', 'latepoint'); ?></th>
              <th><?php _e('Next App', 'latepoint'); ?></th>
              <th><?php _e('Time to Next', 'latepoint'); ?></th>
              <?php if(OsAuthHelper::wp_users_as_customers()) echo '<th>'.__('WP User ID', 'latepoint').'</th>'; ?>
              <th><?php _e('Registered On', 'latepoint'); ?></th>
              <th></th>
            </tr>
          </tfoot>
        </table>
      </div>
	    </div>
    </div>
    <div class="os-pagination-w">
      <div class="pagination-info"><?php echo __('Showing', 'latepoint'). ' <span class="os-pagination-from">'. $showing_from . '</span>-<span class="os-pagination-to">'. $showing_to .'</span> '.__('of', 'latepoint').' <span class="os-pagination-total">'. $total_customers. '</span>'; ?></div>
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
    <div class="icon-w"><i class="latepoint-icon latepoint-icon-users"></i></div>
    <h2><?php _e('No Existing Customers Found', 'latepoint'); ?></h2>
    <a href="<?php echo OsRouterHelper::build_link(OsRouterHelper::build_route_name('customers', 'new_form') ) ?>" class="latepoint-btn"><i class="latepoint-icon latepoint-icon-plus"></i><span><?php _e('Add Customer', 'latepoint'); ?></span></a>
  </div>
<?php } ?>