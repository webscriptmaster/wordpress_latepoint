<?php
/*
 * Copyright (c) 2023 LatePoint LLC. All rights reserved.
 */

/* @var $transactions OsTransactionModel[] */
/* @var $customer_name_query string */
?>
<?php
if ($transactions) {
	foreach ($transactions as $transaction) { ?>
		<tr>
			<td class="text-center os-column-faded"><?php echo $transaction->id; ?></td>
			<td><?php echo $transaction->token; ?></td>
			<td>
				<?php if ($transaction->booking_id) { ?>
					<?php echo '<a href="#" class="in-table-link" ' . OsBookingHelper::quick_booking_btn_html($transaction->booking_id) . '>' . $transaction->booking->id . '</a>'; ?>
				<?php } else {
					echo 'n/a';
				} ?>
			</td>
			<td>

				<?php if ($transaction->customer_id) { ?>

        <a class="os-with-avatar" target="_blank" href="<?php echo OsRouterHelper::build_link(OsRouterHelper::build_route_name('customers', 'edit_form'), array('id' => $transaction->customer->id) ) ?>">
          <span class="os-avatar" style="background-image: url(<?php echo $transaction->customer->get_avatar_url(); ?>)"></span>
          <span class="os-name"><?php echo $transaction->customer->full_name; ?></span>
	        <i class="latepoint-icon latepoint-icon-external-link"></i>
        </a>
				<?php } else {
					echo 'n/a';
				} ?>
			</td>
			<td>
				<div class="lp-processor-logo lp-processor-logo-<?php echo $transaction->processor; ?>"><?php echo $transaction->processor; ?></div>
			</td>
			<td>
				<div class="lp-method-logo lp-method-logo-<?php echo $transaction->payment_method; ?>"><?php echo $transaction->payment_method; ?></div>
			</td>
			<td><?php echo OsMoneyHelper::format_price($transaction->amount, true, false); ?></td>
			<td><span class="lp-transaction-status lp-transaction-status-<?php echo $transaction->status; ?>"><?php echo $transaction->status; ?></span>
			<td><span class="lp-transaction-status lp-transaction-funds-status-<?php echo $transaction->funds_status; ?>"><?php echo $transaction->funds_status; ?></span>
			</td>
			<td><?php echo $transaction->created_at; ?></td>
		</tr>
		<?php
	}
} ?>