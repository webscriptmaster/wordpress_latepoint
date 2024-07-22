<?php
/*
 * Copyright (c) 2022 LatePoint LLC. All rights reserved.
 */

/* @var $transaction OsTransactionModel */
?>
<div class="quick-add-transaction-box-w">
  <div class="quick-add-transaction-box">
	  <?php if($transaction->is_new_record()){ ?>
		  <a href="#" class="trigger-remove-transaction-btn form-close-btn"><i class="latepoint-icon latepoint-icon-x"></i></a>
	  <?php }else{ ?>
	    <a href="#" data-os-prompt="<?php _e('Are you sure you want to delete this transaction?', 'latepoint'); ?>"
	       data-os-after-call="latepoint_transaction_removed"
	       data-os-pass-this="yes"
	       data-os-action="<?php echo OsRouterHelper::build_route_name('transactions', 'destroy'); ?>"
	       data-os-params="<?php echo OsUtilHelper::build_os_params(['id' => $real_or_rand_id]) ?>"
	       class="form-close-btn"><i class="latepoint-icon latepoint-icon-trash-2"></i></a>
	  <?php } ?>
    <h3><?php echo ($transaction->is_new_record() ? __('New Transaction', 'latepoint') : __('Edit Transaction', 'latepoint')); ?></h3>
    <div class="os-row">
      <div class="os-col-lg-6">
        <?php echo OsFormHelper::money_field('transactions['.$real_or_rand_id.'][amount]', __('Amount', 'latepoint'), $transaction->amount, ['placeholder' => __('Amount', 'latepoint')] ); ?>
      </div>
      <div class="os-col-lg-6">
        <?php echo OsFormHelper::text_field('transactions['.$real_or_rand_id.'][created_at]', __('Date', 'latepoint'), $transaction->formatted_created_date('Y-m-d', OsTimeHelper::today_date()), ['placeholder' => __('Date', 'latepoint')]); ?>
      </div>
    </div>
    <div class="os-row">
      <div class="os-col-12">
        <?php echo OsFormHelper::text_field('transactions['.$real_or_rand_id.'][token]', __('Confirmation Number', 'latepoint'), $transaction->token, ['placeholder' => __('Confirmation Code', 'latepoint')] ); ?>
      </div>
    </div>
    <div class="os-row">
      <div class="os-col-6">
        <?php echo OsFormHelper::select_field('transactions['.$real_or_rand_id.'][payment_portion]', __('Payment Portion', 'latepoint'), OsPaymentsHelper::get_payment_portions_list(), $transaction->payment_portion, false ); ?>
      </div>
      <div class="os-col-6">
        <?php echo OsFormHelper::select_field('transactions['.$real_or_rand_id.'][funds_status]', __('Funds Status', 'latepoint'), OsPaymentsHelper::get_funds_statuses_list(), $transaction->funds_status, false ); ?>
      </div>
    </div>
    <div class="os-row">
      <div class="os-col-lg-6">
        <?php echo OsFormHelper::select_field('transactions['.$real_or_rand_id.'][processor]',__('Processor', 'latepoint'), OsPaymentsHelper::get_payment_processors_for_select(false, true), $transaction->processor , false); ?>
      </div>
      <div class="os-col-lg-6">
        <?php echo OsFormHelper::select_field('transactions['.$real_or_rand_id.'][payment_method]',__('Method', 'latepoint'), OsPaymentsHelper::get_all_payment_methods_for_select(true), $transaction->payment_method, false); ?>
      </div>
    </div>
    <div class="os-row">
      <div class="os-col-lg-12">
        <?php echo OsFormHelper::textarea_field('transactions['.$real_or_rand_id.'][notes]',__('Notes', 'latepoint') , $transaction->notes, ['theme' => 'bordered']); ?>
      </div>
    </div>
	  <?php echo OsFormHelper::hidden_field('transactions['.$real_or_rand_id.'][id]', $real_or_rand_id); ?>
	  <?php do_action( 'latepoint_transaction_edit_form_after', $transaction, $real_or_rand_id ); ?>
  </div>
</div>