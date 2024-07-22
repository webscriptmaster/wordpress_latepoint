<?php
/* @var $transactions OsTransactionModel[] */
/* @var $showing_from int */
/* @var $showing_to int */
/* @var $total_transactions int */
/* @var $per_page int */
/* @var $total_pages int */
/* @var $current_page_number int */
?>
<?php if ($transactions) { ?>
	<div class="table-with-pagination-w">
    <div class="os-pagination-w with-actions">
	    <div class="table-heading-w">
			  <h2 class="table-heading"><?php _e('Payments', 'latepoint'); ?></h2>
	      <div class="pagination-info"><?php echo __('Showing', 'latepoint'). ' <span class="os-pagination-from">'. $showing_from . '</span>-<span class="os-pagination-to">'. $showing_to .'</span> '.__('of', 'latepoint').' <span class="os-pagination-total">'. $total_transactions. '</span>'; ?></div>
	    </div>
	    <div class="mobile-table-actions-trigger"><i class="latepoint-icon latepoint-icon-more-horizontal"></i></div>
	    <div class="table-actions">
		    <a href="<?php echo OsRouterHelper::build_admin_post_link(['transactions', 'index'] ) ?>" target="_blank" class="latepoint-btn latepoint-btn-outline latepoint-btn-grey download-csv-with-filters"><i class="latepoint-icon latepoint-icon-download"></i><span><?php _e('Download .csv', 'latepoint'); ?></span></a>
	    </div>
    </div>
	<div class="os-transactions-list">
		<div class="os-scrollable-table-w">
			<div class="os-table-w os-table-compact">
				<table class="os-table os-reload-on-booking-update os-scrollable-table" data-route="<?php echo OsRouterHelper::build_route_name('transactions', 'index'); ?>">
					<thead>
					<tr>
						<th><?php _e('ID', 'latepoint'); ?></th>
						<th><?php _e('Token', 'latepoint'); ?></th>
						<th><?php _e('Booking ID', 'latepoint'); ?></th>
						<th><?php _e('Customer', 'latepoint'); ?></th>
						<th><?php _e('Processor', 'latepoint'); ?></th>
						<th><?php _e('Method', 'latepoint'); ?></th>
						<th><?php _e('Amount', 'latepoint'); ?></th>
						<th><?php _e('Status', 'latepoint'); ?></th>
						<th><?php _e('Funds status', 'latepoint'); ?></th>
						<th><?php _e('Date', 'latepoint'); ?></th>
					</tr>
          <tr>
	          <th><?php echo OsFormHelper::text_field('filter[id]', false, '', ['placeholder' => __('ID', 'latepoint'), 'class' => 'os-table-filter', 'style' => 'width: 60px;']); ?></th>
	          <th><?php echo OsFormHelper::text_field('filter[token]', false, '', ['placeholder' => __('Token', 'latepoint'), 'class' => 'os-table-filter']); ?></th>
	          <th><?php echo OsFormHelper::text_field('filter[booking_id]', false, '', ['placeholder' => __('Booking ID', 'latepoint'), 'class' => 'os-table-filter']); ?></th>
	          <th><?php echo OsFormHelper::text_field('filter[customer][full_name]', false, '', ['placeholder' => __('Customer Name', 'latepoint'), 'class' => 'os-table-filter']); ?></th>
	          <th><?php echo OsFormHelper::select_field('filter[processor]', false, OsPaymentsHelper::get_payment_processors_for_select(), '', ['placeholder' => __('Show All', 'latepoint'),'class' => 'os-table-filter']); ?></th>
	          <th><?php echo OsFormHelper::select_field('filter[payment_method]', false, OsPaymentsHelper::get_all_payment_methods_for_select(), '', ['placeholder' => __('Show All', 'latepoint'),'class' => 'os-table-filter']); ?></th>
	          <th><?php echo OsFormHelper::text_field('filter[amount]', false, '', ['placeholder' => __('Amount', 'latepoint'), 'class' => 'os-table-filter']); ?></th>
	          <th><?php echo OsFormHelper::select_field('filter[status]', false, OsPaymentsHelper::get_transaction_statuses_list(), '', ['placeholder' => __('Show All', 'latepoint'),'class' => 'os-table-filter']); ?></th>
	          <th><?php echo OsFormHelper::select_field('filter[funds_status]', false, OsPaymentsHelper::get_funds_statuses_list(), '', ['placeholder' => __('Show All', 'latepoint'),'class' => 'os-table-filter']); ?></th>
	          <th>
		          <div class="os-form-group">
			          <div class="os-date-range-picker os-table-filter-datepicker" data-can-be-cleared="yes" data-no-value-label="<?php _e('Filter By Date', 'latepoint'); ?>" data-clear-btn-label="<?php _e('Reset Date Filtering', 'latepoint'); ?>">
				          <span class="range-picker-value"><?php _e('Filter By Date', 'latepoint'); ?></span>
				          <i class="latepoint-icon latepoint-icon-chevron-down"></i>
				          <input type="hidden" class="os-table-filter os-datepicker-date-from" name="filter[created_at_from]" value=""/>
				          <input type="hidden" class="os-table-filter os-datepicker-date-to" name="filter[created_at_to]" value=""/>
			          </div>
		          </div>
	          </th>
          </tr>
					</thead>
					<tbody>
						<?php include '_table_body.php'; ?>
					</tbody>
					<tfoot>
					<tr>
						<th><?php _e('ID', 'latepoint'); ?></th>
						<th><?php _e('Token', 'latepoint'); ?></th>
						<th><?php _e('Booking ID', 'latepoint'); ?></th>
						<th><?php _e('Customer', 'latepoint'); ?></th>
						<th><?php _e('Processor', 'latepoint'); ?></th>
						<th><?php _e('Method', 'latepoint'); ?></th>
						<th><?php _e('Amount', 'latepoint'); ?></th>
						<th><?php _e('Status', 'latepoint'); ?></th>
						<th><?php _e('Funds status', 'latepoint'); ?></th>
						<th><?php _e('Date', 'latepoint'); ?></th>
					</tr>
					</tfoot>
				</table>
			</div>
		</div>
	  <div class="os-pagination-w">
	    <div class="pagination-info"><?php echo __('Showing', 'latepoint'). ' <span class="os-pagination-from">'. $showing_from . '</span>-<span class="os-pagination-to">'. $showing_to .'</span> '.__('of', 'latepoint').' <span class="os-pagination-total">'. $total_transactions. '</span>'; ?></div>
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

	</div>
<?php } else { ?>
	<div class="no-results-w">
		<div class="icon-w"><i class="latepoint-icon latepoint-icon-credit-card"></i></div>
		<h2><?php _e('No Transactions Found', 'latepoint'); ?></h2>
		<a href="#" <?php echo OsBookingHelper::quick_booking_btn_html(); ?> class="latepoint-btn"><i
				class="latepoint-icon latepoint-icon-plus"></i><span><?php _e('Create Appointment', 'latepoint'); ?></span></a>
	</div>
<?php } ?>