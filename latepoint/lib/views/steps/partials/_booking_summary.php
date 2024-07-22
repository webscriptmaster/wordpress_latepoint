<?php
/* @var $booking OsBookingModel */
?>
<div class="booking-summary-info-w">
	<?php if($booking->service_id){ ?>
	<div class="summary-box main-box">
	  <?php
	  $service_headings = [];
		if($booking->duration) $service_headings[] = OsServiceHelper::get_summary_duration_label($booking->duration);
		$service_headings = apply_filters('latepoint_booking_summary_service_headings', $service_headings, $booking);
		if($service_headings){
			echo '<div class="summary-box-heading">';
				foreach($service_headings as $heading){
					echo '<div class="sbh-item">'.$heading.'</div>';
				}
				echo '<div class="sbh-line"></div>';
			echo '</div>';
		}
	  ?>
	  <div class="summary-box-content">
		  <div class="sbc-big-item"><?php echo $booking->service->name; ?></div>
		  <div class="sbc-highlighted-item">
			  <?php
			  if($booking->start_date){
				  $booking_start_datetime = $booking->get_nice_start_datetime();
				  $booking_start_datetime = apply_filters('latepoint_booking_summary_formatted_booking_start_datetime', $booking_start_datetime, $booking, $timezone_name ?? '');
					echo $booking_start_datetime;
			  }
				?>
		  </div>
	  </div>

	  <?php
	  $service_attributes = [];
		$service_attributes = apply_filters('latepoint_booking_summary_service_attributes', $service_attributes, $booking);
		if($service_attributes){
			echo '<div class="summary-attributes sa-clean">';
			foreach($service_attributes as $attribute){
				echo '<span>'.$attribute['label'].': <strong>'.$attribute['value'].'</strong></span>';
			}
			echo '</div>';
		}
	  ?>
	</div>
	<?php } ?>
	<?php if(OsSettingsHelper::is_off('steps_hide_agent_info')) echo '<div class="summary-boxes-columns">'; ?>
		<?php if($booking->customer_id){ ?>
		  <div class="summary-box summary-box-customer-info">
				<div class="summary-box-heading">
					<div class="sbh-item"><?php _e('Customer', 'latepoint') ?></div>
					<div class="sbh-line"></div>
				</div>
			  <div class="summary-box-content with-media">
				  <div class="os-avatar-w">
					  <div class="os-avatar"><span><?php echo $booking->customer->get_initials(); ?></span></div>
				  </div>
				  <div class="sbc-content-i">
					  <div class="sbc-main-item"><?php echo $booking->customer->full_name; ?></div>
					  <div class="sbc-sub-item"><?php echo $booking->customer->email; ?></div>
				  </div>
			  </div>
			  <?php
			  $customer_attributes = [];
				$customer_attributes = apply_filters('latepoint_booking_summary_customer_attributes', $customer_attributes, $booking);
				if($customer_attributes){
					echo '<div class="summary-attributes sa-clean sa-hidden">';
					foreach($customer_attributes as $attribute){
						echo '<span>'.$attribute['label'].': <strong>'.$attribute['value'].'</strong></span>';
					}
					echo '</div>';
				}
			  ?>
		  </div>
		<?php } ?>
	  <?php if(OsSettingsHelper::is_off('steps_hide_agent_info') && $booking->agent_id && $booking->agent_id != LATEPOINT_ANY_AGENT){ ?>
		  <div class="summary-box summary-box-agent-info">
				<div class="summary-box-heading">
					<div class="sbh-item"><?php _e('Agent', 'latepoint') ?></div>
					<div class="sbh-line"></div>
				</div>
			  <div class="summary-box-content with-media">
				  <div class="os-avatar-w" style="background-image: url(<?php echo ($booking->agent->avatar_image_id) ? $booking->agent->get_avatar_url() : ''; ?>)">
					  <?php if(!$booking->agent->avatar_image_id) echo '<div class="os-avatar"><span>'.$booking->agent->get_initials().'</span></div>'; ?>
				  </div>
				  <div class="sbc-content-i">
					  <div class="sbc-main-item"><?php echo $booking->agent->full_name; ?></div>
					  <?php
					  if(OsSettingsHelper::steps_show_agent_bio()){
							echo '<div class="os-trigger-item-details-popup sbc-link-item" data-item-details-popup-id="osItemDetailsPopupAgent_'.$booking->agent_id.'">'.__('Learn More', 'latepoint').'</div>';
							OsAgentHelper::generate_bio($booking->agent);
					  }
						?>
				  </div>
			  </div>
		  </div>
	  <?php } ?>
	<?php if(OsSettingsHelper::is_off('steps_hide_agent_info')) echo '</div>'; ?>
	<?php do_action('latepoint_booking_summary_before_price_breakdown', $booking); ?>
</div>
<?php if($booking->service_id && !OsBookingHelper::is_breakdown_free($price_breakdown_rows)){ ?>
	<div class="price-breakdown-w">
	  <div class="pb-heading"><?php _e('Cost Breakdown', 'latepoint'); ?></div>
		<?php
			$payment_attributes = [];
			if(OsPaymentsHelper::is_accepting_payments()){
		    // payment gateways/methods exist
				if($booking->payment_method_nice_name) $payment_attributes[] = ['label' => __('Payment Method', 'latepoint'), 'value' => $booking->payment_method_nice_name];
				if($booking->payment_method != LATEPOINT_PAYMENT_METHOD_LOCAL){
					if($booking->payment_portion == LATEPOINT_PAYMENT_PORTION_DEPOSIT){
						$payment_attributes[] = ['label' => __('Type', 'latepoint'), 'value' => __('Deposit', 'latepoint')];
						$payment_attributes[] = ['label' => __('Amount', 'latepoint'), 'value' => $booking->formatted_deposit_price()];
					}else{
						$payment_attributes[] = ['label' => __('Amount', 'latepoint'), 'value' => $booking->formatted_full_price()];
					}
				}
		  }
			$payment_attributes = apply_filters('latepoint_booking_summary_payment_attributes', $payment_attributes, $booking);
			if($payment_attributes){
				echo '<div class="summary-attributes">';
				foreach($payment_attributes as $attribute){
					echo '<span>'.$attribute['label'].': <strong>'.$attribute['value'].'</strong></span>';
				}
				echo '</div>';
			}
			OsBookingHelper::output_price_breakdown($price_breakdown_rows);
		?>
	</div>
<?php } ?>