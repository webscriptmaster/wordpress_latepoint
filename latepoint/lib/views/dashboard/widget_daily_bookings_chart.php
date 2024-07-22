<?php
/* @var $locations OsLocationModel[] */
/* @var $agents OsAgentModel[] */
/* @var $services OsServiceModel[] */
/* @var $filter \LatePoint\Misc\Filter */
?>
<div class="os-widget os-widget-animated os-widget-daily-bookings" data-os-reload-action="<?php echo OsRouterHelper::build_route_name('dashboard', 'widget_daily_bookings_chart'); ?>">
	<div class="os-widget-header with-actions">
		<h3 class="os-widget-header-text"><?php _e('Performance', 'latepoint'); ?></h3>
		<div class="os-widget-header-actions-trigger"><i class="latepoint-icon latepoint-icon-more-horizontal"></i></div>
		<div class="os-widget-header-actions">
			<?php if($locations && count($locations) > 1){ ?>
				<select name="location_id" class="os-trigger-reload-widget">
					<option value=""><?php _e('All locations', 'latepoint'); ?></option>
					<?php 
					foreach($locations as $location){ ?>
					<option value="<?php echo $location->id ?>" <?php if($location->id == $filter->location_id) echo 'selected="selected"' ?>><?php echo $location->name; ?></option>
					<?php } ?>
				</select>
			<?php } ?>
			<?php $agents_selector_css = (is_array($agents) && (count($agents) == 1)) ? 'display: none;' : ''; ?>
			<select name="agent_id" id="" class="os-trigger-reload-widget" style="<?php echo $agents_selector_css; ?>">
				<option value=""><?php _e('All Agents', 'latepoint'); ?></option>
				<?php 
				if($agents){
					foreach($agents as $agent){ ?>
					<option value="<?php echo $agent->id ?>" <?php if($agent->id == $filter->agent_id) echo 'selected="selected"' ?>><?php echo $agent->full_name; ?></option>
					<?php }
				} ?>
			</select>
			<select name="service_id" id="" class="os-trigger-reload-widget">
				<option value=""><?php _e('All Services', 'latepoint'); ?></option>
				<?php 
				if($services){
					foreach($services as $service){ ?>
					<option value="<?php echo $service->id ?>" <?php if($service->id == $filter->service_id) echo 'selected="selected"' ?>><?php echo $service->name; ?></option>
					<?php }
				} ?>
			</select>
			<div class="os-date-range-picker">
				<span class="range-picker-value"><?php echo $date_period_string; ?></span>
				<input type="hidden" name="date_from" value="<?php echo $date_from; ?>"/>
				<input type="hidden" name="date_to" value="<?php echo $date_to; ?>"/>
				<i class="latepoint-icon latepoint-icon-chevron-down"></i>
			</div>
		</div>
	</div>
	<div class="os-widget-content no-padding">
		<div class="stats-tab-contents">
			<div class="stats-tab-content">
				<div class="stats-charts-w">
					<div class="stats-line-chart-w">
						<div class="stats-tabs">
							<div class="stats-tab">
								<div class="stats-tab-value">
									<span class="stats-tab-value-self"><?php echo $total_bookings; ?></span>
									<span class="stats-change change-<?php echo ($total_bookings == $prev_total_bookings) ? '' : (($total_bookings < $prev_total_bookings) ? 'negative' : 'positive'); ?>">
										<span class="stats-change-label"><?php _e('Previously:', 'latepoint'); echo ' <strong>'.$prev_total_bookings.'</strong>'; ?></span><span class="stats-change-value"><?php echo OsUtilHelper::percent_diff($prev_total_bookings, $total_bookings); ?>%</span>
									</span>
								</div>
								<div class="stats-tab-label"><?php _e('Appointments', 'latepoint'); ?></div>
								<div class="stats-tab-info" data-late-tooltip="<?php _e('Total number of appointments in selected period.', 'latepoint');?>"><div class="stats-tab-info-icon">i</div></div>
							</div>
							<div class="stats-tab">
								<div class="stats-tab-value">
									<span class="stats-tab-value-self"><?php echo OsMoneyHelper::format_price($total_price); ?></span>
									<span class="stats-change change-<?php echo ($total_price == $prev_total_price) ? '' : (($total_price < $prev_total_price) ? 'negative' : 'positive'); ?>">
										<span class="stats-change-label"><?php _e('Previously:', 'latepoint'); echo ' <strong>'.OsMoneyHelper::format_price($prev_total_price).'</strong>'; ?></span><span class="stats-change-value"><?php echo OsUtilHelper::percent_diff($prev_total_price, $total_price); ?>%</span>
									</span>
								</div>
								<div class="stats-tab-label"><?php _e('Sales Revenue', 'latepoint'); ?></div>
								<div class="stats-tab-info" data-late-tooltip="<?php _e('Total sales in selected period.', 'latepoint');?>"><div class="stats-tab-info-icon">i</div></div>
							</div>
							<div class="stats-tab">
								<div class="stats-tab-value">
									<span class="stats-tab-value-self"><?php echo round($total_duration/60, 1); ?></span>
									<span class="stats-change change-<?php echo ($total_duration == $prev_total_duration) ? '' : (($total_duration < $prev_total_duration) ? 'negative' : 'positive'); ?>">
										<span class="stats-change-label"><?php _e('Previously:', 'latepoint'); echo ' <strong>'.round($prev_total_duration/60, 1).'</strong>'; ?></span><span class="stats-change-value"><?php echo OsUtilHelper::percent_diff($prev_total_duration, $total_duration); ?>%</span>
									</span>
								</div>
								<div class="stats-tab-label"><?php _e('Hours Worked', 'latepoint'); ?></div>
								<div class="stats-tab-info" data-late-tooltip="<?php _e('Total hours worked across all selected agents in selected period.', 'latepoint');?>"><div class="stats-tab-info-icon">i</div></div>
							</div>
							<div class="stats-tab">
								<div class="stats-tab-value">
									<span class="stats-tab-value-self"><?php echo $total_new_customers; ?></span>
									<span class="stats-change change-<?php echo ($total_new_customers == $prev_total_new_customers) ? '' : (($total_new_customers < $prev_total_new_customers) ? 'negative' : 'positive'); ?>">
										<span class="stats-change-label"><?php _e('Previously:', 'latepoint'); echo ' <strong>'.$prev_total_new_customers.'</strong>'; ?></span><span class="stats-change-value"><?php echo OsUtilHelper::percent_diff($prev_total_new_customers, $total_new_customers); ?>%</span>
									</span>
								</div>
								<div class="stats-tab-label"><?php _e('New Customers', 'latepoint'); ?></div>
								<div class="stats-tab-info" data-late-tooltip="<?php _e('Total number of new customers registered in selected period.', 'latepoint');?>"><div class="stats-tab-info-icon">i</div></div>
							</div>
						</div>
						<?php if($daily_bookings_chart_data_values_string){ ?>
							<div class="daily-bookings-chart-w">
								<canvas id="chartDailyBookings"
									data-chart-labels="<?php echo $daily_bookings_chart_labels_string; ?>" 
									data-chart-values="<?php echo $daily_bookings_chart_data_values_string; ?>"></canvas>
							</div>
						<?php }else{ ?>
						  <div class="no-results-w">
						    <div class="icon-w"><i class="latepoint-icon latepoint-icon-grid"></i></div>
						    <h2><?php _e('No Appointments Found', 'latepoint'); ?></h2>
						    <a href="#" <?php echo OsBookingHelper::quick_booking_btn_html(); ?> class="latepoint-btn"><i class="latepoint-icon latepoint-icon-plus-square"></i><span><?php _e('Create Appointment', 'latepoint'); ?></span></a>
						  </div>
						<?php } ?>
					</div>
					<?php
					// Circular pie chart, disabled for now
					if(false && isset($pie_chart_data) && !empty($pie_chart_data['values'])){ ?>
						<div class="stats-donut-chart-w">
							<div class="status-donut-label"><?php _e('Breakdown by Service', 'latepoint'); ?></div>
							<canvas class="os-donut-chart" width="140" height="140"
							data-chart-labels="<?php echo implode(',', $pie_chart_data['labels']); ?>" 
							data-chart-colors="<?php echo implode(',', $pie_chart_data['colors']); ?>" 
							data-chart-values="<?php echo implode(',', $pie_chart_data['values']); ?>"></canvas>
						</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
</div>