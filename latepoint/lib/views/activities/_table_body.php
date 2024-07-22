<?php
/* @var $activities OsActivityModel[] */
?>
<?php
if ($activities) {
	foreach ($activities as $activity) { ?>
		<tr class="activity-type-<?php echo $activity->code; ?>">
			<td class="activity-column-name"><div><?php echo $activity->name; ?></div></td>
			<td><?php echo $activity->user_link_with_avatar; ?></td>
			<td><?php echo $activity->nice_created_at; ?></td>
			<td><?php echo $activity->link_to_object; ?></td>
		</tr>
		<?php
	}
} ?>