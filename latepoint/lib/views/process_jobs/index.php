<?php
/*
 * Copyright (c) 2022 LatePoint LLC. All rights reserved.
 */

/* @var $jobs OsProcessJobModel[] */
/* @var $showing_from int */
/* @var $showing_to int */
/* @var $total_records int */
/* @var $per_page int */
/* @var $total_pages int */
/* @var $current_page_number int */
?>
<?php if($jobs){ ?>
	<div class="table-with-pagination-w">
  <div class="os-jobs-list">
	  <div class="os-scrollable-table-w">
    <div class="os-table-w os-table-compact">
      <table class="os-table os-reload-on-booking-update os-scrollable-table os-table-align-top" data-route="<?php echo OsRouterHelper::build_route_name('process_jobs', 'index'); ?>">
        <thead>
          <tr>
            <th><?php _e('Event', 'latepoint'); ?></th>
            <th><?php _e('Process', 'latepoint'); ?></th>
            <th><?php _e('Object ID', 'latepoint'); ?></th>
            <th><?php _e('Actions', 'latepoint'); ?></th>
            <th><?php _e('Status', 'latepoint'); ?></th>
            <th><?php _e('Run Time (UTC)', 'latepoint'); ?></th>
            <th><?php _e('Run Info', 'latepoint'); ?></th>
	          <th></th>
          </tr>
          <tr>
	          <th><?php echo OsFormHelper::select_field('filter[event_type]', false, \LatePoint\Misc\ProcessEvent::get_event_types_for_select(), '', ['placeholder' => __('All Types', 'latepoint'),'class' => 'os-table-filter']); ?></th>
	          <th><?php echo OsFormHelper::select_field('filter[process_id]', false, OsProcessesHelper::processes_list_for_select(), '', ['placeholder' => __('All Processes', 'latepoint'),'class' => 'os-table-filter']); ?></th>
	          <th><?php echo OsFormHelper::text_field('filter[object_id]', false, '', ['placeholder' => __('Object ID', 'latepoint'), 'class' => 'os-table-filter', 'style' => 'width: 80px;']); ?></th>
	          <th></th>
	          <th><?php echo OsFormHelper::select_field('filter[status]', false, [LATEPOINT_JOB_STATUS_COMPLETED => __('Completed', 'latepoint'), LATEPOINT_JOB_STATUS_SCHEDULED => __('Scheduled', 'latepoint'), LATEPOINT_JOB_STATUS_CANCELLED => __('Cancelled', 'latepoint')], '', ['placeholder' => __('All Statuses', 'latepoint'), 'class' => 'os-table-filter']); ?></th>
	          <th>
		          <div class="os-form-group">
			          <div class="os-date-range-picker os-table-filter-datepicker" data-can-be-cleared="yes" data-no-value-label="<?php _e('Filter By Date', 'latepoint'); ?>" data-clear-btn-label="<?php _e('Reset Date Filtering', 'latepoint'); ?>">
				          <span class="range-picker-value"><?php _e('Filter By Date', 'latepoint'); ?></span>
				          <i class="latepoint-icon latepoint-icon-chevron-down"></i>
				          <input type="hidden" class="os-table-filter os-datepicker-date-from" name="filter[to_run_after_utc_from]" value=""/>
				          <input type="hidden" class="os-table-filter os-datepicker-date-to" name="filter[to_run_after_utc_to]" value=""/>
			          </div>
		          </div>
	          </th>
	          <th></th>
	          <th></th>
          </tr>
        </thead>
        <tbody>
        <?php include('_table_body.php'); ?>
        </tbody>
        <tfoot>
          <tr>
            <th><?php _e('Event', 'latepoint'); ?></th>
            <th><?php _e('Process', 'latepoint'); ?></th>
            <th><?php _e('Object ID', 'latepoint'); ?></th>
            <th><?php _e('Actions', 'latepoint'); ?></th>
            <th><?php _e('Status', 'latepoint'); ?></th>
            <th><?php _e('Run Time (UTC)', 'latepoint'); ?></th>
            <th><?php _e('Run Info', 'latepoint'); ?></th>
	          <th></th>
          </tr>
        </tfoot>
      </table>
    </div>
    </div>
  </div>
  <div class="os-pagination-w">
    <div class="pagination-info"><?php echo __('Showing jobs', 'latepoint'). ' <span class="os-pagination-from">'. $showing_from . '</span> '.__('to', 'latepoint').' <span class="os-pagination-to">'. $showing_to .'</span> '.__('of', 'latepoint').' <span class="os-pagination-total">'. $total_records. '</span>'; ?></div>
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
    <div class="icon-w"><i class="latepoint-icon latepoint-icon-credit-card"></i></div>
    <h2><?php _e('No Jobs Found', 'latepoint'); ?></h2>
  </div>
<?php } ?>