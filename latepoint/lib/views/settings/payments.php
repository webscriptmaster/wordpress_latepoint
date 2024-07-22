<div class="latepoint-settings-w os-form-w">
  <form action="" data-os-action="<?php echo OsRouterHelper::build_route_name('settings', 'update'); ?>">
    <?php if(count($payment_processors)){ ?>
      <div class="os-section-header"><h3><?php _e('Payment Processors', 'latepoint'); ?></h3></div>
        <div class="os-togglable-items-w">
        <?php foreach($payment_processors as $payment_processor_code => $payment_processor){ ?>
          <div class="os-togglable-item-w" id="paymentProcessorToggler_<?php echo $payment_processor['code']; ?>">
            <div class="os-togglable-item-head">
              <div class="os-toggler-w">
                <?php echo OsFormHelper::toggler_field('settings[enable_payment_processor_'.$payment_processor_code.']', false, OsPaymentsHelper::is_payment_processor_enabled($payment_processor_code), 'togglePaymentSettings_'.$payment_processor_code, 'large'); ?>
              </div>
	            <?php if(!empty($payment_processor['image_url'])) echo '<img class="os-togglable-item-logo-img" src="'.$payment_processor['image_url'].'"/>'; ?>
              <div class="os-togglable-item-name"><?php echo $payment_processor['name']; ?></div>
            </div>
            <div class="os-togglable-item-body" style="<?php echo OsPaymentsHelper::is_payment_processor_enabled($payment_processor_code) ? '' : 'display: none'; ?>" id="togglePaymentSettings_<?php echo $payment_processor_code; ?>">
              <?php do_action('latepoint_payment_processor_settings', $payment_processor_code); ?>
            </div>
          </div>
        <?php } ?>
        </div>

        <div class="os-section-header"><h3><?php _e('Other Settings', 'latepoint'); ?></h3></div>
		    <div class="white-box">
		      <div class="white-box-header">
		        <div class="os-form-sub-header"><h3><?php _e('Payment Settings', 'latepoint'); ?></h3></div>
		      </div>
		      <div class="white-box-content no-padding">
		        <div class="sub-section-row">
		          <div class="sub-section-label">
		            <h3><?php _e('Environment', 'latepoint') ?></h3>
		          </div>
		          <div class="sub-section-content">
				        <?php echo OsFormHelper::select_field('settings[payments_environment]', false, array(LATEPOINT_ENV_LIVE => __('Live (Production)', 'latepoint'), LATEPOINT_ENV_DEV => __('Testing (Development)', 'latepoint'), LATEPOINT_ENV_DEMO => __('Demo', 'latepoint')), OsSettingsHelper::get_payments_environment()); ?>
		          </div>
		        </div>

		        <div class="sub-section-row">
		          <div class="sub-section-label">
		            <h3><?php _e('Local Payments', 'latepoint') ?></h3>
		          </div>
		          <div class="sub-section-content">
				        <?php echo OsFormHelper::toggler_field('settings[enable_payments_local]', __('Allow Paying Locally', 'latepoint'), OsPaymentsHelper::is_local_payments_enabled(), false, false, ['sub_label' => __('Show "Pay Later" payment option', 'latepoint')]); ?>
		          </div>
		        </div>
		      </div>
		    </div>
      <?php }else{ ?>
        <a href="<?php echo OsRouterHelper::build_link(['addons', 'index']); ?>" class="os-add-box" >
          <div class="add-box-graphic-w"><div class="add-box-plus"><i class="latepoint-icon latepoint-icon-plus4"></i></div></div>
          <div class="add-box-label"><?php _e('Install Payment Gateway Add-on', 'latepoint'); ?></div>
        </a><?php
      } ?>
    <div class="os-form-buttons">
      <?php echo OsFormHelper::button('submit', __('Save Settings', 'latepoint'), 'submit', ['class' => 'latepoint-btn']); ?>
    </div>
  </form>
</div>