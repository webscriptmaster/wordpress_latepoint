Hi {{customer_full_name}},
<br/><br/>
Booking <strong>#{{booking_code}}</strong> was updated
<h4 style="margin-bottom: 10px; margin-top: 0px; font-size: 16px; font-weight: bold;">Appointment Information</h4>
<ul>
	<li>
		<span>Status:</span> <strong>{{booking_status}}</strong>
	</li>
	<li>
		<span>Service:</span> <strong>{{service_name}}</strong>
	</li>
	<li>
		<span>Date, Time:</span> <strong>{{start_date}}, {{start_time}} - {{end_time}}</strong>
	</li>
</ul>
<div style="margin-top: 25px;">
	<a href="{{manage_booking_url_customer}}" style="display: block; text-decoration: none; padding: 10px; border-radius: 6px; text-align: center; font-size: 18px; color: #fff; background-color: #2652E4; font-weight: 700;">Manage This Appointment</a>
</div>