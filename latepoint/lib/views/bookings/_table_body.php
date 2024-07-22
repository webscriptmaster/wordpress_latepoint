<?php
/* @var $bookings OsBookingModel[] */
/* @var $services_list array */
/* @var $locations_list array */
/* @var $agents_list array */
/* @var $selected_columns array */
?>

<?php
if($bookings){
  foreach ($bookings as $booking): ?>
    <tr class="os-clickable-row" <?php echo OsBookingHelper::quick_booking_btn_html($booking->id); ?>>
      <td class="os-column-faded text-right has-floating-button">
        <?php echo $booking->id; ?>
        <div class="os-floating-button"><i class="latepoint-icon latepoint-icon-edit-3"></i></div>
      </td>
      <?php if(count($services_list) > 1){ ?>
      <td>
        <div class="os-with-service-color">
          <span class="cell-link-content">
            <span class="os-column-service-color" style="background-color: <?php echo $booking->service->bg_color; ?>"></span> 
            <span><?php echo $booking->service->name; ?></span>
          </span>
        </div>
      </td>
      <?php } ?>
      <td><strong><?php echo $booking->nice_start_date; ?></strong> <span class="os-dot"></span> <span><?php echo $booking->nice_start_time; ?></span></td>
      <td><span class="in-table-time-left">
	      <?php
	      switch($booking->time_status()){
		      case 'upcoming':
						echo $booking->time_left;
						break;
		      case 'now':
						echo '<span class="time-left is-now">'.__('Now', 'latepoint').'</span>';
						break;
		      case 'past':
						echo '<span class="time-left is-past">'.__('Past', 'latepoint').'</span>';
						break;
	      }
				?>
	      </span>
      </td>
	      <?php if(count($agents_list) > 1){ ?>
      <td>
        <div class="os-with-avatar">
          <span class="cell-link-content">
            <span class="os-avatar" style="background-image: url(<?php echo $booking->agent->get_avatar_url(); ?>)"></span>
            <span class="os-name"><?php echo $booking->agent->full_name; ?></span>
          </span>
          <div class="os-clickable-popup-trigger"
               data-route="<?php echo OsRouterHelper::build_route_name('agents', 'mini_profile'); ?>"
               data-os-params="<?php echo OsUtilHelper::build_os_params(['agent_id' => $booking->agent_id, 'booking_id' => $booking->id]) ?>">
	          <i class="latepoint-icon latepoint-icon-more-horizontal"></i>
          </div>
        </div>
      </td>
			  <?php } ?>
      <?php if(count($locations_list) > 1){ ?>
        <td><?php echo $booking->location->name; ?></td>
      <?php } ?>
      <td>
        <div class="os-with-avatar">
          <span class="cell-link-content">
            <span class="os-avatar" style="background-image: url(<?php echo $booking->customer->get_avatar_url(); ?>)"></span>
            <span class="os-name"><?php echo $booking->customer->full_name;  ?></span>
          </span>
          <div class="os-clickable-popup-trigger" data-route="<?php echo OsRouterHelper::build_route_name('customers', 'mini_profile'); ?>" data-os-params="<?php echo OsUtilHelper::build_os_params(['customer_id' => $booking->customer_id, 'booking_id' => $booking->id]) ?>">
	          <i class="latepoint-icon latepoint-icon-more-horizontal"></i>
          </div>
        </div>
      </td>
      <td><span class="os-column-status os-column-status-<?php echo $booking->status; ?>"><?php echo $booking->nice_status; ?></span></td>
      <td><span class="os-column-status os-column-status-<?php echo $booking->payment_status; ?>"><?php echo $booking->nice_payment_status; ?></span></td>
      <td><?php echo $booking->nice_created_at; ?></td>
      <?php 
      $customer = $booking->customer;
      foreach($selected_columns as $column_type => $columns){ 
        foreach($columns as $column_key){ 
          if(isset($available_columns[$column_type][$column_key])){
            if(property_exists($$column_type, $column_key) || method_exists($$column_type,"get_".$column_key)){
              echo '<td>'.$$column_type->$column_key.'</td>';
            }else{
              echo '<td>'.$$column_type->get_meta_by_key($column_key).'</td>';
            }
          }
        }
      } ?>
    </tr>
    <?php 
  endforeach; 
}?>