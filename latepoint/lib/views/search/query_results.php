<?php
/* @var $bookings OsBookingModel[] */
/* @var $services OsServiceModel[] */
/* @var $agents OsAgentModel[] */

?>
<div class="latepoint-top-search-results">

<h3><i class="latepoint-icon latepoint-icon-calendar"></i><span><?php _e('Appointments', 'latepoint'); ?></span></h3>
<?php if(!empty($bookings)){ ?>
	<div class="latepoint-search-results-tiles-w">
		<?php foreach($bookings as $booking){ ?>
		<a href="#" <?php echo OsBookingHelper::quick_booking_btn_html($booking->id); ?> class="booking-result latepoint-search-result">
			<div class="avatar" style="background-image: url(<?php echo $booking->service->selection_image_url; ?>)"></div>
			<div class="name"><?php echo preg_replace("/($query)/i", "<strong>$1</strong>", $booking->booking_code); ?></div>
		</a>
		<?php } ?>
	</div>
<?php }else{
	echo '<div class="search-no-results">'.__('No Matched Appointments found.', 'latepoint').' <a href="#" '.OsBookingHelper::quick_booking_btn_html().'>'.__('Add Appointment', 'latepoint').'</a></div>';
} ?>
<h3><i class="latepoint-icon latepoint-icon-users"></i><span><?php _e('Customers', 'latepoint'); ?></span></h3>
<?php if(!empty($customers)){ ?>
	<div class="latepoint-search-results-tiles-w">
		<?php foreach($customers as $customer){ ?>
		<a href="<?php echo OsRouterHelper::build_link(OsRouterHelper::build_route_name('customers', 'edit_form'), array('id' => $customer->id) ); ?>" class="customer-result latepoint-search-result">
			<div class="avatar" style="background-image: url(<?php echo $customer->avatar_url; ?>)"></div>
			<div class="name"><?php echo preg_replace("/($query)/i", "<strong>$1</strong>", $customer->full_name); ?></div>
		</a>
		<?php } ?>
	</div>
<?php }else{
	echo '<div class="search-no-results">'.__('No Matched Customers found.', 'latepoint').' <a href="'.OsRouterHelper::build_link(OsRouterHelper::build_route_name('customers', 'create')).'">'.__('Add Customer', 'latepoint').'</a></div>';
} ?>
<?php if(OsRolesHelper::can_user('agent__view')){
	// This results are only for admins
	?>
	<h3><i class="latepoint-icon latepoint-icon-briefcase"></i><span><?php _e('Agents', 'latepoint'); ?></span></h3>
	<?php if(!empty($agents)){ ?>
		<div class="latepoint-search-results-tiles-w">
			<?php foreach($agents as $agent){ ?>
			<a href="<?php echo OsRouterHelper::build_link(OsRouterHelper::build_route_name('agents', 'edit_form'), array('id' => $agent->id) ); ?>" class="agent-result latepoint-search-result">
				<div class="avatar" style="background-image: url(<?php echo $agent->avatar_url; ?>)"></div>
				<div class="name"><?php echo preg_replace("/($query)/i", "<strong>$1</strong>", $agent->full_name); ?></div>
			</a>
			<?php } ?>
		</div>
	<?php }else{
		echo '<div class="search-no-results">'.__('No Matched Agents found.', 'latepoint').' <a href="'.OsRouterHelper::build_link(OsRouterHelper::build_route_name('agents', 'create')).'">'.__('Add Agent', 'latepoint').'</a></div>';
	} ?>
<?php } ?>
<?php if(OsRolesHelper::can_user('service__view')){ ?>
	<h3><i class="latepoint-icon latepoint-icon-package"></i><span><?php _e('Services', 'latepoint'); ?></span></h3>
	<?php if(!empty($services)){ ?>
		<div class="latepoint-search-results-tiles-w">
			<?php foreach($services as $service){ ?>
			<a href="<?php echo OsRouterHelper::build_link(OsRouterHelper::build_route_name('services', 'edit_form'), array('id' => $service->id) ); ?>" class="service-result latepoint-search-result">
				<div class="avatar" style="background-image: url(<?php echo $service->selection_image_url; ?>)"></div>
				<div class="name"><?php echo preg_replace("/($query)/i", "<strong>$1</strong>", $service->name); ?></div>
			</a>
			<?php } ?>
		</div>
	<?php }else{
		echo '<div class="search-no-results">'.__('No Matched Services found.', 'latepoint').' <a href="'.OsRouterHelper::build_link(OsRouterHelper::build_route_name('services', 'create')).'">'.__('Add Service', 'latepoint').'</a></div>';
	} ?>
<?php } ?>
</div>