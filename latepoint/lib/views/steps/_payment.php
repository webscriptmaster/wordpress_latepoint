<div class="step-payment-w latepoint-step-content" 
  data-full-amount="<?php echo $booking->full_amount_to_charge(); ?>" 
  data-sub-step="<?php echo $payment_sub_step; ?>" 
  data-default-portion="<?php echo OsBookingHelper::get_default_payment_portion_type($booking); ?>"
  data-step-name="payment">

  <?php do_action('latepoint_payment_step_content', $booking, $enabled_payment_times); ?>
  <?php if(count($enabled_payment_times) > 1){ ?>
    <div class="lp-payment-times-w">
      <div class="latepoint-step-content-text-centered">
        <h4><?php _e('When would you like to pay for the service?', 'latepoint'); ?></h4>
        <div><?php _e('You can either pay now or pay locally on arrival. You will be able to select payment method in the next step.', 'latepoint'); ?></div>
      </div>
      <div class="lp-options lp-options-grid lp-options-grid-three">
        <?php foreach($enabled_payment_times as $pay_time_name => $pay_time_methods){
          $option = reset($pay_time_methods);
          $option['label'] = ($pay_time_name == 'now') ? __('Pay Now', 'latepoint') : __('Pay Later', 'latepoint');
          $option['image_url'] = ($pay_time_name == 'now') ? LATEPOINT_IMAGES_URL.'payment_now.png' : LATEPOINT_IMAGES_URL.'payment_later.png';
          if(count($pay_time_methods) > 1){
            // if more than one payment methods for this pay time - show them on selection
            $option['css_class'] = 'lp-payment-trigger-payment-time-selector';
            $option['attrs'] = 'data-pay-time="'.$pay_time_name.'"';
          }else{
            // one payment method only available - trigger it
            $option['css_class'] = isset($option['css_class']) ? $option['css_class'] : 'lp-payment-trigger-payment-method-selector';
            $option['attrs'] = 'data-method="'.reset($pay_time_methods)['code'].'"';
          }
          echo OsStepsHelper::output_list_option($option);
        } ?>
      </div>
    </div>
  <?php } ?>
  
  <?php foreach($enabled_payment_times as $pay_time_name => $pay_time_methods){ 
    if(count($pay_time_methods) > 1){ ?>
      <div class="lp-payment-methods-w" data-methods-time="<?php echo $pay_time_name; ?>">
        <div class="latepoint-step-content-text-centered">
          <h4><?php _e('How would you like to make a payment?', 'latepoint'); ?></h4>
          <div><?php _e('You can select payment method from the list below.', 'latepoint'); ?></div>
        </div>
        <div class="lp-options lp-options-grid lp-options-grid-three">
          <?php foreach($pay_time_methods as $pay_method_code => $pay_method){ ?>
            <?php 
            $pay_method['css_class'] = isset($pay_method['css_class']) ? $pay_method['css_class'] : 'lp-payment-trigger-payment-method-selector';
            $pay_method['attrs'] = isset($pay_method['attrs']) ? $pay_method['attrs'] : ' data-method="'.$pay_method_code.'" ';
            echo OsStepsHelper::output_list_option($pay_method); ?>
          <?php } ?>
        </div>
      </div>
    <?php } ?>
  <?php } ?>

  <?php if($booking->can_pay_deposit_and_pay_full()){ ?>
    <div class="lp-payment-portions-w">
      <div class="latepoint-step-content-text-centered">
        <h4><?php _e('How much do you want to pay now?', 'latepoint'); ?></h4>
        <div><?php _e('You can either make a full payment now or just leave a deposit and pay the rest after.', 'latepoint'); ?></div>
      </div>
      <div class="lp-options lp-options-grid lp-options-grid-two">
        <div class="lp-option lp-payment-trigger-payment-portion-selector" data-portion="<?php echo LATEPOINT_PAYMENT_PORTION_FULL; ?>">
          <div class="lp-option-amount-w">
            <div class="lp-option-amount lp-amount-full"><div class="lp-amount-value"><?php echo $booking->formatted_full_price() ?></div></div>
          </div>
          <div class="lp-option-label"><?php _e('Full Amount', 'latepoint'); ?></div>
        </div>
        <div class="lp-option lp-payment-trigger-payment-portion-selector" data-portion="<?php echo LATEPOINT_PAYMENT_PORTION_DEPOSIT; ?>">
          <div class="lp-option-amount-w">
            <div class="lp-option-amount lp-amount-deposit"><div class="lp-slice"></div><div class="lp-amount-value"><?php echo $booking->formatted_deposit_price() ?></div></div>
          </div>
          <div class="lp-option-label"><?php _e('Deposit Only', 'latepoint'); ?></div>
        </div>
      </div>
    </div>
  <?php } ?>
  <?php echo OsBookingHelper::get_payment_total_info_html($booking); ?>
  <?php
    if(!OsPaymentsHelper::get_default_payment_method()) echo OsFormHelper::hidden_field('booking[payment_method]', $booking->payment_method, [ 'class' => 'latepoint_payment_method', 'skip_id' => true]);
    echo OsFormHelper::hidden_field('booking[payment_portion]', $booking->payment_portion ? $booking->payment_portion : OsBookingHelper::get_default_payment_portion_type($booking), [ 'class' => 'latepoint_payment_portion', 'skip_id' => true]);
  ?>
</div>