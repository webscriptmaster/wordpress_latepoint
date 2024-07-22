<h3 class="os-wizard-sub-header"><?php echo sprintf(__('Step %d of %d', 'latepoint'), $current_step_number, 3); ?></h3>
<h2 class="os-wizard-header"><?php _e('Create Agents', 'latepoint'); ?></h2>
<div class="os-wizard-desc">Agents act as your bookable resources, you have to have at least one created in order for you to accept bookings.</div>
<div class="os-wizard-step-content-i">
	<?php 
	if($agents){
		include('_list_agents.php');
	}else{
		include('_form_agent.php');
	} ?>
</div>