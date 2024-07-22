<div class="available-vars-block">
  <h4><?php _e('Direct URLs to Manage Appointment', 'latepoint'); ?></h4>
  <ul>
    <li><span class="var-label"><?php _e('For Agent:', 'latepoint'); ?></span> <span class="var-code os-click-to-copy">{{manage_booking_url_agent}}</span></li>
    <li><span class="var-label"><?php _e('For Customer:', 'latepoint'); ?></span> <span class="var-code os-click-to-copy">{{manage_booking_url_customer}}</span></li>
  </ul>
  <h4><?php _e('Appointment', 'latepoint'); ?></h4>
  <ul>
    <li><span class="var-label"><?php _e('Appointment ID#:', 'latepoint'); ?></span> <span class="var-code os-click-to-copy">{{booking_id}}</span></li>
    <li><span class="var-label"><?php _e('Confirmation Code:', 'latepoint'); ?></span> <span class="var-code os-click-to-copy">{{booking_code}}</span></li>
    <li><span class="var-label"><?php _e('Service Name:', 'latepoint'); ?></span> <span class="var-code os-click-to-copy">{{service_name}}</span></li>
    <li><span class="var-label"><?php _e('Service Category:', 'latepoint'); ?></span> <span class="var-code os-click-to-copy">{{service_category}}</span></li>
    <li><span class="var-label"><?php _e('Start Date:', 'latepoint'); ?></span> <span class="var-code os-click-to-copy">{{start_date}}</span></li>
    <li><span class="var-label"><?php _e('Start Time:', 'latepoint'); ?></span> <span class="var-code os-click-to-copy">{{start_time}}</span></li>
    <li><span class="var-label"><?php _e('End Time:', 'latepoint'); ?></span> <span class="var-code os-click-to-copy">{{end_time}}</span></li>
    <li><span class="var-label"><?php _e('Service Duration:', 'latepoint'); ?></span> <span class="var-code os-click-to-copy">{{booking_duration}}</span></li>
    <li><span class="var-label"><?php _e('Status:', 'latepoint'); ?></span> <span class="var-code os-click-to-copy">{{booking_status}}</span></li>
    <?php do_action('latepoint_available_vars_booking'); ?>
  </ul>
</div>
<div class="available-vars-block">
  <h4><?php _e('Payment', 'latepoint'); ?></h4>
  <ul>
    <li><span class="var-label"><?php _e('Payment Status:', 'latepoint'); ?></span> <span class="var-code os-click-to-copy">{{booking_payment_status}}</span></li>
    <li><span class="var-label"><?php _e('Payment Portion:', 'latepoint'); ?></span> <span class="var-code os-click-to-copy">{{booking_payment_portion}}</span></li>
    <li><span class="var-label"><?php _e('Payment Method:', 'latepoint'); ?></span> <span class="var-code os-click-to-copy">{{booking_payment_method}}</span></li>
    <li><span class="var-label"><?php _e('Payment Amount:', 'latepoint'); ?></span> <span class="var-code os-click-to-copy">{{booking_payment_amount}}</span></li>
    <li><span class="var-label"><?php _e('Total Price:', 'latepoint'); ?></span> <span class="var-code os-click-to-copy">{{booking_price}}</span></li>
  </ul>
</div>
<div class="available-vars-block">
  <h4><?php _e('Customer', 'latepoint'); ?></h4>
  <ul>
    <li><span class="var-label"><?php _e('Full Name:', 'latepoint'); ?></span> <span class="var-code os-click-to-copy">{{customer_full_name}}</span></li>
    <li><span class="var-label"><?php _e('First Name:', 'latepoint'); ?></span> <span class="var-code os-click-to-copy">{{customer_first_name}}</span></li>
    <li><span class="var-label"><?php _e('Last Name:', 'latepoint'); ?></span> <span class="var-code os-click-to-copy">{{customer_last_name}}</span></li>
    <li><span class="var-label"><?php _e('Email Address:', 'latepoint'); ?></span> <span class="var-code os-click-to-copy">{{customer_email}}</span></li>
    <li><span class="var-label"><?php _e('Phone:', 'latepoint'); ?></span> <span class="var-code os-click-to-copy">{{customer_phone}}</span></li>
    <li><span class="var-label"><?php _e('Comments:', 'latepoint'); ?></span> <span class="var-code os-click-to-copy">{{customer_notes}}</span></li>
    <li><span class="var-label"><?php _e('Password Reset Token:', 'latepoint'); ?></span> <span class="var-code os-click-to-copy">{{token}}</span></li>
    <?php do_action('latepoint_available_vars_customer'); ?>
  </ul>
</div>
<div class="available-vars-block">
  <h4><?php _e('Agent', 'latepoint'); ?></h4>
  <ul>
    <li><span class="var-label"><?php _e('First Name:', 'latepoint'); ?></span> <span class="var-code os-click-to-copy">{{agent_first_name}}</span></li>
    <li><span class="var-label"><?php _e('Last Name:', 'latepoint'); ?></span> <span class="var-code os-click-to-copy">{{agent_last_name}}</span></li>
    <li><span class="var-label"><?php _e('Full Name:', 'latepoint'); ?></span> <span class="var-code os-click-to-copy">{{agent_full_name}}</span></li>
    <li><span class="var-label"><?php _e('Display Name:', 'latepoint'); ?></span> <span class="var-code os-click-to-copy">{{agent_display_name}}</span></li>
    <li><span class="var-label"><?php _e('Email:', 'latepoint'); ?></span> <span class="var-code os-click-to-copy">{{agent_email}}</span></li>
    <li><span class="var-label"><?php _e('Phone:', 'latepoint'); ?></span> <span class="var-code os-click-to-copy">{{agent_phone}}</span></li>
    <li><span class="var-label"><?php _e('Additional Emails:', 'latepoint'); ?></span> <span class="var-code os-click-to-copy">{{agent_additional_emails}}</span></li>
    <li><span class="var-label"><?php _e('Additional Phone Numbers:', 'latepoint'); ?></span> <span class="var-code os-click-to-copy">{{agent_additional_phones}}</span></li>
  </ul>
</div>
<div class="available-vars-block">
  <h4><?php _e('Location', 'latepoint'); ?></h4>
  <ul>
    <li><span class="var-label"><?php _e('Name:', 'latepoint'); ?></span> <span class="var-code os-click-to-copy">{{location_name}}</span></li>
    <li><span class="var-label"><?php _e('Full Address:', 'latepoint'); ?></span> <span class="var-code os-click-to-copy">{{location_full_address}}</span></li>
  </ul>
</div>
<div class="available-vars-block">
  <h4><?php _e('Transaction', 'latepoint'); ?></h4>
  <ul>
		<li><span class="var-label"><?php _e('Token:', 'latepoint'); ?></span> <span class="var-code os-click-to-copy"> {{transaction_token}}</span></li>
		<li><span class="var-label"><?php _e('Amount:', 'latepoint'); ?></span> <span class="var-code os-click-to-copy"> {{transaction_amount}}</span></li>
		<li><span class="var-label"><?php _e('Processor:', 'latepoint'); ?></span> <span class="var-code os-click-to-copy"> {{transaction_processor}}</span></li>
		<li><span class="var-label"><?php _e('Payment Method:', 'latepoint'); ?></span> <span class="var-code os-click-to-copy"> {{transaction_payment_method}}</span></li>
		<li><span class="var-label"><?php _e('Funds Status:', 'latepoint'); ?></span> <span class="var-code os-click-to-copy"> {{transaction_funds_status}}</span></li>
		<li><span class="var-label"><?php _e('Status:', 'latepoint'); ?></span> <span class="var-code os-click-to-copy"> {{transaction_status}}</span></li>
		<li><span class="var-label"><?php _e('Notes:', 'latepoint'); ?></span> <span class="var-code os-click-to-copy"> {{transaction_notes}}</span></li>
		<li><span class="var-label"><?php _e('Payment Portion:', 'latepoint'); ?></span> <span class="var-code os-click-to-copy">{{transaction_payment_portion}}</span></li>
    <?php do_action('latepoint_available_vars_transaction'); ?>
  </ul>
</div>
<?php include('_business_variables.php'); ?>
<?php do_action('latepoint_available_vars_after'); ?>