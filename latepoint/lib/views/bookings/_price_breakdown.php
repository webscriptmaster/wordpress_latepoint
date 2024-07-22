<?php
/* @var $booking OsBookingModel */
/* @var $price_breakdown_rows array */
do_action('latepoint_booking_quick_form_price_before_subtotal', $booking);
foreach($price_breakdown_rows['before_subtotal'] as $row){
	OsBookingHelper::output_price_breakdown_row_as_input_field($row, 'price_breakdown[before_subtotal]');
}
echo OsFormHelper::money_field('booking[subtotal]', __('Sub Total', 'latepoint'), $booking->subtotal, ['theme' => 'right-aligned'], [], ['class' => 'os-subtotal']);
foreach($price_breakdown_rows['after_subtotal'] as $row){
	OsBookingHelper::output_price_breakdown_row_as_input_field($row, 'price_breakdown[after_subtotal]');
}

echo OsFormHelper::money_field('booking[price]', __('Total Price', 'latepoint'), $booking->price, ['theme' => 'right-aligned', 'class' => 'os-affects-balance'], [], ['class' => 'os-total']);