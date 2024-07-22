<?php if($customers){
	foreach($customers as $customer){ ?>
    <div class="customer-option" data-os-params="<?php echo OsUtilHelper::build_os_params(['customer_id' => $customer->id]); ?>"
        data-os-after-call="latepoint_quick_booking_customer_selected"
        data-os-output-target=".customer-quick-edit-form-w" 
        data-os-action="<?php echo OsRouterHelper::build_route_name('bookings', 'customer_quick_edit_form'); ?>">
      <div class="customer-option-avatar" style="background-image: url(<?php echo OsCustomerHelper::get_avatar_url($customer); ?>)"></div>
      <div class="customer-option-info">
        <h4 class="customer-option-info-name"><span><?php echo preg_replace("/($query)/i", "<span class='os-query-match'>$1</span>", $customer->full_name); ?></span></h4>
        <ul>
          <li>
            <?php _e('Email: ','latepoint'); ?>
            <strong><?php echo preg_replace("/($query)/i", "<span class='os-query-match'>$1</span>", $customer->email); ?></strong>
          </li>
          <li>
            <?php _e('Phone: ','latepoint'); ?>
            <strong><?php echo preg_replace("/($query)/i", "<span class='os-query-match'>$1</span>", $customer->phone); ?></strong>
          </li>
        </ul>
      </div>
    </div> 
    <?php
	}
}else{
	echo '<div class="os-no-matched-customers">'.__('No matches found.', 'latepoint').'</div>';
} ?>