<div style="font-size: 16px; margin-bottom: 20px; line-height: 1.6;">
	Hi {{customer_full_name}},
	<br>
	<br>
	Your {{service_name}} appointment with <strong>{{agent_full_name}}</strong> is <strong>{{booking_status}}</strong>.
</div>
<h4 style="margin-bottom: 10px; margin-top: 0px; font-size: 16px; font-weight: bold;">Appointment Details:</h4>
<ul>
	<li>
		<span>Agent:</span> <strong>{{agent_full_name}}</strong>
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